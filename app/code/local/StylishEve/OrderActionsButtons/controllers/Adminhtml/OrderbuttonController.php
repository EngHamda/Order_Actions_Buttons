<?php

class StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('orderactionsbuttons/orderbutton');
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("orderactionsbuttons/orderbutton")->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderbutton  Manager"), Mage::helper("adminhtml")->__("Orderbutton Manager"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("OrderActionsButtons"));
        $this->_title($this->__("Manager Orderbutton"));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Note:
     *  - using json instead of serialization
     *      ** because unserialize fn. return error "PHP Notice:  unserialize(): Error at offset 5 of 178 bytes" in grid page
     *      ** another solution use "base64_encode"
     *          in case serialization => $pButtonData['report_attrs'] = base64_encode(serialize($pButtonData['report_attrs']));
     *          in case unserialization => $eportAttrsArray = unserialize(base64_decode($eportAttrs));
     *
     */
    public function editAction()
    {
        $this->_title($this->__("OrderActionsButtons"));
        $this->_title($this->__("Orderbutton"));
        $this->_title($this->__("Edit Item"));

        if(!Mage::helper('core')->isModuleEnabled('Mirasvit_Helpdesk')){
            Mage::getSingleton("adminhtml/session")->addError("Note: Mirasvit_Helpdesk Module is DISABLED");
        }
        if(!Mage::helper('core')->isModuleEnabled('MDN_AdvancedStock')){
            Mage::getSingleton("adminhtml/session")->addError("Note: MDN_AdvancedStock Module is DISABLED");

        }
        if(!Mage::helper('core')->isModuleEnabled('Stylisheve_Warehouserole')){
            Mage::getSingleton("adminhtml/session")->addError("Note: Stylisheve_Warehouserole Module is DISABLED");
        }

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("orderactionsbuttons/orderbutton")->load($id);
        if ($model->getId()) {
            if ($model->getActionType() == StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray()['Generate Report'] && $eportAttrs = $model->getReportAttrs()) {
                $eportAttrsArray = json_decode($eportAttrs, true);
                $model->setReportAttrs($eportAttrsArray);
            }
            Mage::register("orderbutton_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("orderactionsbuttons/orderbutton");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderbutton Manager"), Mage::helper("adminhtml")->__("Orderbutton Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderbutton Description"), Mage::helper("adminhtml")->__("Orderbutton Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("orderactionsbuttons/adminhtml_orderbutton_edit"))->_addLeft($this->getLayout()->createBlock("orderactionsbuttons/adminhtml_orderbutton_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("orderactionsbuttons")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {
        $this->_title($this->__("OrderActionsButtons"));
        $this->_title($this->__("Orderbutton"));
        $this->_title($this->__("New Item"));

        if(!Mage::helper('core')->isModuleEnabled('Mirasvit_Helpdesk')){
            Mage::getSingleton("adminhtml/session")->addError("Note: Mirasvit_Helpdesk Module is DISABLED");
        }

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("orderactionsbuttons/orderbutton")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("orderbutton_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("orderactionsbuttons/orderbutton");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderbutton Manager"), Mage::helper("adminhtml")->__("Orderbutton Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderbutton Description"), Mage::helper("adminhtml")->__("Orderbutton Description"));


        $this->_addContent($this->getLayout()->createBlock("orderactionsbuttons/adminhtml_orderbutton_edit"))->_addLeft($this->getLayout()->createBlock("orderactionsbuttons/adminhtml_orderbutton_edit_tabs"));

        $this->renderLayout();

    }

    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();
        if ($post_data) {

            try {
                /**
                 *
                 * Note: for prepare data ("order_current_status", "accepted_role") before save in database
                 *
                 */
                $this->_prepareButtonDataBeforeSave($post_data);

                $model = Mage::getModel("orderactionsbuttons/orderbutton")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Orderbutton was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setOrderbuttonData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                    ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'saveAction']
                );
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setOrderbuttonData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }

        }
        $this->_redirect("*/*/");
    }

    /**
     *
     * @param Array $pButtonData
     */
    public function _prepareButtonDataBeforeSave(&$pButtonData)
    {
        $actionTypes = StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray();
        $pButtonData['order_current_status'] = implode(",", $pButtonData['order_current_status']);
        $pButtonData['accepted_role'] = implode(",", $pButtonData['accepted_role']);
        if(array_key_exists('order_removed_buttons', $pButtonData)){
            $pButtonData['order_removed_buttons'] = implode(",", $pButtonData['order_removed_buttons']);
            if(!array_key_exists('check_opening_tickets', $pButtonData)){
                $pButtonData['check_opening_tickets']= '0';
            }
        } else{
            if(array_key_exists('check_opening_tickets', $pButtonData)){
                unset($pButtonData['check_opening_tickets']);
            }
        }
        $pButtonData['check_warehouse'] = (array_key_exists('check_warehouse', $pButtonData))?$pButtonData['check_warehouse']:'0';
        $pButtonData['check_delivery_date'] = (array_key_exists('check_delivery_date', $pButtonData))?$pButtonData['check_delivery_date']:'0';

        if(in_array($pButtonData['action_type'],[$actionTypes['Generate Report & Change Status'], $actionTypes['Generate Report']])){
            $pButtonData['report_attrs'] = json_encode($pButtonData['report_attrs']);
        } else{
            if(array_key_exists('report_attrs', $pButtonData)){
                unset($pButtonData['report_attrs']);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = Mage::getModel("orderactionsbuttons/orderbutton");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                    ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'deleteAction']
                );
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }

    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("orderactionsbuttons/orderbutton");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'massRemoveAction']
            );
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'orderbutton.csv';
        $grid = $this->getLayout()->createBlock('orderactionsbuttons/adminhtml_orderbutton_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'orderbutton.xml';
        $grid = $this->getLayout()->createBlock('orderactionsbuttons/adminhtml_orderbutton_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     *  change order/s status
     *
     * - get order_id, order_tobe_status, order_current_status
     * - get order & change it status
     * - redirect to last page:ex. admin/sales_order/view/order_id/xxx
     */
    public function changeStatusAction()
    {
        try {
            $count = 1;
            $btnId = $this->getRequest()->getParam('button_id');
            $btnData = Mage::getSingleton('orderactionsbuttons/orderbutton')->load($btnId);
            $tobeStatus = $btnData->getOrderTobeStatus();
            $currentStatus = $btnData->getOrderCurrentStatus();
            $currentStatuses = explode(',', $currentStatus);
            $tobeStatusName = Mage::getSingleton('sales/order_status')->getCollection()
                ->addFieldToSelect('label')->addFieldToFilter('status', ['eq' => $tobeStatus])->getFirstItem()->getLabel();
            $_userInfo = $this->_getUserInfo();
            $userName = $_userInfo['userName'];
            $orderId = $this->getRequest()->getParam('order_id');
            $redirectUrl = 'adminhtml/sales_order/';
            $redirectData = [];
            if (!is_null($orderId)) {
                $redirectUrl = $redirectUrl . 'view';
                $redirectData['order_id'] = $orderId;
                $orderObj = Mage::getModel('sales/order')->load($orderId);
                $result = $this->_changeStatus($orderObj, $orderId, $userName, $currentStatuses, $tobeStatus, $tobeStatusName);
                if(!$result){
                    $this->_redirect($redirectUrl, $redirectData);
                    return ;
                }
            } else {
                $count = 0;
                $roleId = $_userInfo['roleId'];
                /* switch actions to do additional action before change orders status ex: generate report*/
                $_actions = StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray();
                switch ($btnData->getActionType()):
                    case $_actions['Generate Report & Change Status']:
                        /* do additional action "generate report" before change orders status*/
                        $_orders = Mage::getSingleton('sales/order')->getCollection();
                        $_orders->addFieldToFilter('status', ['in'=> $currentStatuses]);
                        $result = $this->_prepareSelectOrdersQueryForGenerateReport($_orders, $btnData, $roleId);
                        if(!$result){
                            $this->_redirect($redirectUrl, $redirectData);
                            return ;
                        }
                        if ($_orders->getSize() == 0) {
                            Mage::getSingleton("adminhtml/session")->addSuccess(
                                Mage::helper("adminhtml")->__("No Orders need to change status")
                            );
                            $this->_redirect($redirectUrl, $redirectData);
                            return ;
                        }
                        $result = $this->_getReport($_orders, $btnData);
                        if($result['error'] || !array_key_exists('reportNo', $result)){
                            $this->_redirect($redirectUrl, $redirectData);
                            return ;
                        }
                        $reportContent = $result['reportContent'];
                        $reportNo = $result['reportNo'];
                        $result = $this->_generateReport($reportContent);
                        if($result['error']){
                            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__($result['msg']));
                            $this->_redirect($redirectUrl, $redirectData);
                            return ;
                        }
                        $reportFileName = $result['filePath'];
                        $reportType = json_decode($btnData->getReportAttrs(),true)['report_type'];
                        //save report path to archive
                        $reportArchiveData = ['report_number'=>$reportNo, 'report_name'=> pathinfo($reportFileName)['basename'], 'report_type'=>$reportType];
                        $result = $this->_saveReportToArchive($reportArchiveData);
                        if($result['error']){
                            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__($result['msg']));
                            $this->_redirect($redirectUrl, $redirectData);
                            return ;
                        }
                        $result = $this->_updateReportIdInFile($reportNo);
                        if($result['error']){
                            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__($result['msg']));
                            $this->_redirect($redirectUrl, $redirectData);
                            return ;
                        }
                endswitch;
                $_orders = Mage::getSingleton('sales/order')->getCollection();
                $_orders->addFieldToFilter('status', ['in'=> $currentStatuses]);
                $result = $this->_prepareSelectOrdersQueryForChangeStatus($_orders, $btnData, $roleId);
                if(!$result){
                    $this->_redirect($redirectUrl, $redirectData);
                    return ;
                }
                if ($_orders->getSize() == 0) {
                    Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("No Orders need to change status")
                    );
                    $this->_redirect($redirectUrl, $redirectData);
                    return ;
                }
                foreach ($_orders as $_order) {
                    $orderId = $_order->getEntityId();
                    $result = $this->_changeStatus($_order, $userName, $orderId, $currentStatuses, $tobeStatus, $tobeStatusName);
                    if(!$result){
                        continue;
                    }
                    $count++;
                }//endForeach
                /* print report*/
                switch ($btnData->getActionType()):
                    case $_actions['Generate Report & Change Status']:
                        echo  $reportContent;
                        return ;
                    case $_actions['Change Status For Grid Page']:
                        break;
                endswitch;
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__($count." Order".(($count > 1) ? 's' : '')." Updated Successfully"));
            $this->_redirect($redirectUrl, $redirectData);
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Failed changing order/s status"));
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'changeStatusAction']
            );
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'changeStatusAction']
            );
        }
    }

    /**
     * get User Info
     *
     * @return Array
     */
    public function _getUserInfo()
    {
        $admin_user_session = Mage::getSingleton('admin/session');
        $userUsername = $admin_user_session->getUser()->getUsername();
        $userFirstname = $admin_user_session->getUser()->getFirstname();
        $userLastname = $admin_user_session->getUser()->getLastname();
        $_userId = $admin_user_session->getUser()->getUserId();
        $userInfo['userName'] = $userFirstname .' '. $userLastname . ' ('. $userUsername .')';
        $_userData = Mage::getSingleton('admin/user')->load($_userId);
        $userInfo['roleName'] = $_userData->getRole()->role_name;
        $userInfo['roleId'] = $_userData->getRole()->role_id;

        return $userInfo;
    }

    /**
     * ask msg
     *
     * @param Object $pOrderObj
     * @param String $pUserName
     * @param Int $pOrderId
     * @param Array $pCurrentStatuses
     * @param String $pTobeStatus
     * @param String $pTobeStatusName
     * @return Boolean
     */
    public function _changeStatus(&$pOrderObj, $pUserName, $pOrderId, $pCurrentStatuses, $pTobeStatus, $pTobeStatusName)
    {
        //check currentStatus
        if(!in_array($pOrderObj->getStatus(), $pCurrentStatuses)){
            Mage::getSingleton("adminhtml/session")->addError("Order Current Status is ".$pOrderObj->getStatus() );
            return false;
        }
        switch ($pTobeStatus):
            case "return_canceled":
                //TODO: check current status is ready_to_return {'Administrators', 'Storekeepers', 'China Operation' }
                if ($pOrderObj->canCancel()) {
                    $pOrderObj->cancel();
                    $pOrderObj->setStatus($pTobeStatus);
                    $pOrderObj->getStatusHistoryCollection(true);
                    $pOrderObj->save();
                }//endIF canCancel
                break;
            case "back_to_stock":
                $hasRma = $this->_checkRmaProducts($pOrderObj, $pOrderId);
                if(!$hasRma){
                    return false;
                }
                $this->_setTobeStatusAndAddComment($pOrderObj, $pTobeStatus, $pTobeStatusName, $pUserName);
                break;
            case "complete":
                //TODO: check current status is
                // (paid {'Administrators', 'Storekeepers'} {completecashAction}),
                // (pending_cfo_approval, pos {'Administrators', 'CFO', 'Accountant'})
                // (cash {'Administrators', 'CFO', 'Accountant'} {paidAction})
                $this->_handleInvoice($pOrderObj);
                if(in_array("cash", $pCurrentStatuses)) {
                    if (!$this->_checkOrderHasItemWithoutSerial($pOrderObj)) {
                        $this->_addShipmentToOrder($pOrderObj);
                    }//endIF canShip
                } elseif (
                    in_array('pending_cfo_approval', $pCurrentStatuses) ||
                    in_array('pos', $pCurrentStatuses) ||
                    in_array('paid', $pCurrentStatuses)
                ) {
                    if ($pOrderObj->canShip()) {
                        $this->_addShipmentToOrder($pOrderObj);
                    }//endIF canShip
                } else{
                    Mage::getSingleton("adminhtml/session")->addError("Order Current Status is $pOrderObj->getStatus(), Order Status Couldn't change to $pTobeStatusName");
                    return false;
                }
                break;
            default:
                $this->_setTobeStatusAndAddComment($pOrderObj, $pTobeStatus, $pTobeStatusName, $pUserName);
                break;
        endswitch;
        return true;
    }

    /**
     * check Rma Products
     */
    public function _checkRmaProducts(&$pOrderObj, $pOrderId)
    {
        $qtyOrdered = 0;
        $rmaRecords = mage::getModel('ProductReturn/Rma')->getCollection();
        $rmaRecords->addFieldToFilter('rma_order_id', $pOrderId);
        $rmaRecords->addFieldToFilter('rma_status', 'complete');
        $rmaRecords->getSelect()->reset(Zend_Db_Select::COLUMNS)->join(
            ['rp' => 'rma_products'],
            'rp.rp_rma_id = main_table.rma_id',
            ['back_to_stock_qty' => "SUM(rp.rp_qty)"]
        );
        $rmaRecords->addFieldToFilter('rp.rp_action',"backToStock");
        if($rmaRecords->count() == 0) {
            Mage::getSingleton("adminhtml/session")->addError($this->__('No RMA Created.'));
            return false;
        }
        $backToStockQty = intval($rmaRecords->getFirstItem()->getBackToStockQty());
        foreach ($pOrderObj->getAllItems() as $item) {
            if($item->getProductType() == "simple")
                $qtyOrdered += $item->getQtyOrdered();
        }
        if($qtyOrdered != $backToStockQty) {
            Mage::getSingleton("adminhtml/session")->addError($this->__('RMA\'s Quantity is not the same as quantity ordered.'));
            return false;
        } else{
            return true;
        }
    }

    /**
     *
     */
    public function _handleInvoice(&$pOrderObj)
    {
        if ($pOrderObj->canInvoice()) {
            //START Handle Invoice
            $invoice = Mage::getSingleton('sales/service_order', $pOrderObj)->prepareInvoice();
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(false);
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
            //END Handle Invoice
        }//endIF canInvoice
    }

    /**
     *
     */
    public function _checkOrderHasItemWithoutSerial(&$pOrderObj)
    {
        $orderHasItemWithoutSerial = false;
        foreach ($pOrderObj->getAllItems() as $item) {
            $orderItem = $item;
            $orderedQty = $orderItem->getQtyOrdered();
            $itemId = $orderItem->getItemId();
            $erpOrderItem = Mage::getSingleton('AdvancedStock/SalesFlatOrderItem')->load($itemId);
            //Mage::getSingleton('core/resource')->getTableName('AdvancedStock/SalesFlatOrderItem'),
            // TableName erp_sales_flat_order_item
            if( count($erpOrderItem->getData())==0 || empty($erpOrderItem->getSerials()) || substr_count($erpOrderItem->getSerials(), "\n")+1<$orderedQty ){
                $orderHasItemWithoutSerial = true;
                Mage::getSingleton("adminhtml/session")->addError(
                    Mage::helper("customization")->__("Cannot ship this order because the product %s has %s ordered quantity and error in serial.",$orderItem->getName(), $orderedQty)
                );
                //break;
            }//endIF
        }//end Foreach
        return $orderHasItemWithoutSerial;
    }

    /**
     *
     */
    public function _addShipmentToOrder(&$pOrderObj)
    {
        //START Handle Shipment
        $shipment = $pOrderObj->prepareShipment();
        $shipment->register();
        $pOrderObj->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();
        //END Handle Shipment
    }

    /**
     *
     */
    public function _setTobeStatusAndAddComment(&$pOrderObj, $pTobeStatus, $pTobeStatusName, $pUserName)
    {
        $pOrderObj->setStatus($pTobeStatus);
        $history = $pOrderObj->addStatusHistoryComment($pUserName . " has changed status to $pTobeStatusName.", false);
        $history->setIsCustomerNotified(false);
        $pOrderObj->save();
    }

    /**
     *
     */
    public function _prepareSelectOrdersQueryForChangeStatus(&$pOrdersModel, $pBtnData, $pRoleId)
    {
        $pOrdersModel->addFieldToSelect(['status', 'entity_id']);
        if($pBtnData->getCheckWarehouse()){
            if(!Mage::helper('core')->isModuleEnabled('MDN_AdvancedStock')){
                Mage::getSingleton("adminhtml/session")->addError("Note: MDN_AdvancedStock Module is DISABLED");
                return false;

            }
            if(!Mage::helper('core')->isModuleEnabled('Stylisheve_Warehouserole')){
                Mage::getSingleton("adminhtml/session")->addError("Note: Stylisheve_Warehouserole Module is DISABLED");
                return false;
            }
            //get orders depend on warehouses
            $pOrdersModel->getSelect()->join(
                ['sfoi' => 'sales_flat_order_item'], 'sfoi.order_id = `main_table`.entity_id', []
            )->join(
                ['esfoi' => 'erp_sales_flat_order_item'], 'esfoi.esfoi_item_id = sfoi.item_id', []
            )->join(
                ['swr' => 'stylisheve_warehouses_roles'], 'FIND_IN_SET(esfoi.preparation_warehouse, swr.warehouse_id)', []
            )->where("swr.role_id = $pRoleId");

            $pOrdersModel->getSelect()->group('main_table.entity_id');
        }
        if($pBtnData->getCheckDeliveryDate()){
            $pOrdersModel->getSelect()->join(
                ['aad' => 'amasty_amdeliverydate_deliverydate'], 'aad.order_id = `main_table`.entity_id', ['date']
            )->where("aad.date = '0000-00-00' OR CURDATE() >= aad.date OR CURDATE()+5 >= aad.date ");
        }
        return true;
    }

    /**
     *  generate Report
     *
     *
     */
    public function generateReportAction()
    {
        try {
            //userInfo
            $_userInfo = $this->_getUserInfo();
            $roleId = $_userInfo['roleId'];
            $btnId = $this->getRequest()->getParam('button_id');
            $btnData = Mage::getSingleton('orderactionsbuttons/orderbutton')->load($btnId);
            $currentStatus = $btnData->getOrderCurrentStatus();
            $currentStatuses = explode(",", $currentStatus);

            $_orders = Mage::getSingleton('sales/order')->getCollection();
            $_orders->addFieldToFilter('status', ['in'=> $currentStatuses]);

            $result = $this->_prepareSelectOrdersQueryForGenerateReport($_orders, $btnData, $roleId);
            if(!$result){
                $this->_redirect('adminhtml/sales_order/', []);
                return ;
            }
            if(0==$_orders->getSize()){
                Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("No orders exist for generate report"));
                $this->_redirect('adminhtml/sales_order/', []);
                return ;
            }
            $result = $this->_getReport($_orders, $btnData);
            if($result['error'] || !array_key_exists('reportNo', $result)){
                $this->_redirect('adminhtml/sales_order/', []);
                return ;
            }
            $reportContent = $result['reportContent'];
            echo $reportContent;
        } catch (Exception $e) {
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'generateReportAction']
            );
        }
    }

    /**
     *
     */
    public function _prepareSelectOrdersQueryForGenerateReport(&$pOrdersModel, $pBtnData, $pRoleId)
    {
        $pOrdersModel->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(
            ['increment_id', 'order_currency_code', 'grand_total']
        );
        if($pBtnData->getCheckWarehouse()){
            if(!Mage::helper('core')->isModuleEnabled('MDN_AdvancedStock')){
                Mage::getSingleton("adminhtml/session")->addError("Note: MDN_AdvancedStock Module is DISABLED");
                return false;

            }
            if(!Mage::helper('core')->isModuleEnabled('Stylisheve_Warehouserole')){
                Mage::getSingleton("adminhtml/session")->addError("Note: Stylisheve_Warehouserole Module is DISABLED");
                return false;
            }
            //get items depend on warehouses
            $pOrdersModel->getSelect()->join(
                ['sfoi' => 'sales_flat_order_item'], 'sfoi.order_id = `main_table`.entity_id',
                [
                    'sfoi.product_type', 'sfoi.name', 'sfoi.sku',
                    'sfoi.qty_ordered', 'sfoi.parent_item_id', 'sfoi.row_total', 'sfoi.discount_amount',
                ]
            )->join(
                ['sfoa' => 'sales_flat_order_address'],
                'sfoa.parent_id = `main_table`.entity_id AND sfoa.address_type = "shipping"', ['city','region', 'country_id']
            )->join(
                ['sfop' => 'sales_flat_order_payment'], 'sfop.parent_id = `main_table`.entity_id', ['method']
            )->join(
                ['esfoi' => 'erp_sales_flat_order_item'], 'esfoi.esfoi_item_id = sfoi.item_id', []
            )->join(
                ['swr' => 'stylisheve_warehouses_roles'], 'FIND_IN_SET(esfoi.preparation_warehouse, swr.warehouse_id)', []
            )->where("swr.role_id = $pRoleId");
            $pOrdersModel->getSelect()->joinLeft(
                ['parent' => 'sales_flat_order_item'], 'parent.parent_item_id = sfoi.item_id',
                ['parent_row_total'=>'parent.row_total', 'parent_discount_amount'=>'parent.discount_amount']
            );
        }//endIF
        if($pBtnData->getCheckDeliveryDate()){
            $pOrdersModel->getSelect()->join(
                ['aad' => 'amasty_amdeliverydate_deliverydate'], 'aad.order_id = `main_table`.entity_id', ['date']
            )->where("aad.date = '0000-00-00' OR CURDATE() >= aad.date OR CURDATE()+5 >= aad.date ");
        }
        return true;
    }

    /**
     *
     */
    public function _getReport(&$pOrdersModel, &$pBtnObj)
    {
        /* prepare orders*/
        $itemsArray = [];
        $ordersArray = [];
        $this->_prepareReportContent($ordersArray, $itemsArray, $pOrdersModel);
        $fileDir  = Mage::getBaseDir().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'local'.
            DIRECTORY_SEPARATOR.'StylishEve'.DIRECTORY_SEPARATOR.'OrderActionsButtons'.DIRECTORY_SEPARATOR.'Helper';
        $reportHtmlTemplate = file_get_contents($fileDir.DIRECTORY_SEPARATOR.'report.html', true);
        if(0==count($itemsArray) && 0==count($ordersArray)){
            $reportContent = str_replace("###replace###", "No Data Available", $reportHtmlTemplate);
            $msg = 'No Data Available';
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__($msg));
            return ['error'=>0, 'msg'=>$msg, 'reportContent'=>$reportContent];
        }
        $reportTitle = json_decode($pBtnObj->getReportAttrs(),true)['report_title'];
        //generateReportID
        $result = $this->_generateReportId();
        if($result['error']){
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__($result['msg']));
            return ['error'=>1, 'msg'=>$result['msg']];
        }
        $reportNo = $result['reportNo'];
        $reportTitleOutput = '<h3  style="text-align: center;">'.$reportTitle.'</h3>'.PHP_EOL;
        $reportItemsOutput = $this->_getReportContentHtml($itemsArray, $reportHtmlTemplate, $reportNo, count($ordersArray), 'items');
        $reportOrdersOutput = $this->_getReportContentHtml($ordersArray, $reportHtmlTemplate, $reportNo, count($ordersArray), 'orders');
        /* print report*/
        $reportContent = $reportTitleOutput.$reportItemsOutput.$reportOrdersOutput;
        preg_match("/<body[^>]*>(.*?)<\\/body>/si", $reportHtmlTemplate, $match);
        $reportContent = str_replace($match[1], $reportContent, $reportHtmlTemplate);
        return ['error'=>0, 'msg'=>'Data Available', 'reportContent'=>$reportContent, 'reportNo'=>$reportNo];
    }

    /**
     *
     */
    public function _prepareReportContent(&$pOrdersArray, &$pItemsArray, $pOrdersModel)
    {
        foreach ($pOrdersModel as $_order){
            $pItemsArray[] = [
                'name'  => $_order->getName(),
                'sku'   => $_order->getSku(),
                'qty'   => floatval($_order->getQtyOrdered())
            ];
            //get price
            if(empty($_order->getParentItemId())){
                $_price = floatval($_order->getRowTotal()) - floatval($_order->getDiscountAmount()) + 0.0;
            } else{
                $_price = floatval($_order->getParentRowTotal()) - floatval($_order->getParentDiscountAmount()) + 0.0;
            }
            $pOrdersArray[$_order->getIncrementId()][] = [
                'color'     => $_order->getName(),
                'sku'       => $_order->getSku(),
                'qty'       => floatval($_order->getQtyOrdered()),
                'price'     => $_price,
                'currency'  => $_order->getOrderCurrencyCode(),
                'total'     => floatval($_order->getGrandTotal())+0.0,
                'city'      => $_order->getCity(),
                'state'     => $_order->getRegion(),
                'country'   => $_order->getCountryId(),
                'payment'   => $_order->getMethod()
            ];
        }
    }

    /**
     *
     */
    public function _generateReportId()
    {
        $reportLastIdFileName = 'OrdersReportLastId.txt';
        $baseDir  = Mage::getBaseDir().DIRECTORY_SEPARATOR.'var';

        $fileDir  = $baseDir.DIRECTORY_SEPARATOR.'StylishEve'.DIRECTORY_SEPARATOR.'OrderActionsButtons';
        $filePath = $fileDir.DIRECTORY_SEPARATOR.$reportLastIdFileName;
        $fileDirMode = 0777;
        $reportIdPattern = '/^\d{9}$/';
        $data = '';
        $defaultReportId = '100000000';

        $result = $this->_createAndUpdateFileAndUpdateDataIfFileNotExist($baseDir, $fileDir, $fileDirMode, $filePath, $defaultReportId, $data);
        if($result && $result['error']){
            return ['data'=>$data, 'error'=>1, 'msg'=>$result['msg']];
        }
        if (!is_readable($filePath)) {
            return ['data'=>$data, 'error'=>1, 'msg'=>"Can\'t read file `".$filePath."`"];
        }
        $result = $this->_updateDataAfterReadFile($filePath, $defaultReportId, $reportIdPattern, $data);
        if($result && $result['error']){
            return ['data'=>$data, 'error'=>1, 'msg'=>$result['msg']];
        }
        #ASK:why data after that not save in file
        if (!preg_match($reportIdPattern, $data))
            $reportNo = rand(111111111,999999999);
        else
            $reportNo = $data + 1;
        return ['reportNo'=>$reportNo, 'error'=>0, 'msg'=>"File `".$filePath."` not updated"];
    }

    /**
     *
     */
    public function _generateReport($pReportContent)
    {
        $baseDir  = StylishEve_OrderActionsButtons_Block_Adminhtml_Reportsarchive_Grid::getMediaDir();
        $fileDir = StylishEve_OrderActionsButtons_Block_Adminhtml_Reportsarchive_Grid::getReportsDir($baseDir);
        $fileDirMode = 0777;
        return $this->_createAndUpdateFileAndUpdateDataIfFileNotExist($baseDir, $fileDir, $fileDirMode, '', '',$pReportContent);
    }

    /**
     *
     */
    public function _saveReportToArchive($pArchiveData)
    {
        $archiveData = [
            'report_number' => $pArchiveData['report_number'],
            'report_name'   => $pArchiveData['report_name'],
            'report_type'   => $pArchiveData['report_type'],
            //'created_at'  => Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s')
        ];
        $model = Mage::getSingleton('orderactionsbuttons/reportsarchive')->addData($archiveData);
        try
        {
            $model->save();
            return ['error'=>0, 'msg'=>'report data saved to archive table'];
        } catch (Exception $e) {
            return ['error'=>1, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * update reportNo if file exist
     */
    public function _updateReportIdInFile($pReportNo)
    {
        $reportLastIdFileName = 'OrdersReportLastId.txt';
        $baseDir  = Mage::getBaseDir().DIRECTORY_SEPARATOR.'var';

        $fileDir  = $baseDir.DIRECTORY_SEPARATOR.'StylishEve'.DIRECTORY_SEPARATOR.'OrderActionsButtons';
        $filePath = $fileDir.DIRECTORY_SEPARATOR.$reportLastIdFileName;

        if(!is_writable($filePath)){
            return ['error'=>1, 'msg'=>"Can\'t write file `".$filePath."`"];
        }
        $writeHandle = fopen($filePath, 'w');
        fwrite($writeHandle, $pReportNo);
        fclose($writeHandle);
        return false;
    }

    /**
     * create and update file if not exist
     */
    public function _createAndUpdateFileAndUpdateDataIfFileNotExist($pBaseDir, $pFileDir, $pFileDirMode, $pFilePath, $pDefaultReportId, &$pData)
    {
        if(empty($pFilePath)){
            //generate report file name
            $pFilePath = $this->_getReportFileName($pFileDir);
        }
        if(!file_exists($pFilePath)){
            $result = $this->_createDirectoryIfNotExist($pBaseDir, $pFileDir, $pFileDirMode);
            if($result && $result['error']){
                return ['error'=>1, 'msg'=>$result['msg']];
            }
            $handle = fopen($pFilePath, 'w');
            if( !$handle ){
                return ['error'=>1, 'msg'=>"Can\'t open file `".$pFilePath."`"];
            }
            if(!empty($pDefaultReportId)){
                $pData = $pDefaultReportId;
            }
            fwrite($handle, $pData);
            fclose($handle);
            return ['error'=>0, 'msg'=>"File `".$pFilePath."` created and updated successfully", 'filePath'=>$pFilePath];
        }
        return false;
    }

    /**
     *
     */
    public function _getReportFileName($pFileDir)
    {
        $reportFileNameLength = 15;
        $_files = scandir($pFileDir);
        do {
            //generate reportName not exist in dir
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));

            for ($i = 0; $i < $reportFileNameLength; $i++) {
                $key .= $keys[array_rand($keys)];
            }
            $reportFileName = $key.'.html';
        } while (in_array($reportFileName,$_files));
        return $pFileDir.DIRECTORY_SEPARATOR.$reportFileName;
    }

    /**
     * create dir if not exist
     */
    public function _createDirectoryIfNotExist($pBaseDir, $pFileDir, $pFileDirMode)
    {
        if (!file_exists($pFileDir)) {
            //check if writable or not
            if(!is_writable($pBaseDir)){
                return ['error'=>1, 'msg'=>'This directory `'.$pBaseDir.'` need permission'];
            } else{
                mkdir($pFileDir, $pFileDirMode, true);
                return ['error'=>0, 'msg'=>'This directory `'.$pFileDir.'` created'];
            }//endIF check dir. writable
        }//endIF check dir. exist
        return false;
    }

    /**
     * update Data After Read File
     */
    public function _updateDataAfterReadFile($pFilePath, $pDefaultReportId, $pReportIdPattern, &$pData)
    {
        $readHandle = fopen($pFilePath, 'r');
        $pData = fread($readHandle,filesize($pFilePath));
        fclose($readHandle);
        if (!preg_match($pReportIdPattern, $pData)){
            $pData = '';
            if(!is_writable($pFilePath)){
                return ['error'=>1, 'msg'=>"Can\'t write file `".$pFilePath."`"];
            }
            $writeHandle = fopen($pFilePath, 'w');
            $pData = $pDefaultReportId;
            fwrite($writeHandle, $pData);
            fclose($writeHandle);
        }
        return false;
    }

    /**
     * $pTableContentType = items OR orders
     */
    public function _getReportContentHtml(&$pTableDataArray, $pReportHtmlContent, $pReportNo, $pOrdersCount, $pTableContentType)
    {
        /* create table */
        $reportTable ="";
        switch ($pTableContentType):
            case 'items':
                $reportTable  = '<table class="qtytable" border="0" cellspacing="0" cellpadding="0">'.PHP_EOL;
                break;
            case 'orders':
                $reportTable  = '<style>.orderstable { border-collapse: collapse; width: 100%; margin-bottom: 20px;} .orderstable th, .orderstable td { border: 1px solid #ddd; padding: 7.5px;}</style>'.PHP_EOL;
                $reportTable .= '<table class="orderstable">'.PHP_EOL;
                break;
        endswitch;
        $reportTable .= '<thead>'.PHP_EOL;
        $reportTable .=     '<tr>'.PHP_EOL;
        switch ($pTableContentType):
            case 'items':
                $reportTable .= '<th class="no">#</th><th class="desc">Item</th><th class="unit">SKU</th><th class="qty">QUANTITY</th>'.PHP_EOL;
                break;
            case 'orders':
                $reportTable .= '<th>Order ID</th><th>Color</th><th>Cost</th><th>Total</th><th>City</th><th>Country</th><th>Payment Method</th>'.PHP_EOL;
                break;
        endswitch;
        $reportTable .=     '</tr>'.PHP_EOL;
        $reportTable .=   '</thead>'.PHP_EOL;
        $reportTable .=   '<tbody>'.PHP_EOL;
        /* add content to table */
        $reportTable .= $this->_prepareContentToAddToTable($pTableDataArray, $pTableContentType);
        /* end table */
        $reportTable .=   '</tbody>'.PHP_EOL;
        $reportTable .= '</table>'.PHP_EOL;
        /*replace string in html*/
        $reportOutput = $this->_getReportHtmlAfterReplaceString($pReportHtmlContent, $reportTable, $pReportNo, $pOrdersCount);
        preg_match("/<body[^>]*>(.*?)<\\/body>/si", $reportOutput, $match);
        return $match[1];
//        return $reportOutput;
    }

    /**
     * add content to table
     */
    public function _prepareContentToAddToTable(&$pTableDataArray, $pTableContentType)
    {
        switch ($pTableContentType):
            case 'items':
                $reportContent = $this->_prepareItemsToAddToTable($pTableDataArray);
                break;
            case 'orders':
                $reportContent = $this->_prepareOrdersToAddToTable($pTableDataArray);
                break;
        endswitch;
        return $reportContent;
    }

    /**
     * prepare items to add to table
     */
    public function _prepareItemsToAddToTable($pItemsArray)
    {
        $reportContent = "";
        $itemsTotalQty = 0;
        foreach($pItemsArray as $i=>$_item){
            $reportContent .=   "<tr>";
            $reportContent .=       "<td class=\"no\">".($i + 1)."</td>";
            $reportContent .=       "<td class=\"desc\">".$_item['name']."</td>";
            $reportContent .=       "<td class=\"unit\">".$_item['sku']."</td>";
            $reportContent .=       "<td class=\"qty\">".$_item['qty']."</td>";
            $reportContent .=   "</tr>";
            $itemsTotalQty += $_item['qty'];
        }
        $reportContent .= "<tfoot><tr><td></td><td></td><td><b>Total Quantity:</b></td><td><b>". $itemsTotalQty ."</b></td></tr></tfoot>";
        return $reportContent;
    }

    /**
     * prepare orders to add to table
     */
    public function _prepareOrdersToAddToTable($pOrdersArray)
    {
        $reportContent = "";
        foreach ($pOrdersArray as $_orderId=>$_order){
            $countItemsInOrder = count($_order);
            $reportContent .= '<tr>';
            $reportContent .=   '<td rowspan="'.($countItemsInOrder + 1).'">'.$_orderId.'</td>';
            $reportContent .= '</tr>';
            foreach ($_order as $i=>$v){
                $reportContent .= '<tr>';
                $reportContent .=   '<td>'.$v['qty'] .'x '. $v['color'].'</td>';
                $reportContent .=   '<td>'.$v['price'] .' '. $v['currency'].'</td>';
                if(0==$i){
                    $reportContent .= '<td rowspan="'.$countItemsInOrder.'">'.$v['total'] .' '. $v['currency'].'</td>';
                    $reportContent .= '<td rowspan="'.$countItemsInOrder.'">'.$v['city'] .' '. $v['state'].'</td>';
                    $reportContent .= '<td rowspan="'.$countItemsInOrder.'">'.$v['country'].'</td>';
                    $reportContent .= '<td rowspan="'.$countItemsInOrder.'">'.$v['payment'].'</td>';
                }
                $reportContent .= '</tr>';
            }//endForeach OrderItem
        }//endForeach Order
        return $reportContent;

    }

    /**
     *
     */
    public function _getReportHtmlAfterReplaceString($pReportHtmlContent, $pReportOrdersTable, $pReportNo, $pOrdersCount)
    {
        $reportDate = date("d/m/y");
        $reportLogoURL = "https://www.rushbrush.com/media/rushbrush/images/logo.png";
        $reportSupportEmail = 'support@rushbrush.com';
        $reportOrdersOutput = str_replace("###replace###", $pReportOrdersTable, $pReportHtmlContent);
        $reportOrdersOutput = str_replace("###reportNo###", $pReportNo, $reportOrdersOutput);
        $reportOrdersOutput = str_replace("###NoOrders###", $pOrdersCount, $reportOrdersOutput);
        $reportOrdersOutput = str_replace("###date###", $reportDate, $reportOrdersOutput);
        $reportOrdersOutput = str_replace("###logoURL###", $reportLogoURL, $reportOrdersOutput);
        $reportOrdersOutput = str_replace("###email###", $reportSupportEmail, $reportOrdersOutput);
        return $reportOrdersOutput;
    }

    /**
     *
     */
    public function reportsarchiveAction()
    {
        $this->_title($this->__("List Reports Archive"));
        $this->loadLayout()->_setActiveMenu("orderactionsbuttons/orderbutton")->_addBreadcrumb(Mage::helper("adminhtml")->__("List Resports Archive"), Mage::helper("adminhtml")->__("List Resports Archive"));
        $this->_addContent($this->getLayout()->createBlock("orderactionsbuttons/adminhtml_reportsarchive_grid"));
        $this->renderLayout();
    }
}

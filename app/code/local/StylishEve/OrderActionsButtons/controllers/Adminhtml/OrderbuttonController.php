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

    public function editAction()
    {
        $this->_title($this->__("OrderActionsButtons"));
        $this->_title($this->__("Orderbutton"));
        $this->_title($this->__("Edit Item"));

        if(!Mage::helper('core')->isModuleEnabled('Mirasvit_Helpdesk')){
            Mage::getSingleton("adminhtml/session")->addError("Note: Mirasvit_Helpdesk Module is DISABLED");
        }

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("orderactionsbuttons/orderbutton")->load($id);
        if ($model->getId()) {
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
                $post_data['order_current_status'] = implode(",", $post_data['order_current_status']);
                $post_data['accepted_role'] = implode(",", $post_data['accepted_role']);
                if(array_key_exists('order_removed_buttons', $post_data)){
                    $post_data['order_removed_buttons'] = implode(",", $post_data['order_removed_buttons']);
                    if(!array_key_exists('check_opening_tickets', $post_data)){
                        $post_data['check_opening_tickets']= '0';
                    }
                } else{
                    if(array_key_exists('check_opening_tickets', $post_data)){
                        unset($post_data['check_opening_tickets']);
                    }
                }
                $post_data['check_warehouse'] = (array_key_exists('check_warehouse', $post_data))?$post_data['check_warehouse']:'0';
                $post_data['check_delivery_date'] = (array_key_exists('check_delivery_date', $post_data))?$post_data['check_delivery_date']:'0';

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
     *  change order status
     *
     * - get order_id, order_tobe_status
     * - get order & change it status
     * - redirect to last page:ex. admin/sales_order/view/order_id/xxx
     */
    public function changeStatusAction()
    {
        try {
            $count = 1;
            $tobeStatus = $this->getRequest()->getParam('order_tobe_status');
            $currentStatus = $this->getRequest()->getParam('order_current_status');
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
                $result = $this->_changeStatus($userName, $orderId, $currentStatus, $tobeStatus, $tobeStatusName);
                if(!$result){
                    $this->_redirect($redirectUrl, $redirectData);
                    return ;
                }
            } else {
                $count = 0;
                $_currentStatuses = explode(',', $currentStatus);
                $_orders = mage::getModel('sales/order')->getCollection();
                $_orders->addFieldToSelect(['status', 'entity_id']);

                $btnId = $this->getRequest()->getParam('button_id');
                $btnData = Mage::getSingleton('orderactionsbuttons/orderbutton')->load($btnId);

                if('in_process'!=$tobeStatus && !in_array('confirmed', $_currentStatuses)){
                    $_orders->addFieldToFilter('status', ['in'=> $_currentStatuses]);
                    //Todo: check tobe , current, role for btn converted to 4 btns
                } else{
                    //TODO:check warehouse Module is active
                    $_orders->addFieldToFilter('main_table.status', 'confirmed');
                    if($btnData->getCheckWarehouse()){
                        //get orders depend on warehouses
                        $roleId = $_userInfo['roleId'];

                        $_orders->getSelect()->join(
                            ['sfoi' => 'sales_flat_order_item'], 'sfoi.order_id = `main_table`.entity_id', []
                        )->join(
                            ['esfoi' => 'erp_sales_flat_order_item'], 'esfoi.esfoi_item_id = sfoi.item_id', []
                        )->join(
                            ['swr' => 'stylisheve_warehouses_roles'], 'FIND_IN_SET(esfoi.preparation_warehouse, swr.warehouse_id)', []
                        )->where("swr.role_id = $roleId");

                        $_orders->getSelect()->group('main_table.entity_id');
                    }
                    if($btnData->getCheckDeliveryDate()){
                        $_orders->getSelect()->join(
                            ['aad' => 'amasty_amdeliverydate_deliverydate'], 'aad.order_id = `main_table`.entity_id', ['date']
                        )->where("aad.date = '0000-00-00' OR CURDATE() >= aad.date OR CURDATE()+5 >= aad.date ");
                    }
                }
                if ($_orders->getSize() == 0) {
                    Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("No Orders need to change status")
                    );
                    $this->_redirect($redirectUrl, $redirectData);
                    return ;
                }

                foreach ($_orders as $_order) {
                    if('ready_to_return' == $_order->getStatus() && 'return_canceled' == $tobeStatus ){
                        if ($_order->canCancel()) {
                            $_order->cancel();
                            $_order->setStatus($tobeStatus);
                            $_order->getStatusHistoryCollection(true);
                            $_order->save();
                            $count++;
                        }
                    } else{
                        $_order->setStatus($tobeStatus);
                        $history = $_order->addStatusHistoryComment($userName . " has changed status to $tobeStatusName.", false);
                        $history->setIsCustomerNotified(false);
                        $_order->save();
                        $count++;
                    }//endIF
                }//endForeach
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
     * get User Name
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
     */
    public function _changeStatus($pUserName, $pOrderId, $pCurrentStatus, $pTobeStatus, $pTobeStatusName)
    {
        $orderObj = Mage::getModel('sales/order')->load($pOrderId);
        //check currentStatus
        if(!in_array($orderObj->getStatus(), explode(",", $pCurrentStatus))){
            Mage::getSingleton("adminhtml/session")->addError("Order Current Status is $orderObj->getStatus() ");
            return false;
        }
        //check if $pTobeStatus is "back_to_stock"
        if (in_array($pTobeStatus, ["back_to_stock"])) {
            $hasRma = $this->_checkRmaProducts($orderObj, $pOrderId);
            if(!$hasRma){
                return false;
            }
        }//endIF
        //check if $currentStatus is "cash{paidAction}" OR "paid{completecashAction}"
        if (in_array($pCurrentStatus, ["cash", "paid", "pos", "pending_cfo_approval"])) {
            $this->_handleInvoice($orderObj);
            switch ($pCurrentStatus):
                case "cash":
                    if (!$this->_checkOrderHasItemWithoutSerial($orderObj)) {
                        $this->_addShipmentToOrder($orderObj);
                    }//endIF canShip
                    break;
                case "paid":
                case "pos":
                case "pending_cfo_approval":
                    if ($orderObj->canShip()) {
                        $this->_addShipmentToOrder($orderObj);
                    }//endIF canShip
                    break;
            endswitch;
        } else{
            $orderObj->setStatus($pTobeStatus);
            $history = $orderObj->addStatusHistoryComment($pUserName . " has changed status to $pTobeStatusName.", false);
            $history->setIsCustomerNotified(false);
            $orderObj->save();
        }//endIF
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
     *  generate Report
     *
     * - get order_current_status
     *
     */
    public function generateReportAction()
    {
        //userInfo
        $_userInfo = $this->_getUserInfo();
        $currentStatus = $this->getRequest()->getParam('order_current_status');
        $currentStatuses = explode(",", $currentStatus);
        $btnId = $this->getRequest()->getParam('button_id');
        $btnData = Mage::getSingleton('orderactionsbuttons/orderbutton')->load($btnId);

        /* prepare orders*/
        $itemsArray = [];
        $ordersArray = [];
        $_orders = Mage::getSingleton('sales/order')->getCollection();
        $_orders->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(
            ['increment_id', 'order_currency_code', 'grand_total']
        );
        $_orders->addFieldToFilter('status', ['in'=> $currentStatuses]);
        if($btnData->getCheckWarehouse()){
            //get items depend on warehouses
            $roleId = $_userInfo['roleId'];
            $_orders->getSelect()->join(
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
            // )->join(
            //     ['swr' => 'stylisheve_warehouses_roles'], 'FIND_IN_SET(esfoi.preparation_warehouse, swr.warehouse_id)', []
            // )->where("swr.role_id = $roleId");
            );
            $_orders->getSelect()->joinLeft(
                ['parent' => 'sales_flat_order_item'], 'parent.parent_item_id = sfoi.item_id',
                ['parent_row_total'=>'parent.row_total', 'parent_discount_amount'=>'parent.discount_amount']
            );
        }

        if(0==$_orders->getSize()){
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("No items exist for generate report"));
            $this->_redirect('adminhtml/sales_order/', []);
            return ;
        }
        foreach ($_orders as $_order) {
            $itemsArray[] = [
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
            $ordersArray[$_order->getIncrementId()][] = [
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
        /*########################################################*/
        try {
            $fileDir  = Mage::getBaseDir().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.'local'.
                DIRECTORY_SEPARATOR.'StylishEve'.DIRECTORY_SEPARATOR.'OrderActionsButtons'.DIRECTORY_SEPARATOR.'Helper';
            $reportHtmlContent = file_get_contents($fileDir.DIRECTORY_SEPARATOR.'report.html', true);

            if(0==count($itemsArray) && 0==count($ordersArray)){
                $reportContent = str_replace("###replace###", "No Data Available", $reportHtmlContent);
                echo $reportContent;
                return;
            }
            /* prepare report params */
            $reportTitle = 'In Process Orders';
            //generateReportID
            $result = $this->_generateReportId();
            if($result['error']){
                Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__($result['msg']));
                $this->_redirect('adminhtml/sales_order/', []);
                return ;
            }
            $reportNo = $result['reportNo'];
            $reportContent = '<h3  style="text-align: center;">'.$reportTitle.'</h3>'.PHP_EOL;
            $reportItemsOutput = $this->_getReportContentHtml($itemsArray, $reportHtmlContent, $reportNo, count($ordersArray), 'items');
            $reportOrdersOutput = $this->_getReportContentHtml($ordersArray, $reportHtmlContent, $reportNo, count($ordersArray), 'orders');
            /* print report*/
            $reportContent .= $reportItemsOutput.$reportOrdersOutput;
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
     * create and update file if not exist
     */
    public function _createAndUpdateFileAndUpdateDataIfFileNotExist($pBaseDir, $pFileDir, $pFileDirMode, $pFilePath, $pDefaultReportId, &$pData)
    {
        if(!file_exists($pFilePath)){
            $result = $this->_createDirectoryIfNotExist($pBaseDir, $pFileDir, $pFileDirMode);
            if($result && $result['error']){
                return ['error'=>1, 'msg'=>$result['msg']];
            }
            $handle = fopen($pFilePath, 'w');
            if( !$handle ){
                return ['error'=>1, 'msg'=>"Can\'t open file `".$pFilePath."`"];
            }
            $pData = $pDefaultReportId;
            fwrite($handle, $pDefaultReportId);
            fclose($handle);
            return ['error'=>0, 'msg'=>"File `".$pFilePath."` created and updated successfully"];
        }
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
        return $reportOutput;
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
}

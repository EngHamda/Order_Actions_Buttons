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
            $userName = $this->_getUserInfo()['userName'];
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
                $_current_statuses = explode(',', $currentStatus);
                $_orders = mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('in' => $_current_statuses));
                if ($_orders->getSize() == 0) {
                    Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("No Orders need to change status")
                    );
                    $this->_redirect($redirectUrl, $redirectData);
                    return ;
                }
                $count = 0;
                foreach ($_orders as $_order) {
                    $_order->setStatus($tobeStatus);
                    $history = $_order->addStatusHistoryComment($userName . " has changed status to $tobeStatusName.", false);
                    $history->setIsCustomerNotified(false);
                    $_order->save();
                    $count++;
                }
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("$count Order".($count > 1) ? 's' : ''." Updated Successfully"));
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
     *  generate Report
     *
     * - get order_current_status
     *
     */
    public function generateReportAction()
    {
        $items = [];
        $orderCurrentStatus = $this->getRequest()->getParam('order_current_status');
        //get orders
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', ['in' => explode(",", $orderCurrentStatus)])
            ->setOrder('created_at', 'desc');
        foreach ($orders as $order) {
            foreach ($order->getAllItems() as $item) {
                if (array_key_exists($item->getSku(), $items)) {
                    $items[$item->getSku()] = $items[$item->getSku()] + floatval($item->getQtyOrdered());
                } else {
                    $items[$item->getSku()] = floatval($item->getQtyOrdered());
                }
            }
        }
        //display table
        $this->loadLayout();
        $this->_title($this->__("Report Page"));
        $block = $this->getLayout()->getBlock('generatereport'); //then blocks are available
        $block->setItem($items);
        // $this->getLayout()->getBlock('head')->addItem('skin_OrderActionsButtons','generate-report-custom.css'); //To add css file under the /skin folder
        try {
            $this->renderLayout();
        } catch (Exception $e) {
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'generateReportAction']
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
        $userId = $admin_user_session->getUser()->getUserId();
        $userInfo['userName'] = $userFirstname .' '. $userLastname . ' ('. $userUsername .')';
        $userInfo['roleName'] = Mage::getModel('admin/user')->load($userId)->getRole()->role_name;

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
}

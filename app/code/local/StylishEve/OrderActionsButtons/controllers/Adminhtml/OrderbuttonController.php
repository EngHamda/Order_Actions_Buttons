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
    public function changeOrderStatusAction()
    {
        try {
            $tobeStatus = $this->getRequest()->getParam('order_tobe_status');
            $currentStatus = $this->getRequest()->getParam('order_current_status');
            $orderId = $this->getRequest()->getParam('order_id');
            $redirectUrl = 'adminhtml/sales_order/';
            $redirectData = [];
            if (!is_null($orderId)) {
                $orderObj = Mage::getModel('sales/order')->load($orderId);
                $orderObj->setStatus($tobeStatus);
                $orderObj->save();
                $redirectUrl = $redirectUrl . 'view';
                $redirectData['order_id'] = $orderId;
            } else {
                $_current_statuses = explode(',', $currentStatus);
                $_orders = mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('in' => $_current_statuses));
                if ($_orders->getSize() == 0) {
                    Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("No Orders with status " . $currentStatus)
                    );
                    $this->_redirect($redirectUrl, $redirectData);
                }
                foreach ($_orders as $_order) {
                    $_order->setStatus($tobeStatus);
                    $_order->save();
                }
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Orders updated Successfully"));
            $this->_redirect($redirectUrl, $redirectData);

        } catch (Exception $e) {
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Adminhtml_OrderbuttonController', 'methodName'=>'changeOrderStatusAction']
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
}

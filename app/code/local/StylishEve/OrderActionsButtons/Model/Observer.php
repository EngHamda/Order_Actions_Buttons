<?php

class StylishEve_OrderActionsButtons_Model_Observer
{
    /**
     * for add "available" action buttons in order page to change order status
     *
     * - check each page, each block
     * - for accepted block
     *        - get order //for specifice order (ex.view order with id = 1)
     *        - get all data of OrderActionsButtons module
     *        - loop each for remove button for each record
     *                  -- has action_type == RemoveButtonsFromView && page_type == view
     *        - loop each for create change status buttons for each record
     *                  -- has action_type == ChangeStatusForView && page_type == view
     *            * check if order_current_status has avalible actionsbuttons
     *                * if yes check button type then show buttons
     *        - loop each for create generate report button for each record
     *                  -- has action_type == GenerateReportForGrid && page_type == grid
     *
     */
    public function AddOrRemoveOrderActionsButton(Varien_Event_Observer $observer)
    {
        try{
            $block = $observer->getBlock();
            if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
                $this->displayButtons($block, array('ChangeStatusForView'), 'view');
                $this->removeButtons( $block, array('RemoveButtonsFromView'), 'view');
            } elseif ($block instanceof Mage_Adminhtml_Block_Sales_Order) {
                $this->displayButtons($block, array('ChangeStatusForGrid', 'GenerateReportForGrid'), 'grid');
            }//endIf check Block
        } catch(Exception $e){
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Model_Observer', 'methodName'=>'AddOrRemoveOrderActionsButton']
            );
        }
    }

    /**
     *
     * get login user role
     *
     */
    public function getUserRole()
    {
        //get user role
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminUserId = $admin_user_session->getUser()->getUserId();
        return Mage::getModel('admin/user')->load($adminUserId)->getRole()->role_name;
    }

    public function displayButtons(&$block, $action_type, $page_type)
    {
        $order = $block->getOrder();
        if ($order)
            $orderId = $order->getEntityId();
        $role_name = $this->getUserRole();
        $orderActionsData = Mage::getSingleton('orderactionsbuttons/orderbutton')->getCollection();
        $orderActionsData->addFieldToFilter('action_type', array('in' => $action_type));
        $orderActionsData->addFieldToFilter('accepted_role', array('finset' => $role_name));
        if($orderActionsData->getSize() == 0){
            return false;
        }//endIF
        foreach ($orderActionsData as $buttonData) {
            if ($page_type == 'view') {
                $this->_addButtonInBlock($block, $order, $buttonData, $role_name, $orderId);
            } elseif ($page_type == 'grid') {
                $actionTypeArray = StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray();
                if (in_array($role_name, explode(",", $buttonData->getAcceptedRole()))) {
                    $requestData = ['order_current_status' => $buttonData->getOrderCurrentStatus(), 'button_id' =>$buttonData->getId() ];
                    $urlRequest = 'admin_orderactionsbuttons/adminhtml_orderbutton/';
                    //check if action_type is change status OR generate report
                    switch ($buttonData->getActionType()):
                        case $actionTypeArray['Change Status For Grid Page']:
                            $urlAction = 'changestatus';
                            $requestData['order_tobe_status'] = $buttonData->getOrderTobeStatus();
                            break;
                        case $actionTypeArray['Generate Report']:
                            $urlAction = 'generatereport';
                            break;
                    endswitch;
                    #TODO: change 'window.open' to confirmSetLocation in change status
                    $block->addButton('btn_' . $buttonData->getName(), array(
                        'label' => Mage::helper('core')->__($buttonData->getName()),
                        'onclick' => "window.open('{$block->getUrl($urlRequest.$urlAction, $requestData)}');",
                        'class' => $buttonData->getCssClasses(),//change color and change icon
                    ));
                }//endIF
            }
        }//endForeach
    }

    /**
     *
     */
    public function _addButtonInBlock(&$block, &$pOrder, $pButtonData, $pRoleName, $pOrderId)
    {
        if (
            in_array($pOrder->getStatus(), explode(",", $pButtonData->getOrderCurrentStatus())) &&
            in_array($pRoleName, explode(",", $pButtonData->getAcceptedRole()))
        ) {
            $tobeStatusName = Mage::getSingleton('sales/order_status')->getCollection()
                ->addFieldToSelect('label')->addFieldToFilter('status', ['eq' => $pButtonData->getOrderTobeStatus()])
                ->getFirstItem()->getLabel();
            $message = Mage::helper('core')->__('Are you sure you want to change order status to '.$tobeStatusName.'?');
            $block->addButton('btn_' . $pButtonData->getName(), array(
                'label' => Mage::helper('core')->__($pButtonData->getName()),
                'onclick' => "confirmSetLocation('{$message}', '{$block->getUrl(
                        // 'onclick' => "window.open('{$block->getUrl(
							'admin_orderactionsbuttons/adminhtml_orderbutton/changestatus', 
							[
								'order_id' => $pOrderId, 
								'order_current_status' => $pButtonData->getOrderCurrentStatus(), 
								'order_tobe_status' => $pButtonData->getOrderTobeStatus(), 
								'button_id' => $pButtonData->getId()
							]
						)}');",
                'class' => $pButtonData->getCssClasses(),//change color and change icon
            ));
        } //endIF

    }

    /**
     * Notes:
     *  - to get btn id ===> id form $block->(protected)_buttons[$level][$id]"sourceCode"
     *  - $block->unsetChild('order_edit_button');//for remove child in block
     *  - $block->getButtonsHtml(); //for get html for all btns in block
     */
    public function removeButtons(&$block, $action_type='RemoveButtonForView', $page_type='view')
    {
        $orderActions = array_keys($block->getChild());//for get list of all buttons in block
        $role_name = $this->getUserRole();
        $orderActionsData = Mage::getSingleton('orderactionsbuttons/orderbutton')->getCollection();
        $orderActionsData->addFieldToFilter('action_type', array('in' => $action_type));
        $orderActionsData->addFieldToFilter('accepted_role', array('finset' => $role_name));
        $orderActionsData->addFieldToFilter('order_current_status', array('finset' => $block->getOrder()->getStatus()));
        $orderActionsData->addFieldToFilter('order_removed_buttons', array('neq' => ''));
        $orderActionsData->addFieldToFilter('order_removed_buttons', array('notnull' => true));
        if($orderActionsData->getSize() == 0){
            return false;
        }//endIF
        //check IF order has ticket
        $isTicketModuleEnabled = (Mage::helper('core')->isModuleEnabled('Mirasvit_Helpdesk'))?true:false;
        $orderId = $block->getOrder()->getEntityId();
        if($isTicketModuleEnabled){
            $orderHasOpenTicket = $this->_orderHasOpenTicket($orderId);
        }//endIF
        foreach ($orderActionsData as $buttonData) {
            $checkTickets = ( !empty($buttonData->getCheckOpeningTickets()) )?true:false;
            if(!$checkTickets || ($checkTickets && $isTicketModuleEnabled && $orderHasOpenTicket) ){
                $removedBtns = explode(",",$buttonData->getOrderRemovedButtons());
                foreach ($removedBtns as $btnId){
                    //check if in array
                    if(in_array($btnId."_button",$orderActions)){
                        //remove
                        $block->removeButton($btnId);
                    }
                }
            }//endIF
        }//endForeach
    }

    /**
     *
     * check IF order has open ticket, remove btn
     *
     */
    public function _orderHasOpenTicket($pOrderId)
    {
        $helpdeskModel = Mage::getSingleton('helpdesk/ticket')->getCollection();
        $helpdeskModel->addFieldToFilter('order_id', array('eq' => $pOrderId));
        $helpdeskModel->addFieldToFilter('status_id', 1);//code is open, //getStatus()->getCode()
        $orderHasOpenTicket = ($helpdeskModel->getSize() == 0)?false:true;
        return $orderHasOpenTicket;
    }
}

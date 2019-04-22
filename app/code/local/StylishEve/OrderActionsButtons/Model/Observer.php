<?php

class StylishEve_OrderActionsButtons_Model_Observer
{
    /**
     * for add or remove "available" action buttons in order page to change order status
     * OR add "available" action buttons in orders grid page to change orders status OR generate reports
     *
     * - check each page, each block
     *      - for accepted block "Mage_Adminhtml_Block_Sales_Order_View"
     *              - call displayButtons "method", send page_type, actions
     *              - call removeButtons "method", send page_type, actions
     *      - for accepted block "Mage_Adminhtml_Block_Sales_Order"
     *              - call displayButtons "method", send page_type, actions
     */
    public function AddOrRemoveOrderActionsButton(Varien_Event_Observer $observer)
    {
        try{
            $block = $observer->getBlock();
            if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
                $actions = StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray();
                $this->displayButtons($block, array($actions['Change Status For View Page']), 'view');
                $this->removeButtons( $block, array($actions['Remove Buttons From View Page']), 'view');
            } elseif ($block instanceof Mage_Adminhtml_Block_Sales_Order) {
                $actions = StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray();
                $this->displayButtons($block, array($actions['Change Status For Grid Page'], $actions['Generate Report']), 'grid');
            }//endIf check Block
        } catch(Exception $e){
            Mage::helper('orderactionsbuttons')->logException('OrderActionsButtons.log',
                ['exceptionObj'=>$e, 'className'=>'StylishEve_OrderActionsButtons_Model_Observer', 'methodName'=>'AddOrRemoveOrderActionsButton']
            );
        }
    }

    /**
     *
     * get login user role name
     *
     * @return String
     */
    public function getUserRole()
    {
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminUserId = $admin_user_session->getUser()->getUserId();
        return Mage::getModel('admin/user')->load($adminUserId)->getRole()->role_name;
    }

    /**
     * - get order from block
     * - get orderId if order exists in block (ex.view order with id = 1)
     * - get login user role name
     * - get order buttons from db depending on roleName, actionType
     *      - return false if no buttons exist
     *      - loop each button
     *              -- has page_type == view
     *                  ** call _addButtonInViewBlock "method", send $block, $order, $buttonData, $roleName, $orderId
     *              -- has page_type == grid
     *                  ** call _addButtonInViewBlock "method", send $block, $buttonData, $roleName
     *
     * @param Object $block
     * @param Array $pActionType
     * @param String $pPageType
     * @return Boolean
     */
    public function displayButtons(&$block, $pActionType, $pPageType)
    {
        $order = $block->getOrder();
        if ($order)
            $orderId = $order->getEntityId();
        $roleName = $this->getUserRole();
        $orderActionsData = Mage::getSingleton('orderactionsbuttons/orderbutton')->getCollection();
        $orderActionsData->addFieldToFilter('action_type', array('in' => $pActionType));
        $orderActionsData->addFieldToFilter('accepted_role', array('finset' => $roleName));
        if($orderActionsData->getSize() == 0){
            return false;
        }//endIF
        foreach ($orderActionsData as $buttonData) {
            if ($pPageType == 'view') {
                $this->_addButtonInViewBlock($block, $order, $buttonData, $roleName, $orderId);
            } elseif ($pPageType == 'grid') {
                $this->_addButtonInGridBlock($block, $buttonData, $roleName);
            }
        }//endForeach
    }

    /**
     * depend on orderStatus, userRoleName
     *  - get tobeStatusName
     *  - add button
     *
     * @param Object $block
     * @param Object $pOrder
     * @param Object $pButtonData
     * @param String $pRoleName
     * @param Int $pOrderId
     * @return Boolean
     */
    public function _addButtonInViewBlock(&$block, &$pOrder, $pButtonData, $pRoleName, $pOrderId)
    {
        if (
            !in_array($pOrder->getStatus(), explode(",", $pButtonData->getOrderCurrentStatus())) ||
            !in_array($pRoleName, explode(",", $pButtonData->getAcceptedRole()))
        ) {
            return false;
        } //endIF

        $tobeStatusName = Mage::getSingleton('sales/order_status')->getCollection()
            ->addFieldToSelect('label')->addFieldToFilter('status', ['eq' => $pButtonData->getOrderTobeStatus()])
            ->getFirstItem()->getLabel();
        $message = Mage::helper('core')->__('Are you sure you want to change order status to '.$tobeStatusName.'?');

        $block->addButton('btn_' . $pButtonData->getName(), array(
            'label' => Mage::helper('core')->__($pButtonData->getName()),
            'onclick' => "confirmSetLocation('{$message}', '{$block->getUrl(
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
    }

    /**
     * depend on userRoleName, buttonActionType
     *  - add button
     *
     * @param Object $block
     * @param Object $pButtonData
     * @param String $pRoleName
     * @return Boolean
     */
    public function _addButtonInGridBlock(&$block, $pButtonData, $pRoleName)
    {
        if (!in_array($pRoleName, explode(",", $pButtonData->getAcceptedRole()))) {
            return false;
        }//endIF
        $_actions = StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray();
        $_requestData = ['order_current_status' => $pButtonData->getOrderCurrentStatus(), 'button_id' =>$pButtonData->getId() ];
        $_urlRequest = 'admin_orderactionsbuttons/adminhtml_orderbutton/';

        //check if actionType is change status OR generate report
        switch ($pButtonData->getActionType()):
            case $_actions['Change Status For Grid Page']:
                $_urlAction = 'changestatus';
                $_requestData['order_tobe_status'] = $pButtonData->getOrderTobeStatus();

                $tobeStatusName = Mage::getSingleton('sales/order_status')->getCollection()
                    ->addFieldToSelect('label')->addFieldToFilter('status', ['eq' => $pButtonData->getOrderTobeStatus()])
                    ->getFirstItem()->getLabel();
                $message = Mage::helper('core')->__('Are you sure you want to update available orders status to '.$tobeStatusName.'?');

                $block->addButton('btn_' . $pButtonData->getName(), array(
                    'label' => Mage::helper('core')->__($pButtonData->getName()),
                    'onclick' => "confirmSetLocation('{$message}', '{$block->getUrl($_urlRequest.$_urlAction, $_requestData)}');",
                    'class' => $pButtonData->getCssClasses(),//change color and change icon
                ));
                break;
            case $_actions['Generate Report']:
                $_urlAction = 'generatereport';
                $block->addButton('btn_' . $pButtonData->getName(), array(
                    'label' => Mage::helper('core')->__($pButtonData->getName()),
                    'onclick' => "window.open('{$block->getUrl($_urlRequest.$_urlAction, $_requestData)}');",
                    'class' => $pButtonData->getCssClasses(),//change color and change icon
                ));
                break;
        endswitch;
    }

    /**
     * Notes:
     *  - to get btn id ===> id form $block->(protected)_buttons[$level][$id]"sourceCode"
     *  - $block->unsetChild('order_edit_button');//for remove child in block
     *  - $block->getButtonsHtml(); //for get html for all btns in block
     *
     * @param Object $block
     * @param String $pActionType
     * @param String $pPageType
     * @return Boolean
     */
    public function removeButtons(&$block, $pActionType='RemoveButtonForView', $pPageType='view')
    {
        $orderActions = array_keys($block->getChild());//for get list of all buttons in block
        $roleName = $this->getUserRole();
        $orderActionsData = Mage::getSingleton('orderactionsbuttons/orderbutton')->getCollection();
        $orderActionsData->addFieldToFilter('action_type', array('in' => $pActionType));
        $orderActionsData->addFieldToFilter('accepted_role', array('finset' => $roleName));
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
     * @param Int $pOrderId
     * @return Boolean $orderHasOpenTicket
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

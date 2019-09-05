<?php


class Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

        $this->_controller = "adminhtml_orderbutton";
        $this->_blockGroup = "orderactionsbuttons";
        $this->_headerText = Mage::helper("orderactionsbuttons")->__("Order Buttons Manager");
        $this->_addButtonLabel = Mage::helper("orderactionsbuttons")->__("Add New Order Button");
        parent::__construct();

    }

}
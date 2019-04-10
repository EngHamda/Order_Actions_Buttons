<?php
class StylishEve_OrderActionsButtons_Model_Mysql4_Orderbutton extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("orderactionsbuttons/orderbutton", "id");
    }
}
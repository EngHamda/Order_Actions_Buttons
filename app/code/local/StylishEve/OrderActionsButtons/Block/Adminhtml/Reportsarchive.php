<?php


class StylishEve_OrderActionsButtons_Block_Adminhtml_Reportsarchive extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

         $this->_controller = "adminhtml_orderbutton";
         $this->_blockGroup = "orderactionsbuttons";
         $this->_headerText = Mage::helper("orderactionsbuttons")->__("Reportsarchive Manager");
         /**
          * Note:
          *  replace
          *         parent::__construct();
          *  with
          *         $this->setTemplate('widget/grid/container.phtml');
          *         $this->getGridHtml();
          *
          *  - for add report grid title and, use report model
          */
         $this->setTemplate('widget/grid/container.phtml');
         $this->getGridHtml();
         $this->_removeButton('add');

    }

}
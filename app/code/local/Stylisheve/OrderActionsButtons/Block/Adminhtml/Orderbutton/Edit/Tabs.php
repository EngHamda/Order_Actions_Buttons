<?php
class Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("orderbutton_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("orderactionsbuttons")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("orderactionsbuttons")->__("Item Information"),
				"title" => Mage::helper("orderactionsbuttons")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("orderactionsbuttons/adminhtml_orderbutton_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}

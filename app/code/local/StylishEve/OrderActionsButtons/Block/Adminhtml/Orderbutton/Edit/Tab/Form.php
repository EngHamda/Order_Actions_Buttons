<?php

class StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("orderactionsbuttons_form", array("legend" => Mage::helper("orderactionsbuttons")->__("Item information")));
        
            $fieldset->addField("name", "text", array(
                "label" => Mage::helper("orderactionsbuttons")->__("name"),
                "required" => true,
                "name" => "name",
            ));

            $fieldset->addField("color", "text", array(
                "label" => Mage::helper("orderactionsbuttons")->__("color"),
                "name" => "color",
            ));

            $fieldset->addField("icon", "text", array(
                "label" => Mage::helper("orderactionsbuttons")->__("icon"),
                "name" => "icon",
            ));

            $fieldset->addField("css_classes", "text", array(
                "label" => Mage::helper("orderactionsbuttons")->__("css_classes"),
                "name" => "css_classes",
            ));

            /**
             *
             * - for add accepted_role field
             *
             */
            $fieldset->addField('accepted_role', 'multiselect', array(
                "label" => Mage::helper('orderactionsbuttons')->__('accepted_role'),
                "values" => StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getAcceptedRoleInForm(),
                "required" => true,
                "name" => 'accepted_role',
            ));

            $fieldset->addField('order_current_status', 'multiselect', array(
                "label" => Mage::helper('orderactionsbuttons')->__('order_current_status'),
                "values" => StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getOrderCurrentStatusesInForm(),
                "required" => true,
                "name" => 'order_current_status',
            ));

            $fieldset->addField("action_type", "select", array(
                "label" => Mage::helper("orderactionsbuttons")->__("action type"),
                "values" => StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeInForm(),
                "required" => true,
                "name" => "action_type",
            ));

            $fieldset->addField('order_tobe_status', 'select', array(
                "label" => Mage::helper('orderactionsbuttons')->__('order_tobe_status'),
                "values" => StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getOrderTobeStatusesInForm(),
                "required" => true,
                "name" => 'order_tobe_status',
                "container_id" =>"order-tobe-status-container",
            ));

            $fieldset->addField('order_removed_buttons', 'multiselect', array(
                "label" => Mage::helper('orderactionsbuttons')->__('order_removed_buttons'),
                "values" => StylishEve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getRemovedButtonsNamesInForm(),
                "required" => true,
                "name" => 'order_removed_buttons',
                "container_id" =>"order-removed-buttons-container",
            ));

            $model  = Mage::registry('orderbutton_data');
            $fieldset->addField('check_warehouse', 'checkbox', array(
                "label"         => Mage::helper('orderactionsbuttons')->__('Check Warehouse?'),
                "onclick"       => "this.value = this.checked ? 1 : 0;",
                "name"          => "check_warehouse",
                "container_id"  => "order-check-warehouse-container",
                "checked"       => $model->getCheckWarehouse(),//!empty("this.value")?true:false,
                //"checked"       => "1 == this.value ? '' : '';",
            ));
            $fieldset->addField('check_delivery_date', 'checkbox', array(
                "label"         => Mage::helper('orderactionsbuttons')->__('Check Delivery Date?'),
                "onclick"       => "this.value = this.checked ? 1 : 0;",
                "name"          => "check_delivery_date",
                "container_id"  => "order-check-delivery-date-container",
                "checked"       => $model->getCheckDeliveryDate(),//!empty("this.value")?true:false,
                //"checked"       => "1 == this.value ? '' : '';",
            ));
            $fieldset->addField('check_opening_tickets', 'checkbox', array(
                "label"         => Mage::helper('orderactionsbuttons')->__('Check Any Opening Tickets?'),
                "onclick"       => "this.value = this.checked ? 1 : 0;",
                "name"          => "check_opening_tickets",
                "container_id"  => "order-check-tickets-container",
                "checked"       => $model->getCheckOpeningTickets(),//!empty("this.value")?true:false,
                //"checked"       => "1 == this.value ? '' : '';",
            ));

        if (Mage::getSingleton("adminhtml/session")->getOrderbuttonData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getOrderbuttonData());
            Mage::getSingleton("adminhtml/session")->setOrderbuttonData(null);
        } elseif (Mage::registry("orderbutton_data")) {
            $form->setValues(Mage::registry("orderbutton_data")->getData());
        }
        return parent::_prepareForm();
    }
}

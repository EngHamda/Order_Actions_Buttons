<?php

class Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
                "values" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getAcceptedRoleInForm(),
                "required" => true,
                "name" => 'accepted_role',
            ));

            $fieldset->addField('order_current_status', 'multiselect', array(
                "label" => Mage::helper('orderactionsbuttons')->__('order_current_status'),
                "values" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getOrderCurrentStatusesInForm(),
                "required" => true,
                "name" => 'order_current_status',
            ));

            $fieldset->addField("action_type", "select", array(
                "label" => Mage::helper("orderactionsbuttons")->__("action type"),
                "values" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeInForm(),
                "required" => true,
                "name" => "action_type",
            ));

            $fieldset->addField('order_tobe_status', 'select', array(
                "label" => Mage::helper('orderactionsbuttons')->__('order_tobe_status'),
                "values" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getOrderTobeStatusesInForm(),
                "required" => true,
                "name" => 'order_tobe_status',
                "container_id" =>"order-tobe-status-container",
            ));

            $fieldset->addField('order_removed_buttons', 'multiselect', array(
                "label" => Mage::helper('orderactionsbuttons')->__('order_removed_buttons'),
                "values" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getRemovedButtonsNamesInForm(),
                "required" => true,
                "name" => 'order_removed_buttons',
                "container_id" =>"order-removed-buttons-container",
            ));

            $model  = Mage::registry('orderbutton_data');
            if(is_string($model->getReportAttrs())){
                //from db
                $reportAttrs = json_decode(html_entity_decode($model->getReportAttrs()),true);
            } elseif (is_array($model->getReportAttrs())){
                //from session
                $reportAttrs = $model->getReportAttrs();
            } else{
                $reportAttrs = $model->getReportAttrs();
            }
            /**/
            #TODO: add reportNumber "text,readOnly" "last report id OR default id "
            $fieldset->addField('report_type', 'select', array(
                "label" => Mage::helper('orderactionsbuttons')->__('Report Type'),
                "values" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getReportTypesInForm(),
                "required" => true,
                "name" => 'report_attrs[report_type]',
                "value" => !empty($reportAttrs)?$reportAttrs['report_type']: null,
                "container_id" =>"report-type-container",
            ));
            $fieldset->addField('report_title', 'text', array(
                "label" => Mage::helper('orderactionsbuttons')->__('Report Title'),
                "required" => true,
                "name" => 'report_attrs[report_title]',
                "value" => !empty($reportAttrs)?$reportAttrs['report_title']: null,
                "container_id" =>"report-title-container",
            ));
            /**/

            $fieldset->addField('check_warehouse', 'checkbox', array(
                "label"         => Mage::helper('orderactionsbuttons')->__('Check Warehouse?'),
                "onclick"       => "this.value = this.checked ? 1 : 0;",
                "name"          => "check_warehouse",
                "container_id"  => "order-check-warehouse-container",
                "checked"       => $model->getCheckWarehouse(),
            ));
            $fieldset->addField('check_delivery_date', 'checkbox', array(
                "label"         => Mage::helper('orderactionsbuttons')->__('Check Delivery Date?'),
                "onclick"       => "this.value = this.checked ? 1 : 0;",
                "name"          => "check_delivery_date",
                "container_id"  => "order-check-delivery-date-container",
                "checked"       => $model->getCheckDeliveryDate(),
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
            $form->addValues(Mage::getSingleton("adminhtml/session")->getOrderbuttonData());
            Mage::getSingleton("adminhtml/session")->setOrderbuttonData(null);
        } elseif (Mage::registry("orderbutton_data")) {
            $form->addValues(Mage::registry("orderbutton_data")->getData());
        }
        return parent::_prepareForm();
    }
}

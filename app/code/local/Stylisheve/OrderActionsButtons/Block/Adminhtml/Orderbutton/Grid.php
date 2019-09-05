<?php

class Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("orderbuttonGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
        $this->setStatusRecorders();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("orderactionsbuttons/orderbutton")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("id", array(
            "header" => Mage::helper("orderactionsbuttons")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "id",
        ));

        $this->addColumn("name", array(
            "header" => Mage::helper("orderactionsbuttons")->__("name"),
            "index" => "name",
        ));
        #TODO:css_classes be select in form & filtration
        $this->addColumn("css_classes", array(
            "header" => Mage::helper("orderactionsbuttons")->__("css_classes"),
            "index" => "css_classes",
            'frame_callback' => array($this, '_DisplayText'),
            //'filter' => false
        ));
        $this->addColumn("action_type", array(
            "header" => Mage::helper("orderactionsbuttons")->__("action_type"),
            "index" => "action_type",
            "type" => "options",
            "options" => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionType(),
        ));
        $this->addColumn('accepted_role', array(
            'header' => Mage::helper('orderactionsbuttons')->__('accepted_role'),
            'index' => 'accepted_role',
            'frame_callback' => array($this, '_PreparingAcceptedRoleDisplay'), //for prepare col. cell before display
            'filter_condition_callback' => array($this, '_AcceptedRoleFiltration'),//for filtration
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getAcceptedRoleInGrid(),
        ));
        $this->addColumn('order_current_status', array(
            'header' => Mage::helper('orderactionsbuttons')->__('order_current_status'),
            'index' => 'order_current_status',
            'frame_callback' => array($this, '_preparingOrderCurrentStatusDisplay'), //for prepare col. cell before display
            'filter_condition_callback' => array($this, '_orderCurrentStatusFiltration'),//for filtration
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getOrderStatusesInGrid(),
        ));
        $this->addColumn('order_tobe_status', array(
            'header' => Mage::helper('orderactionsbuttons')->__('order_tobe_status'),
            'index' => 'order_tobe_status',
            'frame_callback' => array($this, '_preparingOrderTobeStatusDisplay'), //for prepare col. cell before display
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getOrderStatusesInGrid(),
        ));
        $this->addColumn("check_warehouse", array(
            "header" => Mage::helper("orderactionsbuttons")->__("check_warehouse"),
            "index" => "check_warehouse",
            'frame_callback' => array($this, '_DisplayCheckText'),
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getChecksInGrid(),
        ));
        $this->addColumn("check_delivery_date", array(
            "header" => Mage::helper("orderactionsbuttons")->__("check_delivery_date"),
            "index" => "check_delivery_date",
            'frame_callback' => array($this, '_DisplayCheckText'),
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getChecksInGrid(),
        ));
        $this->addColumn("report_attrs", array(
            "header" => Mage::helper("orderactionsbuttons")->__("report_attrs"),
            "index" => "report_attrs",
            'frame_callback' => array($this, '_DisplayReportAttrsText'),
            'filter' => false
        ));

        $this->addColumn('order_removed_buttons', array(
            'header' => Mage::helper('orderactionsbuttons')->__('order_removed_buttons'),
            'index' => 'order_removed_buttons',
            'frame_callback' => array($this, '_preparingOrderRemovedButtonsDisplay'), //for prepare col. cell before display
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getRemovedButtonsNamesInGrid(),
        ));
        $this->addColumn("check_opening_tickets", array(
            "header" => Mage::helper("orderactionsbuttons")->__("check_opening_tickets"),
            "index" => "check_opening_tickets",
            'frame_callback' => array($this, '_DisplayCheckText'),
            'type' => 'options',
            'options' => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getChecksInGrid(),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_orderbutton', array(
            'label' => Mage::helper('orderactionsbuttons')->__('Remove Orderbutton'),
            'url' => $this->getUrl('*/adminhtml_orderbutton/massRemove'),
            'confirm' => Mage::helper('orderactionsbuttons')->__('Are you sure?')
        ));
        return $this;
    }

    /**
     *
     */
    public function _DisplayText($value, $row, $column, $isExport)
    {
        if(empty($value)) {
            return '--';
        } else{
            return $value;
        }
    }

    /**
     *
     * used for get action_type page
     */
    static public function getActionType()
    {
        $data_array = array();
        $data_array['ChangeStatusForView'] = 'Change Status For View Page';
        $data_array['ChangeStatusForGrid'] = 'Change Status For Grid Page';
        $data_array['GenerateReport&ChangeStatusForGrid'] = 'Generate Report & Change Status';
        $data_array['GenerateReportForGrid'] = 'Generate Report';
        $data_array['RemoveButtonsFromView'] = 'Remove Buttons From View Page';
        return ($data_array);
    }

    /**
     *
     * used in grid page for filter action_type page
     */
    static public function getActionTypeInForm()
    {
        $data_array = array();
        $data_array[] = ['value' => null, 'label' => 'Select action type'];
        $_actionTypes = self::getActionType();
        foreach ($_actionTypes as $index => $value) {
            $data_array[] = array('value' => $index, 'label' => $value);
        }
        return ($data_array);
    }

    /**
     *
     * used in observer page for filter action_type page
     */
    static public function getActionTypeValueArray()
    {
        $data_array = array();
        $data_array['Change Status For View Page'] = 'ChangeStatusForView';
        $data_array['Change Status For Grid Page'] = 'ChangeStatusForGrid';
        $data_array['Generate Report & Change Status'] = 'GenerateReport&ChangeStatusForGrid';
        $data_array['Generate Report'] = 'GenerateReportForGrid';
        $data_array['Remove Buttons From View Page'] = 'RemoveButtonsFromView';
        return ($data_array);
    }

    /**
     * get all accepted roles from db
     *
     * - This fn. used to set array options of dropdown filter field in grid and input in form
     *
     */
    static public function getAcceptedRoleRecorders()
    {
        return Mage::getSingleton('admin/roles')->getCollection()->addFieldToSelect('role_name');
    }

    /**
     *
     * used in edit and add form
     */
    static public function getAcceptedRoleInForm()
    {
        $data_array = array();
        $roleRecorders = self::getAcceptedRoleRecorders();
        foreach ($roleRecorders as $index => $roleData) {
            $data_array[] = array('value' => $roleData->getRoleName(), 'label' => $roleData->getRoleName());
        }
        return ($data_array);
    }

    /**
     * used in Grid For Filtration
     */
    static public function getAcceptedRoleInGrid()
    {
        $data_array = array();
        $roleRecorders = self::getAcceptedRoleRecorders();
        foreach ($roleRecorders as $index => $roleData) {
            $data_array[$roleData->getRoleName()] = $roleData->getRoleName();
        }
        return ($data_array);
    }

    /**
     * prepare accepted_role columns
     *
     */
    public function _PreparingAcceptedRoleDisplay($value, $row, $column, $isExport)
    {
        $_acceptedRoleCollection = self::getAcceptedRoleRecorders();
        $_acceptedRoles = explode(',', $row->getAcceptedRole());
        $_acceptedRoleCollection->addFieldToFilter('role_name', ['in' => $_acceptedRoles]);
        if($_acceptedRoleCollection->getSize('role_name') == 0) {
            return '--';
        }
        $_htmlContent = '<div style="position:relative"><ul class="iwd_order_btn_items_in_grid">';
        foreach ( $_acceptedRoleCollection as $_role )
        {
            $_htmlContent .= '<li style="margin:3px;">•&nbsp;'. $_role->getRoleName() .'</li>';
        }
        $_htmlContent .= '</ul></div>';
        return $_htmlContent;
    }

    /**
     * prepare accepted_role columns
     *
     * - for filter roles in accepted_role by role name
     *
     */
    public function _AcceptedRoleFiltration($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()){
            return '**';
        }
        $this->getCollection()->addFieldToFilter('accepted_role', ['finset' => $value]);
    }

    /**
     * get all order statuses from db
     *
     * - This fn. used to set array options of dropdown filter field in grid and inputs in form
     *
     */
    static public function getAllOrderStatusesRecorders()
    {
        return Mage::getSingleton('sales/order_status')->getCollection()->addFieldToSelect(['status', 'label']);
    }

    /**
     *
     * used in edit and add form
     */
    static public function getOrderCurrentStatusesInForm()
    {
        $data_array = array();
        $statusRecorders = self::getAllOrderStatusesRecorders();
        foreach ($statusRecorders as $index => $statusData) {
            $data_array[] = array('value' => $statusData->getStatus(), 'label' => $statusData->getLabel());
        }
        return ($data_array);
    }

    /**
     *
     * used in edit and add form
     */
    static public function getOrderTobeStatusesInForm()
    {
        $data_array = array();
        $data_array[] = ['value' => null, 'label' => 'Select tobe Status'];
        $statusRecorders = self::getAllOrderStatusesRecorders();
        foreach ($statusRecorders as $index => $statusData) {
            $data_array[] = array('value' => $statusData->getStatus(), 'label' => $statusData->getLabel());
        }
        return ($data_array);
    }

    /**
     * used in Grid For Filtration
     */
    static public function getOrderStatusesInGrid()
    {
        $data_array = array();
        $statusRecorders = self::getAllOrderStatusesRecorders();
        foreach ($statusRecorders as $index => $statusData) {
            $data_array[$statusData->getStatus()] = $statusData->getLabel();
        }
        return ($data_array);
    }

    /**
     * prepare order_current_status columns
     *
     */
    public function _preparingOrderCurrentStatusDisplay($value, $row, $column, $isExport)
    {
        $_orderStatusesCollection = self::getAllOrderStatusesRecorders();
        $_orderCurrentStatuses = explode(',', $row->getOrderCurrentStatus());
        $_orderStatusesCollection->addFieldToFilter('status', ['in' => $_orderCurrentStatuses]);
        if($_orderStatusesCollection->getSize('status') == 0) {
            return '--';
        }
        $_htmlContent = '<div style="position:relative"><ul class="iwd_order_btn_items_in_grid">';
        foreach ( $_orderStatusesCollection as $_status )
        {
            $_htmlContent .= '<li style="margin:3px;">•&nbsp;'. $_status->getLabel() .'</li>';
        }
        $_htmlContent .= '</ul></div>';
        return $_htmlContent;
    }

    /**
     * prepare order_current_status columns
     *
     * - for filter statuses in order_current_status by status label
     *
     */
    public function _orderCurrentStatusFiltration($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()){
            return '**';
        }
        $this->getCollection()->addFieldToFilter('order_current_status', ['finset' => $value]);
    }

    /**
     * prepare order_tobe_status columns
     *
     */
    public function _preparingOrderTobeStatusDisplay($value, $row, $column, $isExport)
    {
        $_orderStatusesCollection = self::getAllOrderStatusesRecorders();
        $_orderTobeStatuses = $row->getOrderTobeStatus();
        $_orderStatusesCollection->addFieldToFilter('status', ['eq' => $_orderTobeStatuses]);
        if($_orderStatusesCollection->getSize('status') == 0) {
            return '--';
        } else{
            return $_orderStatusesCollection->getFirstItem()->getLabel();
        }
    }

    /**
     *
     * used in edit and add form
     */
    static public function getReportTypes()
    {
        $data_array = array();
        $data_array['Stock In'] = 'Stock In';
        $data_array['Stock In - Refusal'] = 'Stock In - Refusal';
        $data_array['Stock In - Return'] = 'Stock In - Return';
        $data_array['Stock Take'] = 'Stock Take';
        return ($data_array);
    }

    /**
     *
     * used in edit and add form
     */
    static public function getReportTypesInForm()
    {
        $data_array = array();
        $data_array[] = ['value' => null, 'label' => 'Select Report Type'];
        $reportTypes = self::getReportTypes();
        foreach ($reportTypes as $value => $label) {
            $data_array[] = array('value' => $value, 'label' => $label);
        }
        return ($data_array);
    }

    /**
     * used in Grid For Filtration
     */
    static public function getChecksInGrid()
    {
        return array('No','Yes');
    }

    /**
     *
     */
    public function _DisplayCheckText($value, $row, $column, $isExport)
    {
        if(empty($value)){
            return '--';
        } else{
            return $value;
        }
    }

    /**
     *
     */
    public function _DisplayReportAttrsText($value, $row, $column, $isExport)
    {
        if(empty($value)) {
            return '--';
        } else{
            $_htmlContent = '<div style="position:relative"><ul>';
            foreach ( json_decode(html_entity_decode($value),true) as $i => $v )
            {
                $_htmlContent .= '<li style="margin:3px;">•&nbsp;'. $i.': '. $v .'</li>';
            }
            $_htmlContent .= '</ul></div>';
            return $_htmlContent;
        }
    }

    static public function getRemovedButtonsNames()
    {
        $data_array = array();
        $data_array['back'] = 'back';
        $data_array['order_hold'] = 'Hold';
        $data_array['order_ship'] = 'Ship';
        $data_array['order_edit'] = 'Edit';
        $data_array['order_cancel'] = 'Cancel';
        $data_array['order_unhold'] = 'Unhold';
        $data_array['order_invoice'] = 'Invoice';
        $data_array['order_reorder'] = 'Reorder';
        $data_array['order_creditmemo'] = 'Creditmemo';
        $data_array['send_notification'] = 'Send Notification';
        $data_array['deny_payment'] = 'Deny Payment';
        $data_array['accept_payment'] = 'Accept Payment';
        $data_array['void_payment'] = 'Void Payment';
        $data_array['get_review_payment_update'] = 'Reorder';
        return ($data_array);
    }

    static public function getRemovedButtonsNamesInForm()
    {
        $data_array = array();
        $removedButtonsNames = self::getRemovedButtonsNames();
        foreach ($removedButtonsNames as $index => $value) {
            $data_array[] = array('value' => $index, 'label' => $value);
        }
        return ($data_array);
    }

    /**
     * used in Grid For Filtration
     */
    static public function getRemovedButtonsNamesInGrid()
    {
        $data_array = array();
        $removedButtonsNames = self::getRemovedButtonsNames();
        foreach ($removedButtonsNames as $index => $btn) {
            $data_array[$index] = $btn;
        }
        return ($data_array);
    }

    /**
     * prepare order_removed_buttons column
     *
     */
    public function _preparingOrderRemovedButtonsDisplay($value, $row, $column, $isExport)
    {
        $_orderRemovedButtons = $row->getOrderRemovedButtons();
        $removedButtonsNames = self::getRemovedButtonsNames();
        if(empty($_orderRemovedButtons)) {
            return '--';
        } else{
            return $removedButtonsNames[$_orderRemovedButtons];
        }
    }

}
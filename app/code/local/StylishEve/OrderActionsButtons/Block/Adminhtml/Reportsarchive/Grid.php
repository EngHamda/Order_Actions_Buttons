<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 17/03/19
 * Time: 11:00
 */

class StylishEve_OrderActionsButtons_Block_Adminhtml_Reportsarchive_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId("reportsarchiveGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
        $this->setStatusRecorders();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("orderactionsbuttons/reportsarchive")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("id", array(
            "header"    => Mage::helper("orderactionsbuttons")->__("ID"),
            "align"     => "right",
            "width"     => "50px",
            "type"      => "number",
            "index"     => "report_id",
        ));

        $this->addColumn("report_number", array(
            "header"    => Mage::helper("orderactionsbuttons")->__("Report Number"),
            "index"     => "report_number",
        ));
        $this->addColumn("report_name", array(
            "header"    => Mage::helper("orderactionsbuttons")->__("Report Name"),
            "index"     => "report_name",
        ));
        $this->addColumn("report_type", array(
            "header"    => Mage::helper("orderactionsbuttons")->__("Report Type"),
            "index"     => "report_type",
        ));
        $this->addColumn("created_at", array(
            "header"    => Mage::helper("orderactionsbuttons")->__("Created At"),
            "index"     => "created_at",
            "type"      => "datetime",
        ));
        $this->addColumn("updated_at", array(
            "header"    => Mage::helper("orderactionsbuttons")->__("Updated At"),
            "index"     => "updated_at",
            "type"      => "datetime",
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        #TODO:ReportName be clickable to redirect to report
        // return $this->getUrl("*/adminhtml_agentrewarddetails", array('user_id' => $row->getUser_id()));
        return false;
    }
}
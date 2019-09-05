<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 17/03/19
 * Time: 11:00
 */

class Stylisheve_OrderActionsButtons_Block_Adminhtml_Reportsarchive_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
            "type"      => 'options',
            "options"   => Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getReportTypes(),
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

    /**
     * get report url
     *
     * urlExample: http://127.0.0.1/magento/media/Stylisheve/OrderActionsButtons/stockmovment/0umsckqkcd1178k.html
     *
     * @param Object $row
     * @return String OR Boolean
     */
    public function getRowUrl($row)
    {
        $_fileDir = self::getReportsDir('media');
        $_reports = self::getAllReportsName();

        if (in_array($row->getReportName(), $_reports)) {
            return Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL).$_fileDir.DIRECTORY_SEPARATOR.$row->getReportName();
        } else{
            return false;
        }
    }

    /**
     * get All reports in directory
     *
     * @return Array
     */
    static public function getAllReportsName()
    {
        $_baseDir = self::getMediaDir();
        $_fileDir = self::getReportsDir($_baseDir);
        return scandir($_fileDir);
    }

    /**
     * get media directory
     *
     * @return String
     */
    static public function getMediaDir()
    {
        return Mage::getBaseDir('media');
    }

    /**
     * get reports directory
     *
     * @return String
     */
    static public function getReportsDir($pBaseDir)
    {
        return $pBaseDir.DIRECTORY_SEPARATOR.'Stylisheve'.
            DIRECTORY_SEPARATOR.'OrderActionsButtons'.DIRECTORY_SEPARATOR.'stockmovment';
    }
}
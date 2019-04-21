<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE order_button_reports_archive(
    report_id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
    report_number VARCHAR(20) NOT NULL, 
    report_name VARCHAR(20) NOT NULL, 
    report_type VARCHAR(50) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    updated_at TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, 
    PRIMARY KEY (report_id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
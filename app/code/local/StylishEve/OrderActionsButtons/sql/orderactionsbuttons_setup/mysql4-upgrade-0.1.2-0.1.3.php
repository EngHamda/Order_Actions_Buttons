<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE order_button
    DROP COLUMN `action_type`,
    ADD COLUMN `action_type` VARCHAR(100) NOT NULL AFTER `css_classes`;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
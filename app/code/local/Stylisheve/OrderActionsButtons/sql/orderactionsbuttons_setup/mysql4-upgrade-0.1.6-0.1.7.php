<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE order_button
    ADD COLUMN `check_warehouse` VARCHAR(1) NULL DEFAULT '0' AFTER `order_removed_buttons`,
    ADD COLUMN `check_delivery_date` VARCHAR(1) NULL DEFAULT '0' AFTER `check_warehouse`;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
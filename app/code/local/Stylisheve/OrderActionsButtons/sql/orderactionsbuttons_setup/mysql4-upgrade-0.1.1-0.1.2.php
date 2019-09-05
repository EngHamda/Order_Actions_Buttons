<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE order_button
 MODIFY order_tobe_status VARCHAR(100),
 CHANGE url action_type VARCHAR(50) NOT NULL;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
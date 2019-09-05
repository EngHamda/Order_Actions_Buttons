<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE order_button
    CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
    CHANGE `color` `color` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
    CHANGE `icon` `icon` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
    CHANGE `css_classes` `css_classes` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, 
    CHANGE `order_current_status` `order_current_status` VARCHAR(225) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
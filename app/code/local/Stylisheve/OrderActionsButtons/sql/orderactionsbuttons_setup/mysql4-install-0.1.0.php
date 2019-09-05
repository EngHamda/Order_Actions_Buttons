<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table order_button(
    id int not null auto_increment,
    name varchar(100) not null,
    color varchar(100),
    icon varchar(100),
    url varchar(100),
    css_classes varchar(100),
    order_current_status varchar(100) not null,
    order_tobe_status varchar(100) not null,
    primary key(id)
);
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
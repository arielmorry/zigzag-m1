<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$definition = [
    'type'     => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'nullable' => true,
    'comment'  => 'Delivery Date',
];

$installer->addAttribute('order', 'zigzag_delivery_from', $definition);
$installer->addAttribute('order', 'zigzag_delivery_to', $definition);
$installer->addAttribute('quote', 'zigzag_delivery_from', $definition);
$installer->addAttribute('quote', 'zigzag_delivery_to', $definition);

$installer->endSetup();
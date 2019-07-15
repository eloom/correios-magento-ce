<?php

##eloom.licenca##

$installer = $this;
$installer->startSetup();

/**
 * ------------ Status ------------
 */
$statusTable = $installer->getTable('eloom_correiossro_sonda');
$table = $installer->getConnection()->newTable($statusTable)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_BIGINT, 10, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        ), 'ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_BIGINT, 10, array(
        'nullable' => false,
        ), 'Order Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'nullable' => false,
        ), 'Store ID')
    ->addColumn('number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Track Number')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 0, array(
        'nullable' => false,
        ), 'Creation date')
    ->addColumn('delivery_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 0, array(
        'nullable' => false,
        ), 'Delivery date')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Message')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Track Status')
    ->addColumn('attempts', Varien_Db_Ddl_Table::TYPE_SMALLINT, 3, array(
    'nullable' => false,
    ), 'Attempts');

$installer->getConnection()->createTable($table);
/**
 * ------------ Status Fim ------------
 */
$installer->endSetup();

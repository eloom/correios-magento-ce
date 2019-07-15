<?php

##eloom.licenca##

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/**
 * ------------ Status ------------
 */
$statusTable = $installer->getTable('eloom_correiossro_sonda');

if (!$conn->tableColumnExists($this->getTable('eloom_correiossro_sonda'), 'message')) {
  $installer->run("ALTER TABLE {$this->getTable('eloom_correiossro_sonda')} ADD `message` VARCHAR(255) NULL");
}
/**
 * ------------ Status Fim ------------
 */
$installer->endSetup();

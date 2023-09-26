<?php

##eloom.licenca##

$installer = $this;
$installer->startSetup();

/**
 * ------------ Atributos ------------
 */
$attribute = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

$volumeAltura = $attribute->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'volume_altura');
if ($volumeAltura === false) {
  $this->addAttribute('catalog_product', 'volume_altura', array(
      'group' => 'Frete',
      'type' => 'int',
      'input' => 'text',
      'label' => 'Altura (cm)',
      'source' => '',
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
      'visible' => true,
      'required' => false,
      'user_defined' => false,
      'default' => 0,
      'visible_on_front' => false,
      'is_configurable' => false,
      'sort_order' => 1,
      'apply_to' => 'simple,bundle,grouped,configurable',
      'note' => 'Altura da embalagem do produto (Para cálculo de Frete)'
  ));
}

$volumeLargura = $attribute->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'volume_largura');
if ($volumeLargura === false) {
  $this->addAttribute('catalog_product', 'volume_largura', array(
      'group' => 'Frete',
      'type' => 'int',
      'input' => 'text',
      'label' => 'Largura (cm)',
      'source' => '',
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
      'visible' => true,
      'required' => false,
      'user_defined' => false,
      'default' => 0,
      'visible_on_front' => false,
      'is_configurable' => false,
      'sort_order' => 2,
      'apply_to' => 'simple,bundle,grouped,configurable',
      'note' => 'Largura da embalagem do produto (Para cálculo  de Frete)'
  ));
}

$volumeComprimento = $attribute->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'volume_comprimento');
if ($volumeComprimento === false) {
  $this->addAttribute('catalog_product', 'volume_comprimento', array(
      'group' => 'Frete',
      'type' => 'int',
      'input' => 'text',
      'label' => 'Comprimento (cm)',
      'source' => '',
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
      'visible' => true,
      'required' => false,
      'user_defined' => false,
      'default' => 0,
      'visible_on_front' => false,
      'is_configurable' => false,
      'sort_order' => 3,
      'apply_to' => 'simple,bundle,grouped,configurable',
      'note' => 'Comprimento da embalagem do produto (Para cálculo  de Frete)'
  ));
}

/**
 * ------------ Atributos Fim ------------
 */
$installer->endSetup();

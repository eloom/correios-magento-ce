<?php

##eloom.licenca##

class Eloom_Correios_Model_System_Config_Source_TaxaExtra {

  /**
   * @return array
   */
  public function toOptionArray() {
    return array(
        array('value' => '0', 'label' => Mage::helper('eloom_correios')->__('Não')),
        array('value' => '1', 'label' => Mage::helper('eloom_correios')->__('Em percentual')),
        array('value' => '2', 'label' => Mage::helper('eloom_correios')->__('Em valor')),
    );
  }

}

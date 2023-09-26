<?php

##eloom.licenca##

class Eloom_Correios_Model_System_Config_Source_PesoEncomenda {

  /**
   * Constants for weight
   */
  const WEIGHT_GR = 'gr';
  const WEIGHT_KG = 'kg';

  /**
   * @return array
   */
  public function toOptionArray() {
    return array(
        array('value' => self::WEIGHT_GR, 'label' => Mage::helper('eloom_correios')->__('Gramas')),
        array('value' => self::WEIGHT_KG, 'label' => Mage::helper('eloom_correios')->__('Kilos')),
    );
  }

}

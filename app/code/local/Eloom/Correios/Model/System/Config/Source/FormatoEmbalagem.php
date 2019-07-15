<?php

##eloom.licenca##

class Eloom_Correios_Model_System_Config_Source_FormatoEmbalagem {

  /**
   * @return array
   */
  public function toOptionArray() {
    return array(
        array('value' => '1', 'label' => Mage::helper('eloom_correios')->__('Caixa/Pacote')),
        array('value' => '2', 'label' => Mage::helper('eloom_correios')->__('Rolo/Prisma')),
        array('value' => '3', 'label' => Mage::helper('eloom_correios')->__('Envelope')),
    );
  }

}

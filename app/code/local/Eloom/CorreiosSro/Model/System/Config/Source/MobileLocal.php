<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_System_Config_Source_MobileLocal {

  /**
   * @return array
   */
  public function toOptionArray() {
    return array(
        array('value' => 'order', 'label' => Mage::helper('eloom_correiossro')->__('Flat Order')),
        array('value' => 'order_address', 'label' => Mage::helper('eloom_correiossro')->__('Sales Flat Order Address')),
    );
  }

}

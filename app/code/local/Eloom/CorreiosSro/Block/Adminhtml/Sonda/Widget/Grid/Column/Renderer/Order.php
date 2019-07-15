<?php

##eloom.licenca##

class Eloom_CorreiosSro_Block_Adminhtml_Sonda_Widget_Grid_Column_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

  public function render(Varien_Object $row) {
    $order = Mage::getModel('sales/order');
    $order->loadByAttribute('entity_id', $row->getData('order_id'));

    return $order->getIncrementId();
  }

}

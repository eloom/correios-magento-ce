<?php

##eloom.licenca##

class Eloom_CorreiosSro_Block_Adminhtml_Sonda extends Mage_Adminhtml_Block_Widget_Grid_Container {

  public function __construct() {
    $this->_blockGroup = 'eloom_correiossro_adminhtml';
    $this->_controller = 'sonda';
    $this->_headerText = Mage::helper('eloom_correiossro')->__('Sonda Directory');

    parent::__construct();
    $this->_removeButton('add');
  }
  
  public function getCreateUrl() {
    return $this->getUrl('eloom_correiossro/sonda/edit');
  }

}

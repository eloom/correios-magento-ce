<?php

##eloom.licenca##

class Eloom_CorreiosSro_Block_Adminhtml_Sonda_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

  protected function _construct() {
    $this->_blockGroup = 'eloom_correiossro_adminhtml';
    $this->_controller = 'sonda';

    $this->_mode = 'edit';

    $newOrEdit = $this->getRequest()->getParam('id') ? $this->__('Edit') : $this->__('New');
    $this->_headerText = $newOrEdit . ' ' . $this->__('Localizador');
  }

}

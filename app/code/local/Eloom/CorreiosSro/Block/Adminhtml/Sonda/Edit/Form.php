<?php

##eloom.licenca##

class Eloom_CorreiosSro_Block_Adminhtml_Sonda_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

  protected function _prepareForm() {
    $form = new Varien_Data_Form(array(
        'id' => 'edit_form',
        'action' => $this->getUrl('eloom_correiossro/sonda/edit', array(
            '_current' => true,
            'continue' => 0,
            )
        ),
        'method' => 'post',
    ));
    $form->setUseContainer(true);
    $this->setForm($form);

    $fieldset = $form->addFieldset(
        'general', array(
        'legend' => $this->__('Sonda Details')
        )
    );

    $this->_addFieldsToFieldset($fieldset, array(
        'number' => array(
            'label' => $this->__('Numero do Objeto'),
            'input' => 'text',
            'required' => true,
        )
    ));

    return $this;
  }

  protected function _addFieldsToFieldset(Varien_Data_Form_Element_Fieldset $fieldset, $fields) {
    $requestData = new Varien_Object($this->getRequest()->getPost('localizadorData'));

    foreach ($fields as $name => $_data) {
      if ($requestValue = $requestData->getData($name)) {
        $_data['value'] = $requestValue;
      }
      $_data['name'] = "localizadorData[$name]";
      $_data['title'] = $_data['label'];
      if (!array_key_exists('value', $_data)) {
        $_data['value'] = $this->_getSonda()->getData($name);
      }

      $fieldset->addField($name, $_data['input'], $_data);
    }

    return $this;
  }

  /**
   * Retrieve the existing sonda for pre-populating the form fields.
   * For a new sonda entry, this will return an empty sonda object.
   */
  protected function _getSonda() {
    if (!$this->hasData('sonda')) {
      $sonda = Mage::registry('current_sonda');
      if (!$sonda instanceof Eloom_CorreiosSro_Model_Sonda) {
        $sonda = Mage::getModel('eloom_correiossro/sonda');
      }

      $this->setData('sonda', $sonda);
    }

    return $this->getData('sonda');
  }

}

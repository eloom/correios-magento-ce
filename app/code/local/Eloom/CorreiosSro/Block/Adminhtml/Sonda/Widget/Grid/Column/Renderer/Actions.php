<?php

##eloom.licenca##

class Eloom_CorreiosSro_Block_Adminhtml_Sonda_Widget_Grid_Column_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text {

  public function render(Varien_Object $row) {
    $html = parent::render($row);
    $html .= '<button onclick="javascript:eloomCorreiosSroInst.tracking(' . $row->getId() . ', \'' . $row->getNumber() . '\');" style="float: right;">' . Mage::helper('eloom_correiossro')->__('Rastrear') . '</button>';

    $html .= '<div id="tracking-result-' . $row->getId() . '" style="text-align: left;padding-top: 20px;"></div>';

    return $html;
  }

}

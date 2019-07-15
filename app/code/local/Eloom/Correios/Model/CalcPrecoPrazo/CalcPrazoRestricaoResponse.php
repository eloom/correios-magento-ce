<?php

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoRestricaoResponse {

  /**
   * @var cResultado $CalcPrazoRestricaoResult
   * @access public
   */
  public $CalcPrazoRestricaoResult = null;

  /**
   * @param cResultado $CalcPrazoRestricaoResult
   * @access public
   */
  public function __construct($CalcPrazoRestricaoResult) {
    $this->CalcPrazoRestricaoResult = $CalcPrazoRestricaoResult;
  }

}

<?php

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoRestricaoResponse {

  /**
   * @var cResultado $CalcPrecoPrazoRestricaoResult
   * @access public
   */
  public $CalcPrecoPrazoRestricaoResult = null;

  /**
   * @param cResultado $CalcPrecoPrazoRestricaoResult
   * @access public
   */
  public function __construct($CalcPrecoPrazoRestricaoResult) {
    $this->CalcPrecoPrazoRestricaoResult = $CalcPrecoPrazoRestricaoResult;
  }

}

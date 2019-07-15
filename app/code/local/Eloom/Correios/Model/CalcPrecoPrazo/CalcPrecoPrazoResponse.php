<?php

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoResponse {

  /**
   * @var cResultado $CalcPrecoPrazoResult
   * @access public
   */
  public $CalcPrecoPrazoResult = null;

  /**
   * @param cResultado $CalcPrecoPrazoResult
   * @access public
   */
  public function __construct($CalcPrecoPrazoResult) {
    $this->CalcPrecoPrazoResult = $CalcPrecoPrazoResult;
  }

}

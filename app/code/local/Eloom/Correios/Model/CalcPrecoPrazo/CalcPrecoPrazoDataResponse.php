<?php

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoDataResponse {

  /**
   * @var cResultado $CalcPrecoPrazoDataResult
   * @access public
   */
  public $CalcPrecoPrazoDataResult = null;

  /**
   * @param cResultado $CalcPrecoPrazoDataResult
   * @access public
   */
  public function __construct($CalcPrecoPrazoDataResult) {
    $this->CalcPrecoPrazoDataResult = $CalcPrecoPrazoDataResult;
  }

}

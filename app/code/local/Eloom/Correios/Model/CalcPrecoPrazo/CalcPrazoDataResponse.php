<?php

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoDataResponse {

  /**
   * @var cResultado $CalcPrazoDataResult
   * @access public
   */
  public $CalcPrazoDataResult = null;

  /**
   * @param cResultado $CalcPrazoDataResult
   * @access public
   */
  public function __construct($CalcPrazoDataResult) {
    $this->CalcPrazoDataResult = $CalcPrazoDataResult;
  }

}

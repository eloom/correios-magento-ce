<?php

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoResponse {

  /**
   * @var cResultado $CalcPrazoResult
   * @access public
   */
  public $CalcPrazoResult = null;

  /**
   * @param cResultado $CalcPrazoResult
   * @access public
   */
  public function __construct($CalcPrazoResult) {
    $this->CalcPrazoResult = $CalcPrazoResult;
  }

}

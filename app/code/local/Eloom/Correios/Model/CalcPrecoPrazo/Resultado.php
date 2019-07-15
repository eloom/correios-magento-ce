<?php

class Eloom_Correios_Model_CalcPrecoPrazo_Resultado {

  /**
   * @var Eloom_Correios_Model_CalcPrecoPrazo_Servico[] $servicos
   * @access public
   */
  public $servicos = null;

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_Servico[] $servicos
   * @access public
   */
  public function __construct($servicos) {
    $this->servicos = $servicos;
  }

}

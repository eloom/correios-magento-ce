<?php

##eloom.licenca##

class Eloom_Correios_Model_CotacaoFrete extends Mage_Core_Model_Abstract {

  private $logger;

  /**
   * Initialize resource model
   */
  protected function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    parent::_construct();
  }

  /**
   * 
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazo $calcPrecoPrazo
   * @return type
   */
  public function calcularFrete(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazo $calcPrecoPrazo) {
    $helper = Mage::helper('eloom_correios');

    $calcPrecoPrazo->nCdEmpresa = $helper->getCodigoEmpresa();
    $calcPrecoPrazo->sDsSenha = $helper->getSenhaAcesso();
    $calcPrecoPrazo->nCdServico = $helper->getCodigoServicos();
    $calcPrecoPrazo->nCdFormato = $helper->getCodigoFormato();

    if ($helper->isAvisoRecebimento()) {
      $calcPrecoPrazo->sCdAvisoRecebimento = 'S';
    } else {
      $calcPrecoPrazo->sCdAvisoRecebimento = 'N';
    }
    if ($helper->isMaoPropria()) {
      $calcPrecoPrazo->sCdMaoPropria = 'S';
    } else {
      $calcPrecoPrazo->sCdMaoPropria = 'N';
    }

    try {
      $calculoFrete = new Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoWS();
      $calculoFrete->__setLocation('http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx');

      $calculaFreteResponse = $calculoFrete->CalcPrecoPrazo($calcPrecoPrazo);
      if ($helper->isWriteLog()) {
        $this->logger->info($calculaFreteResponse);
      }
      return $calculaFreteResponse->CalcPrecoPrazoResult->Servicos;
    } catch (SoapFault $sf) {
      $this->logger->fatal($sf->getMessage());
    }

    return null;
  }

}

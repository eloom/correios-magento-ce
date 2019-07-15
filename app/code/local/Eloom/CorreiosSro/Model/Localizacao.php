<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_Localizacao extends Mage_Core_Model_Abstract {

  private $logger;

  /**
   * Initialize resource model
   */
  public function __construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
  }

  /**
   *
   * @param type $localizador
   * @param type $resultado 'U' | 'L'
   * @return null
   */
  public function localizaMercadoria($localizador, $resultado) {
    $helper = Mage::helper('eloom_correiossro');

    try {
      $parameters = array('usuario' => trim($helper->getUsuario()), 'senha' => trim($helper->getSenha()), 'tipo' => 'L', 'resultado' => $resultado, 'lingua' => '101', 'objetos' => $localizador);

      $client = new SoapClient($helper->getUrl());
      $response = $client->buscaEventos($parameters);
      
      if ($helper->isWriteLog()) {
        $this->logger->info("Correios Response \n $response->return");
      }
      if (!$response->return) {
        throw new RuntimeException(sprintf("Rastreamento não encontrado para o objeto %s", $localizador));
      }
      $objeto = new Eloom_Correios_Sro_Response();
      $parse = $objeto->parse($response->return);
      return $parse;
    } catch (Exception $exc) {
      $this->logger->fatal('Erro ao localizar objeto ' . $localizador, $exc);
    }

    return null;
  }

}

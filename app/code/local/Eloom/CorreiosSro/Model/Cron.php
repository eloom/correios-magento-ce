<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_Cron extends Mage_Core_Model_Abstract {

  private $logger;

  /**
   * Initialize resource model
   */
  protected function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    parent::_construct();
  }

  /**
   * Envia e-mail com status de entrega realizada
   *
   */
  public function statusEntrega() {
    $helper = Mage::helper('eloom_correiossro');
    if (!$helper->isActive(Mage::app()->getStore()->getStoreId())) {
      return true;
    }
    $this->logger->info('Status de entrega - INÍCIO');

    $sondas = Mage::getResourceModel('eloom_correiossro/sonda_collection')
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');

    foreach ($sondas as $sonda) {
      try {
        $localizacao = Mage::getModel('eloom_correiossro/localizacao');
        $response = $localizacao->localizaMercadoria($sonda->getNumber(), 'U');
        if (is_null($response)) {
          continue;
        }
        if ($response->hasError()) {
          if ($sonda->isNotityError()) {
            $message = sprintf("Objeto %s - %s - %s", $sonda->getNumber(), $response->getError(), 'Para corrigir o problema vá em Vendas > Correios > Localizadores');

            $inbox = Mage::getModel('eloombootstrap/inbox');
            $inbox->addMajor('Correios SRO - Erro ao consultar objeto', $message);

            $this->logger->error($message);
          }

          continue;
        }
        $lastEvent = $response->getObjeto()->getLastEvent();

        if (!$lastEvent) {
          continue;
        }
				if($this->logger->isDebugEnabled()) {
					$this->logger->debug('Status do Objeto ' . $sonda->getNumber() . ' - ' . $lastEvent->getTipoStatus());
				}

        if ($sonda->getStatus() != $lastEvent->getTipoStatus()) {
          $sonda->incrementAttempts();
          $sonda->setStatus($lastEvent->getTipoStatus());
          $sonda->setMessage($lastEvent->getDescricao());
          $sonda->save();

          // envia notificação por email
          if ($lastEvent->isTemplateEntregaRealizada()) {
            Mage::dispatchEvent('eloom_correiossro_email_encomenda_entregue', array('objeto' => $response->getObjeto(), 'order_id' => $sonda->getOrderId(), 'store_id' => $sonda->getStoreId()));
          } else if ($lastEvent->isTemplateLojista()) {
            $message = sprintf("Objeto [%s] - Status [%s]", $response->getObjeto()->getNumero(), $lastEvent->getDescricao());

            $inbox = Mage::getModel('eloombootstrap/inbox');
            $inbox->addMajor('Correios SRO', $message);

            $this->logger->error($message);
          } else {
            Mage::dispatchEvent('eloom_correiossro_email_status_entrega', array('objeto' => $response->getObjeto(), 'order_id' => $sonda->getOrderId(), 'store_id' => $sonda->getStoreId()));
          }

          Mage::dispatchEvent('eloom_correiossro_sms', array('order_id' => $sonda->getOrderId(), 'number' => $sonda->getNumber(), 'message' => $lastEvent->getDescricao()));
        }

        if ($sonda->canDelete($lastEvent)) {
          $sonda->delete();
        }
      } catch (Exception $exc) {
        $this->logger->fatal('Erro ao executar consulta com ID ' . $sonda->getId());
      }
    }
    $this->logger->info('Status de entrega - FIM');
  }

}

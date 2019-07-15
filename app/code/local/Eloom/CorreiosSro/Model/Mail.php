<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_Mail extends Mage_Core_Model_Abstract {

  private $logger;

  /**
   * Sonda states
   */
  public function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    parent::_construct();
  }

  public function sendEmailStatusEntrega(Eloom_Correios_Sro_Objeto $object, $orderId, $storeId) {
    $lastEvent = $object->getLastEvent();

		if($this->logger->isDebugEnabled()) {
			$this->logger->debug(sprintf("Enviando email. Objeto [%s] - Status [%s]", $object->getNumero(), $lastEvent->getDescricao()));
		}

    $order = Mage::getModel('sales/order')->load($orderId);
    $helper = Mage::helper('eloom_correiossro');
    $templateId = $helper->getTemplateStatusEntrega();
    $mailSubject = null;
    $senderId = $helper->getSender();
    $sender = array('name' => Mage::getStoreConfig("trans_email/ident_$senderId/name", $storeId), 'email' => Mage::getStoreConfig("trans_email/ident_$senderId/email", $storeId));

    $email = $order->getCustomerEmail();
    $emailArr = explode('@', $email);
    $pseudoName = $emailArr[0];

    $destino = null;
    if ($lastEvent->getDestino()) {
      $destino = sprintf("Unidade Operacional em %s / %s - Bairro %s", $lastEvent->getDestino()->getCidade(), $lastEvent->getDestino()->getUf(), $lastEvent->getDestino()->getBairro());
    }
    $vars = array('order' => $order,
        'billing' => $order->getBillingAddress(),
        'status' => $lastEvent->getDescricao(),
        'numero' => $object->getNumero(),
        'data' => $lastEvent->getData(),
        'hora' => $lastEvent->getHora(),
        'local' => $lastEvent->getLocal(),
        'cidade' => $lastEvent->getCidade(),
        'estado' => $lastEvent->getUf(),
        'destino' => (!is_null($destino) ? $destino : null),
        'comment' => null);

    $translate = Mage::getSingleton('core/translate');

    $mailTemplate = Mage::getModel('core/email_template');
    $mailTemplate->setTemplateSubject($mailSubject);

    $mailTemplate->sendTransactional($templateId, $sender, $email, $pseudoName, $vars, $storeId);
    $translate->setTranslateInLine(true);
  }

  public function sendEmailEncomendaEntregue(Eloom_Correios_Sro_Objeto $object, $orderId, $storeId) {
    $lastEvent = $object->getLastEvent();

		if($this->logger->isDebugEnabled()) {
			$this->logger->debug(sprintf("Enviando email de encomenda entregue. Objeto [%s] - Status [%s]", $object->getNumero(), $lastEvent->getDescricao()));
		}

    $order = Mage::getModel('sales/order')->load($orderId);
    $helper = Mage::helper('eloom_correiossro');
    $templateId = $helper->getTemplateEncomendaEntregue();
    $mailSubject = null;
    $senderId = $helper->getSender();
    $sender = array('name' => Mage::getStoreConfig("trans_email/ident_$senderId/name", $storeId), 'email' => Mage::getStoreConfig("trans_email/ident_$senderId/email", $storeId));

    $email = $order->getCustomerEmail();
    $emailArr = explode('@', $email);
    $pseudoName = $emailArr[0];

    $destino = null;
    if ($lastEvent->getDestino()) {
      $destino = sprintf("Unidade Operacional em %s / %s - Bairro %s", $lastEvent->getDestino()->getCidade(), $lastEvent->getDestino()->getUf(), $lastEvent->getDestino()->getBairro());
    }
    $vars = array('order' => $order,
        'billing' => $order->getBillingAddress(),
        'status' => $lastEvent->getDescricao(),
        'numero' => $object->getNumero(),
        'data' => $lastEvent->getData(),
        'hora' => $lastEvent->getHora(),
        'local' => $lastEvent->getLocal(),
        'cidade' => $lastEvent->getCidade(),
        'estado' => $lastEvent->getUf(),
        'destino' => (!is_null($destino) ? $destino : null),
        'comment' => null);

    $translate = Mage::getSingleton('core/translate');

    $mailTemplate = Mage::getModel('core/email_template');
    $mailTemplate->setTemplateSubject($mailSubject);
    $mailTemplate->sendTransactional($templateId, $sender, $email, $pseudoName, $vars, $storeId);
    $translate->setTranslateInLine(true);
  }

}

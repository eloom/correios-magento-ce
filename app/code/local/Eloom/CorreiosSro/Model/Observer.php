<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_Observer extends Mage_Core_Model_Abstract {

  private $logger;

  /**
   * Initialize resource model
   */
  protected function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    parent::_construct();
  }

  public function sendSms(Varien_Event_Observer $observer) {
    $helper = Mage::helper('eloom_correiossro');
    if (!$helper->isIagenteActive()) {
      return true;
    }
    $number = $observer->getEvent()->getNumber();
    $message = $observer->getEvent()->getMessage();
    $orderId = $observer->getEvent()->getOrderId();

    $order = Mage::getModel('sales/order')->load($orderId);
    if ($helper->isMobileInSalesOrder()) {
      $mobile = $order->getData($helper->getIagenteMobileField());
    } else {
      $mobile = $order->getBillingAddress()->getData($helper->getIagenteMobileField());
    }

    if (is_null($mobile)) {
      $inbox = Mage::getModel('eloombootstrap/inbox');
      $inbox->addMajor('Correios SRO - Celular não encontrado', sprintf("Celular não encontrado para pedido %s", $orderId));

      $this->logger->fatal(sprintf("Celular não encontrado para pedido %s", $orderId));
      return $this;
    }

    // valida celular
    $mobile = Mage::helper('eloombootstrap')->isValidMobilePhone($mobile);
    if (!$mobile) {
      $inbox = Mage::getModel('eloombootstrap/inbox');
      $inbox->addMajor('Correios SRO - Celular Inválido', sprintf("Celular inválido para pedido %s", $orderId));

      $this->logger->fatal(sprintf("Celular inválido para pedido %s", $orderId));
      return $this;
    }
    $sms = Mage::getModel('eloom_correiossro/iagente');
    $sms->sendSms($mobile, $number, $message);

    return $this;
  }

  public function sendEmailStatusEntrega(Varien_Event_Observer $observer) {
    $objeto = $observer->getEvent()->getObjeto();
    $orderId = $observer->getEvent()->getOrderId();
    $storeId = $observer->getEvent()->getStoreId();

    $mail = Mage::getModel('eloom_correiossro/mail');
    $mail->sendEmailStatusEntrega($objeto, $orderId, $storeId);

    return $this;
  }

  public function sendEmailEncomendaEntregue(Varien_Event_Observer $observer) {
    $objeto = $observer->getEvent()->getObjeto();
    $orderId = $observer->getEvent()->getOrderId();
    $storeId = $observer->getEvent()->getStoreId();

    $mail = Mage::getModel('eloom_correiossro/mail');
    $mail->sendEmailEncomendaEntregue($objeto, $orderId, $storeId);

    return $this;
  }

}

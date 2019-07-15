<?php

##eloom.licenca##

class Eloom_Correios_Model_Observer extends Mage_Core_Model_Abstract {

  private $logger;

  /**
   * Initialize resource model
   */
  protected function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    parent::_construct();
  }

  protected function _isValidForShipmentEmail($shipment) {
    $trackingNumbers = array();
    foreach ($shipment->getAllTracks() as $track) {
      $trackingNumbers[] = $track->getNumber();
    };
    if (count($trackingNumbers) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function salesOrderShipmentSaveBefore(Varien_Event_Observer $observer) {
    $shipment = $observer->getEvent()->getShipment();
    if ($shipment) {
      if ($this->_isValidForShipmentEmail($shipment)) {
        $shipment->setEmailSent(true);
      }
    }
    return $this;
  }

  public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer) {
    $shipment = $observer->getEvent()->getShipment();

    if ($shipment) {
      if ($this->_isValidForShipmentEmail($shipment)) {
        foreach ($shipment->getAllTracks() as $track) {
          if ($track->getCarrierCode() != Eloom_Correios_Model_Carrier::CODE) {
            continue;
          }
          Mage::getModel('eloom_correiossro/sonda')
              ->create()
              ->setNumber(trim($track->getNumber()))
              ->setOrderId(trim($track->getOrderId()))
              ->setStoreId(trim($track->getStoreId()))
              ->save();
        };

        $shipment->sendEmail();
      }
    }
    return $this;
  }

}

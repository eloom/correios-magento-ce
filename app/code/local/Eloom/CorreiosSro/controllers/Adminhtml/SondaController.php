<?php

##eloom.licenca##

class Eloom_CorreiosSro_Adminhtml_SondaController extends Mage_Adminhtml_Controller_Action {

  private $logger;

  /**
   * Initialize
   */
  protected function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
  }

  public function indexAction() {
    $sondaBlock = $this->getLayout()->createBlock('eloom_correiossro_adminhtml/sonda');
    $this->loadLayout()->_addContent($sondaBlock)->renderLayout();
  }

  public function trackingAction() {
    $number = $this->getRequest()->getParam('number', false);
    $localizacao = Mage::getModel('eloom_correiossro/localizacao');
    $response = $localizacao->localizaMercadoria($number, 'U');

    if ($response->hasError()) {
      $result = array('status' => 'error', 'message' => $response->getError());
    } else {
      $lastEvent = $response->getObjeto()->getLastEvent();
      $dataEntrega = str_replace('/', '-', $lastEvent->getData());

      $track = array(
          'deliverydate' => date('d-m-Y', strtotime($dataEntrega)),
          'deliverytime' => date('H:i', strtotime($lastEvent->getHora())),
          'deliverylocation' => $lastEvent->getCidade() . '&nbsp;/&nbsp;' . $lastEvent->getUf(),
          'status' => htmlentities($lastEvent->getDescricao()),
      );

      $result = array('status' => 'success', 'track' => $track);
    }

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  public function gridAction() {
    $this->loadLayout();
    $this->getResponse()->setBody($this->getLayout()->createBlock('eloom_correiossro_adminhtml/sonda_grid')->toHtml());
  }

  public function editAction() {
    $sonda = Mage::getModel('eloom_correiossro/sonda');
    if ($sondaId = $this->getRequest()->getParam('id', false)) {
      $sonda->load($sondaId);

      if (!$sonda->getId()) {
        $this->_getSession()->addError($this->__('Record not found.'));
        return $this->_redirect('eloom_correiossro/sonda/index');
      }
    }

    if ($postData = $this->getRequest()->getPost('localizadorData')) {
      try {
        $sonda->addData($postData);

        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection');
        $shipmentCollection->addAttributeToFilter('order_id', $sonda->getOrderId());

        foreach ($shipmentCollection as $sc) {
          $shipment = Mage::getModel('sales/order_shipment');
          $shipment->load($sc->getId());

          if ($shipment->getId() != '') {
            $trackingCollection = Mage::getResourceModel('sales/order_shipment_track_collection');
            $trackingCollection->addAttributeToFilter('parent_id', $shipment->getId());

            foreach ($trackingCollection as $tc) {
              $track = Mage::getModel('sales/order_shipment_track');
              $track->load($tc->getId());

              if ($track->getId() != '' && $track->getCarrierCode() == Eloom_Correios_Model_Carrier::CODE) {
                $track->setData('track_number', $sonda->getNumber());
                $track->save();
              }
            }
          }
        }

        $sonda->save();

        $this->_getSession()->addSuccess($this->__('The record has been saved.'));

        return $this->_redirect('eloom_correiossro/sonda/index', array('id' => $sonda->getId()));
      } catch (Exception $e) {
        $this->logger->fatal($e);
        $this->_getSession()->addError($e->getMessage());
      }
    }

    Mage::register('current_sonda', $sonda);
    $sondaEditBlock = $this->getLayout()->createBlock('eloom_correiossro_adminhtml/sonda_edit');
    $this->loadLayout()->_addContent($sondaEditBlock)->renderLayout();
  }

  public function deleteAction() {
    $sonda = Mage::getModel('eloom_correiossro/sonda');

    if ($sondaId = $this->getRequest()->getParam('id', false)) {
      $sonda->load($sondaId);
    }

    if (!$sonda->getId()) {
      $this->_getSession()->addError($this->__('Record not found.'));
      return $this->_redirect('eloom_correiossro/sonda/index');
    }

    try {
      $sonda->delete();

      $this->_getSession()->addSuccess($this->__('The record has been deleted.'));
    } catch (Exception $e) {
      Mage::logException($e);
      $this->_getSession()->addError($e->getMessage());
    }

    return $this->_redirect('eloom_correiossro/sonda/index');
  }

  /**
   * Thanks to Ben for pointing out this method was missing. Without
   * this method the ACL rules configured in adminhtml.xml are ignored.
   */
  protected function _isAllowed() {
    /**
     * we include this switch to demonstrate that you can add action
     * level restrictions in your ACL rules. The isAllowed() method will
     * use the ACL rule we have configured in our adminhtml.xml file:
     * - acl
     * - - resources
     * - - - admin
     * - - - - children
     * - - - - - eloom_correiossro
     * - - - - - - children
     * - - - - - - - sonda
     *
     * eg. you could add more rules inside sonda for edit and delete.
     */
    $actionName = $this->getRequest()->getActionName();
    switch ($actionName) {
      case 'index':
      case 'edit':
      case 'delete':
      default:
        $adminSession = Mage::getSingleton('admin/session');
        $isAllowed = $adminSession->isAllowed('eloom_correiossro/sonda');
        break;
    }

    return $isAllowed;
  }

}

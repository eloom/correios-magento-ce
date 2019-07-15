<?php

##eloom.licenca##

class Eloom_Correios_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

  const CODE = 'eloom_correios';
  const COUNTRY = 'BR';

  /**
   * Código de erro para CEP de origem não pode postar para o CEP de destino informado.
   * @var type 
   */
  const CODE_SAME_CEP = '-888';

  /**
   * Código do Método de Frete
   * @var type
   */
  protected $_code = self::CODE;
  protected $_result = null;
  protected $_fromZip = null;
  protected $_toZip = null;
  protected $_helper = null;
  protected $_freeMethod = null;
  protected $_hasFreeMethod = false;
  // dimensoes
  protected $_nVlComprimento = 0;
  protected $_nVlAltura = 0;
  protected $_nVlLargura = 0;

  /**
   *
   * @var type boolean
   */
  private $_hasErrorSameCEP = false;

	/**
	 * 011 - O CEP de destino está sujeito a condições especiais de entrega  pela  ECT e será realizada com o acréscimo de até 7 (sete) dias úteis ao prazo regular.
	 *
	 * @var array
	 */
  private $_skipErrors = array('010','011');

  /**
   * 
   * @var type
   */
  protected $_freeMethodSameCEP = null;

  protected $_reverse = array('4510' => '04510', '4669' => '04669', '4014' => '04014', '4162' => '04162');

  /**
   * 
   * @var type
   */
  private $logger;

  /**
   * Initialize resource model
   */
  public function __construct() {
    parent::__construct();
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    $this->_helper = Mage::helper('eloom_correios');
  }

  /**
   * Make initial checks and iniciate module variables
   *
   * @param Mage_Shipping_Model_Rate_Request $request
   *
   * @return bool
   */
  protected function _check(Mage_Shipping_Model_Rate_Request $request) {
    if (!$this->getConfigFlag('active')) {
      return false;
    }

    $this->_result = Mage::getModel('shipping/rate_result');
    $origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
    $destCountry = $request->getDestCountryId();
    if ($origCountry != self::COUNTRY || $destCountry != self::COUNTRY) {
      $rate = Mage::getModel('shipping/rate_result_error');
      $rate->setCarrier($this->_code);
      $rate->setCarrierTitle($this->getConfigData('title'));
      $rate->setErrorMessage(Eloom_Correios_ErrorMessages::getMessage('002'));
      $this->_result->append($rate);

      return false;
    }
    $this->_fromZip = Mage::getStoreConfig('shipping/origin/postcode', $this->getStore());
    $this->_toZip = $request->getDestPostcode();

    // Fix ZIP code
    $this->_fromZip = str_replace(array('-', '.'), '', trim($this->_fromZip));
    $this->_toZip = str_replace(array('-', '.'), '', trim($this->_toZip));
    $this->_toZip = str_replace('-', '', $this->_toZip);

    if (!preg_match('/^([0-9]{8})$/', $this->_fromZip)) {
      $rate = Mage::getModel('shipping/rate_result_error');
      $rate->setCarrier($this->_code);
      $rate->setCarrierTitle($this->getConfigData('title'));
      $rate->setErrorMessage(Eloom_Correios_ErrorMessages::getMessage('003'));
      $this->_result->append($rate);

      return false;
    }
    $price = 0;
    $weight = 0;

    if ($request->getPackageValue() > 0 && $request->getPackageWeight() > 0) {
      $price = $request->getPackageValue();
      $weight = $request->getPackageWeight();
    } else if ($request->getAllItems()) {
      foreach ($request->getAllItems() as $item) {
        if ($item->getProduct()->isVirtual()) {
          continue;
        }

        if ($item->getHasChildren() && $item->isShipSeparately()) {
          foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
              $product_id = $child->getProductId();
              $product = Mage::getModel('catalog/product');
              $product->setStoreId($this->getStore());
              $product->load($product_id);

              $price += (float) (!is_null($product->getData('special_price')) ? $product->getData('special_price') : $product->getData('price'));
              $weight += (float) $product->getData('weight');
            }
          }
        } else {
          $product_id = $item->getProductId();
          $product = Mage::getModel('catalog/product');
          $product->setStoreId($this->getStore());
          $product->load($product_id);
          if ($product->getTypeId() == 'simple') {
            $price += (float) (!is_null($product->getData('special_price')) ? $product->getData('special_price') : $product->getData('price'));
          }

          if ($product->getTypeId() == 'simple') {
            $weight += (float) $product->getData('weight');
          }
        }
      }
    }
    $this->_nVlAltura = $this->_helper->getAlturaPadrao();
    $this->_nVlLargura = $this->_helper->getLarguraPadrao();
    $this->_nVlComprimento = $this->_helper->getComprimentoPadrao();

    $this->_hasFreeMethod = $request->getFreeShipping();
    $this->_freeMethod = $this->_helper->getServicoGratuito();

    $this->_packageValue = $request->getBaseCurrency()->convert(
            $price, $request->getPackageCurrency()
    );
    $this->_packageWeight = number_format($weight, 2, '.', '');
    $this->_freeMethodWeight = number_format($request->getFreeMethodWeight(), 2, '.', '');
  }

  public function getAllowedMethods() {
    $allowed = explode(',', $this->getConfigData('cd_servico'));
    $arr = array();
    foreach ($allowed as $k) {
      $arr[$k] = $this->getCode('service', $k);
    }
    return $arr;
  }

  public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
    if ($this->_check($request) === false) {
      return $this->_result;
    }
    if (!preg_match('/^([0-9]{8})$/', $this->_toZip)) {
      return $this->_result;
    }

    if ($this->_getRates()->getError()) {
      return $this->_result;
    }

    return $this->_result;
  }

  protected function _getRates() {
    $cotacaoFrete = Mage::getModel('eloom_correios/cotacaoFrete');

    $calcPrecoPrazo = new Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazo();
    $calcPrecoPrazo->sCepOrigem = $this->_fromZip;
    $calcPrecoPrazo->sCepDestino = $this->_toZip;

    if ($this->_volumeWeight > $this->getConfigData('volume_weight_min') && $this->_volumeWeight > $this->_packageWeight) {
      $calcPrecoPrazo->nVlPeso = $this->_volumeWeight;
    } else {
      $calcPrecoPrazo->nVlPeso = $this->_packageWeight;
    }

    if ($this->_helper->isValorDeclarado()) {
      $calcPrecoPrazo->nVlValorDeclarado = $this->_packageValue;
    } else {
      $calcPrecoPrazo->nVlValorDeclarado = 0;
    }
    $calcPrecoPrazo->nVlComprimento = $this->_nVlComprimento;
    $calcPrecoPrazo->nVlAltura = $this->_nVlAltura;
    $calcPrecoPrazo->nVlLargura = $this->_nVlLargura;
    $calcPrecoPrazo->nVlDiametro = 0;

    $response = $cotacaoFrete->calcularFrete($calcPrecoPrazo);
    if ($response == null) {
      $rate = Mage::getModel('shipping/rate_result_error');
      $rate->setCarrier($this->_code);
      $rate->setCarrierTitle($this->getConfigData('title'));
      $rate->setErrorMessage(Eloom_Correios_ErrorMessages::getMessage('001'));
      $this->_result->append($rate);

      return $this->_result;
    }
    
    $this->_checkErrorSameCep($response);

    // adiciona os serviços
    if ($response && is_array($response->cServico)) {
      foreach ($response->cServico as $servico) {
        $this->_appendService($servico);
      }
    } else if ($response && $response->cServico instanceof stdClass) {
      $this->_appendService($response->cServico);
    }

    return $this->_result;
  }

  /**
   * Verifica se tem serviço com erro de CEP com 
   * 
   * @param type $response
   */
  private function _checkErrorSameCep($response) {
    if ($response && is_array($response->cServico)) {
      foreach ($response->cServico as $servico) {
        if ($servico->Erro == self::CODE_SAME_CEP) {
          $this->_hasErrorSameCEP = true;
        }
      }
    } else if ($response && $response->cServico instanceof stdClass) {
      $this->_hasErrorSameCEP = true;
    }
    // pega o serviço com menor valor
    if ($this->_hasErrorSameCEP) {
      $cheapValue = null;

      if ($response && is_array($response->cServico)) {
        foreach ($response->cServico as $servico) {
          if ($servico->Erro == '0') {
            $v = floatval(str_replace(',', '.', (string) $servico->Valor));
            if ($cheapValue == null || $v < $cheapValue) {
              $cheapValue = $v;
              $this->_freeMethodSameCEP = (isset($this->_reverse[$servico->Codigo]) ? $this->_reverse[$servico->Codigo] : $servico->Codigo);
            }
          }
        }
      } else if ($response && $response->cServico instanceof stdClass) {
        if ($servico->Erro == '0') {
          $v = floatval(str_replace(',', '.', (string) $servico->Valor));
          if ($cheapValue == null || $v < $cheapValue) {
            $cheapValue = $v;
            $this->_freeMethodSameCEP = (isset($this->_reverse[$servico->Codigo]) ? $this->_reverse[$servico->Codigo] : $servico->Codigo);
          }
        }
      }
    }
  }

  private function _appendService($servico) {
    $rate = null;
		$method = (isset($this->_reverse[$servico->Codigo]) ? $this->_reverse[$servico->Codigo] : $servico->Codigo);
    if ($servico->Erro != '0' && !in_array($servico->Erro, $this->_skipErrors)) {
      if ($this->getConfigData('showmethod')) {
        $title = $this->getCode('front', $method);

        $rate = Mage::getModel('shipping/rate_result_error');
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setErrorMessage($title . ' - ' . $servico->MsgErro);
      }
    } else {
      $rate = Mage::getModel('shipping/rate_result_method');
      $rate->setCarrier($this->_code);
      $rate->setCarrierTitle($this->getConfigData('title'));
      $rate->setMethod($method);

      $title = $this->getCode('front', $method);
      if ($this->_helper->isShowPrazoEntrega()) {
        $s = $this->_helper->getMensagemPrazoEntrega();
        $title = sprintf($s, $title, intval($servico->PrazoEntrega + $this->_helper->getPrazoExtra()));
      }
      if ($servico->obsFim != '') {
        $title = $title . ' [' . $servico->obsFim . ']';
      }
      $title = substr($title, 0, 255);
      $rate->setMethodTitle($title);

      if ($this->_helper->hasTaxaExtra()) {
        $v1 = floatval(str_replace(',', '.', (string) $this->_helper->getValorTaxaExtra()));
        $v2 = floatval(str_replace(',', '.', (string) $servico->Valor));

        if ($this->_helper->isTaxaExtraInValor()) {
          $rate->setPrice($v1 + $v2);
        } else if ($this->_helper->isTaxaExtraInPercentual()) {
          $rate->setPrice($v2 + (($v1 * $v2) / 100));
        }
      } else {
        $rate->setPrice(floatval(str_replace(',', '.', (string) $servico->Valor)));
      }
      if ($this->_hasFreeMethod) {
        if ($method == $this->_freeMethod) {
          $rate->setPrice(0);
        }
        if ($method == $this->_freeMethodSameCEP) {
          $rate->setPrice(0);
        }
      }

      $rate->setCost(0);
    }

    $this->_result->append($rate);
  }

  /**
   * Check if current carrier offer support to tracking
   *
   * @return bool true
   */
  public function isTrackingAvailable() {
    return true;
  }

  /**
   * Get Tracking Info
   *
   * @param mixed $tracking
   *
   * @return mixed
   */
  public function getTrackingInfo($tracking) {
    $result = $this->getTracking($tracking);
    if ($result instanceof Mage_Shipping_Model_Tracking_Result) {
      if ($trackings = $result->getAllTrackings()) {
        return $trackings[0];
      }
    } elseif (is_string($result) && !empty($result)) {
      return $result;
    }

    return false;
  }

  /**
   * Get Tracking
   *
   * @param array $trackings
   *
   * @return Mage_Shipping_Model_Tracking_Result
   */
  public function getTracking($trackings) {
    $this->_result = Mage::getModel('shipping/tracking_result');
    foreach ((array) $trackings as $code) {
      $this->_getTracking($code);
    }

    return $this->_result;
  }

  /**
   * Protected Get Tracking, opens the request to Correios
   *
   * @param string $nf
   *
   * @return bool
   */
  protected function _getTracking($nf) {
    $localizacao = Mage::getModel('eloom_correiossro/localizacao');
    $response = $localizacao->localizaMercadoria($nf, 'T');

    if ($response == null || $response == '' || $response->hasError()) {
      $error = Mage::getModel('shipping/tracking_result_error');
      $error->setTracking($nf);
      $error->setCarrier($this->_code);
      $error->setCarrierTitle($this->getConfigData('title'));

			if($response != null && $response->getError()) {
				$error->setErrorMessage($response->getError());
			}

      $this->_result->append($error);
    } else {
      $lastEvent = $response->getObjeto()->getLastEvent();
      $dataEntrega = str_replace('/', '-', $lastEvent->getData());

      $track = array(
          'deliverydate' => date('d-m-Y', strtotime($dataEntrega)),
          'deliverytime' => date('H:i', strtotime($lastEvent->getHora())),
          'deliverylocation' => $lastEvent->getCidade() . '&nbsp;/&nbsp;' . $lastEvent->getUf(),
          'status' => htmlentities($lastEvent->getDescricao()),
          'progressdetail' => $this->_eventsAsString($response->getObjeto()),
      );
      $tracking = Mage::getModel('shipping/tracking_result_status');
      $tracking->setTracking($nf);
      $tracking->setCarrier($this->_code);
      $tracking->setCarrierTitle($this->getConfigData('title'));
      $tracking->addData($track);

      $this->_result->append($tracking);
    }
  }

  private function _eventsAsString($objeto) {
    $detail = array();
    foreach ($objeto->getEventos() as $event) {
      $dataEntrega = str_replace('/', '-', $event->getData());
      $destino = $event->getDestino();

      $detail[] = array(
          'deliverydate' => date('d-m-Y', strtotime($dataEntrega)),
          'deliverytime' => $event->getHora(),
          'deliverylocation' => $event->getCidade() . '&nbsp;/&nbsp;' . $event->getUf(),
          'activity' => $event->getDescricao() . (!is_null($destino) ? sprintf(' (Unidade Operacional em %s / %s)', $destino->getCidade(), $destino->getUf()) : ''),
      );
    }

    return $detail;
  }

  /**
   * Get configuration data of carrier
   *
   * @param string $type
   * @param string $code
   * @return array|bool
   */
  public function getCode($type, $code = '') {
    static $codes;
    $codes = array(
        'service' => array(
            '04510' => $this->_helper->__('04510 - PAC sem contrato'),
            '04669' => $this->_helper->__('04669 - PAC com contrato'),
            '04014' => $this->_helper->__('04014 - SEDEX sem contrato'),
            '04162' => $this->_helper->__('04162 - SEDEX com contrato'),
            '40045' => $this->_helper->__('40045 - SEDEX a Cobrar, sem contrato'),
            '40126' => $this->_helper->__('40126 - SEDEX a Cobrar, com contrato'),
            '40215' => $this->_helper->__('40215 - SEDEX 10, sem contrato'),
            '40290' => $this->_helper->__('40290 - SEDEX Hoje, sem contrato'),
        ),
        'front' => array(
            '04510' => $this->_helper->__('PAC'),
            '04669' => $this->_helper->__('PAC'),
            '04014' => $this->_helper->__('SEDEX'),
            '04162' => $this->_helper->__('SEDEX'),
            '40045' => $this->_helper->__('SEDEX a Cobrar'),
            '40126' => $this->_helper->__('SEDEX a Cobrar'),
            '40215' => $this->_helper->__('SEDEX 10'),
            '40290' => $this->_helper->__('SEDEX Hoje'),
        )
    );

    if (!isset($codes[$type])) {
      return false;
    } elseif ('' === $code) {
      return $codes[$type];
    }

    if (!isset($codes[$type][$code])) {
      return false;
    } else {
      return $codes[$type][$code];
    }
  }

}

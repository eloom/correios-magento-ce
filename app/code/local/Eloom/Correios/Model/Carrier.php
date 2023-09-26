<?php

##eloom.licenca##

use Eloom\SdkCorreios\Client;
use Eloom\SdkCorreios\Endpoints\Rastro;
use Eloom\SdkCorreios\Exceptions\UnauthorizedException;

class Eloom_Correios_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

	const CODE = 'eloom_correios';
	const COUNTRY = 'BR';

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
	 * 011 - O CEP de destino está sujeito a condições especiais de entrega  pela  ECT e será realizada com o acréscimo de até 7 (sete) dias úteis ao prazo regular.
	 *
	 * @var array
	 */
	private $_skipErrors = array('010', '011');

	/**
	 *
	 * @var type
	 */
	protected $_freeMethodSameCEP = null;

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

							$price += (float)(!is_null($product->getData('special_price')) ? $product->getData('special_price') : $product->getData('price'));
							$weight += (float)$product->getData('weight');
						}
					}
				} else {
					$product_id = $item->getProductId();
					$product = Mage::getModel('catalog/product');
					$product->setStoreId($this->getStore());
					$product->load($product_id);
					if ($product->getTypeId() == 'simple') {
						$price += (float)(!is_null($product->getData('special_price')) ? $product->getData('special_price') : $product->getData('price'));
					}

					if ($product->getTypeId() == 'simple') {
						$weight += (float)$product->getData('weight');
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
		$helper = Mage::helper('eloom_correios');

		/**
		 * Autentica
		 */
		$client = new Client($helper->getUsuario(), $helper->getCodigoAcesso());
		try {
			$client->autentica()->cartaoPostagem($helper->getCartaoPostagem());
		} catch (UnauthorizedException $exception) {
			$rate = Mage::getModel('shipping/rate_result_error');
			$rate->setCarrier($this->_code);
			$rate->setCarrierTitle($this->getConfigData('title'));
			$rate->setErrorMessage($exception->getMessage());
			$this->_result->append($rate);

			return $this->_result;
		}
		
		$codigoServicos = explode(',', $helper->getCodigoServicos());

		/**
		 * Prazo
		 */
		$prazoClient = $client->prazo();
		foreach ($codigoServicos as $servico) {
			$prazoClient->withProduct($servico);
		}
		$prazos = $prazoClient->withCepOrigem($this->_fromZip)->withCepDestino($this->_toZip)->nacional();

		/**
		 * Preço
		 */
		$precoClient = $client->preco();
		foreach ($codigoServicos as $servico) {
			$precoClient->withProduct($servico);
		}
		$precoClient->withCepOrigem($this->_fromZip)->withCepDestino($this->_toZip);
		$precoClient->withDiametro(0)->withAltura($this->_nVlAltura)->withLargura($this->_nVlLargura)->withComprimento($this->_nVlComprimento);

		$nVlPeso = 0;
		if ($this->_volumeWeight > $this->getConfigData('volume_weight_min') && $this->_volumeWeight > $this->_packageWeight) {
			$nVlPeso = $this->_volumeWeight;
		} else {
			$nVlPeso = $this->_packageWeight;
		}
		$precoClient->withPsObjeto($nVlPeso);

		if ($this->_helper->isValorDeclarado()) {
			$precoClient->withVlDeclarado($this->_packageValue);
		}

		$precoClient->withTpObjeto($helper->getCodigoFormato());

		$precos = $precoClient->nacional();

		/**
		 * Merge
		 */
		$services = [];
		foreach($precos as $preco) {
			$services[$preco->coProduto] = $preco; 
		}

		foreach($prazos as $prazo) {
			$service = $services[$prazo->coProduto];
			$service->prazoEntrega = $prazo->prazoEntrega;
			$service->entregaDomiciliar = $prazo->entregaDomiciliar;
			$service->entregaSabado = $prazo->entregaSabado;
			$service->msgPrazo = $prazo->msgPrazo;

			$services[$prazo->coProduto] = $service;
		}

		foreach($services as $service) {
			$this->_appendService($service);
		}
		
		return $this->_result;
	}

	private function _appendService($servico) {
		$rate = null;
		$method = $servico->coProduto;

		if ($servico->txErro && !in_array($servico->txErro, $this->_skipErrors)) {
			if ($this->getConfigData('showmethod')) {
				$title = $this->getCode('front', $method);

				$rate = Mage::getModel('shipping/rate_result_error');
				$rate->setCarrier($this->_code);
				$rate->setCarrierTitle($this->getConfigData('title'));
				$rate->setErrorMessage($title . ' - ' . $servico->txErro);
			}
		} else {
			$rate = Mage::getModel('shipping/rate_result_method');
			$rate->setCarrier($this->_code);
			$rate->setCarrierTitle($this->getConfigData('title'));
			$rate->setMethod($method);

			$title = $this->getCode('front', $method);
			if ($this->_helper->isShowPrazoEntrega()) {
				$s = $this->_helper->getMensagemPrazoEntrega();
				$title = sprintf($s, $title, intval($servico->prazoEntrega + $this->_helper->getPrazoExtra()));
			}
			if ($servico->msgPrazo) {
				$title = $title . ' [' . $servico->msgPrazo . ']';
			}
			$title = substr($title, 0, 250);
			$rate->setMethodTitle($title);

			if ($this->_helper->hasTaxaExtra()) {
				$v1 = floatval(str_replace(',', '.', (string)$this->_helper->getValorTaxaExtra()));
				$v2 = floatval(str_replace(',', '.', (string)$servico->pcFinal));

				if ($this->_helper->isTaxaExtraInValor()) {
					$rate->setPrice($v1 + $v2);
				} else if ($this->_helper->isTaxaExtraInPercentual()) {
					$rate->setPrice($v2 + (($v1 * $v2) / 100));
				}
			} else {
				$rate->setPrice(floatval(str_replace(',', '.', (string)$servico->pcFinal)));
			}
			if ($this->_hasFreeMethod) {
				if ($method == $this->_freeMethod) {
					$v1 = floatval(str_replace(',', '.', (string)$this->_helper->getDescontoServicoGratuito()));
					$p = $rate->getPrice();
					if ($v1 > 0 && $v1 > $p) {
						$rate->setPrice(0);
					}
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
		foreach ((array)$trackings as $code) {
			$this->_getTracking($code);
		}

		return $this->_result;
	}

	protected function _getTracking($trackingNumber) {
		$helper = Mage::helper('eloom_correios');
		/**
		 * Autentica
		 */
		$client = new Client($helper->getUsuario(), $helper->getCodigoAcesso());
		try {
			$client->autentica()->cartaoPostagem($helper->getCartaoPostagem());
		} catch (UnauthorizedException $exception) {
			$error = Mage::getModel('shipping/tracking_result_error');
			$error->setTracking($trackingNumber);
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('title'));

			$error->setErrorMessage($exception->getMessage());

			$this->_result->append($error);
		}

		$objetos = $client->rastro()->withCodigoObjeto($trackingNumber)->withResultado(Rastro::EVENTOS_TODOS)->objeto();
		$objeto = $objetos->objetos[0];

		if (isset($objeto->mensagem)) {
			$error = Mage::getModel('shipping/tracking_result_error');
			$error->setTracking($trackingNumber);
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('title'));

			$error->setErrorMessage($objeto->mensagem);

			$this->_result->append($error);
		} else {
			$lastEvent = $objeto->eventos[0];
			$endereco = $lastEvent->unidade->endereco;

			$track = array(
				'deliverydate' => date('d-m-Y', strtotime($lastEvent->dtHrCriado)),
				'deliverytime' => date('H:i', strtotime($lastEvent->dtHrCriado)),
				'deliverylocation' => (isset($endereco->cidade) ? $endereco->cidade . '&nbsp;/&nbsp;' . $endereco->uf : ''),
				'status' => htmlentities($lastEvent->descricao),
				'progressdetail' => $this->_eventsAsString($objeto->eventos),
			);
			$tracking = Mage::getModel('shipping/tracking_result_status');
			$tracking->setTracking($objeto->codObjeto);
			$tracking->setCarrier($this->_code);
			$tracking->setCarrierTitle($this->getConfigData('title'));
			$tracking->addData($track);

			$this->_result->append($tracking);
		}
	}

	private function _eventsAsString($eventos) {
		$detail = array();
		foreach ($eventos as $event) {
			$endereco = $event->unidade->endereco;

			$detail[] = array(
				'deliverydate' => date('d-m-Y', strtotime($event->dtHrCriado)),
				'deliverytime' => date('H:i', strtotime($event->dtHrCriado)),
				'deliverylocation' => (isset($endereco->cidade) ? $endereco->cidade . '&nbsp;/&nbsp;' . $endereco->uf : ''),
				'activity' => $event->descricao . (isset($endereco->cidade) ? sprintf(' (Unidade Operacional em %s / %s)', $endereco->cidade, $endereco->uf) : '')
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
				'03085' => $this->_helper->__('03085 - PAC com contrato'),
				'03298' => $this->_helper->__('03298 - PAC com contrato'),
				'04669' => $this->_helper->__('04669 - PAC com contrato'),
				'03050' => $this->_helper->__('03050 - SEDEX com contrato'),
				'03220' => $this->_helper->__('03220 - SEDEX com contrato'),
				'04162' => $this->_helper->__('04162 - SEDEX com contrato'),
				'40126' => $this->_helper->__('40126 - SEDEX a Cobrar, com contrato')
			),
			'front' => array(
				'03085' => $this->_helper->__('PAC'),
				'03298' => $this->_helper->__('PAC'),
				'04669' => $this->_helper->__('PAC'),
				'03050' => $this->_helper->__('SEDEX'),
				'03220' => $this->_helper->__('SEDEX'),
				'04162' => $this->_helper->__('SEDEX'),
				'40126' => $this->_helper->__('SEDEX a Cobrar')
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

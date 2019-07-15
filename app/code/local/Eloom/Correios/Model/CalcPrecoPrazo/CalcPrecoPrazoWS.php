<?php

##eloom.licenca##

class Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoWS extends \SoapClient {

	const TIMEOUT = 10;
  /**
   * @var array $classmap The defined classes
   * @access private
   */
  private static $classmap = array(
      'CalcPrecoPrazo' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazo',
      'CalcPrecoPrazoResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoResponse',
      'Resultado' => '\Eloom_Correios_Model_CalcPrecoPrazo_Resultado',
      'Servico' => '\Eloom_Correios_Model_CalcPrecoPrazo_Servico',
      'CalcPrecoPrazoData' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoData',
      'CalcPrecoPrazoDataResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoDataResponse',
      'CalcPrecoPrazoRestricao' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoRestricao',
      'CalcPrecoPrazoRestricaoResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoRestricaoResponse',
      'CalcPreco' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPreco',
      'CalcPrecoResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoResponse',
      'CalcPrecoData' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoData',
      'CalcPrecoDataResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoDataResponse',
      'CalcPrazo' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazo',
      'CalcPrazoResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoResponse',
      'CalcPrazoData' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoData',
      'CalcPrazoDataResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoDataResponse',
      'CalcPrazoRestricao' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoRestricao',
      'CalcPrazoRestricaoResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoRestricaoResponse',
      'CalcPrecoFAC' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoFAC',
      'CalcPrecoFACResponse' => '\Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoFACResponse');

  /**
   * @param array $options A array of config values
   * @param string $wsdl The wsdl file to use
   * @access public
   */
  public function __construct(array $options = array(), $wsdl = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?wsdl') {
    foreach (self::$classmap as $key => $value) {
      if (!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }

		ini_set('default_socket_timeout', self::TIMEOUT);
		parent::__construct($wsdl, $options);
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazo $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoResponse
   */
  public function CalcPrecoPrazo(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazo $parameters) {
    return $this->__soapCall('CalcPrecoPrazo', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoData $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoDataResponse
   */
  public function CalcPrecoPrazoData(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoData $parameters) {
    return $this->__soapCall('CalcPrecoPrazoData', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoRestricao $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoRestricaoResponse
   */
  public function CalcPrecoPrazoRestricao(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoPrazoRestricao $parameters) {
    return $this->__soapCall('CalcPrecoPrazoRestricao', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPreco $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoResponse
   */
  public function CalcPreco(Eloom_Correios_Model_CalcPrecoPrazo_CalcPreco $parameters) {
    return $this->__soapCall('CalcPreco', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoData $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoDataResponse
   */
  public function CalcPrecoData(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoData $parameters) {
    return $this->__soapCall('CalcPrecoData', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazo $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoResponse
   */
  public function CalcPrazo(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazo $parameters) {
    return $this->__soapCall('CalcPrazo', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoData $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoDataResponse
   */
  public function CalcPrazoData(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoData $parameters) {
    return $this->__soapCall('CalcPrazoData', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoRestricao $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoRestricaoResponse
   */
  public function CalcPrazoRestricao(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrazoRestricao $parameters) {
    return $this->__soapCall('CalcPrazoRestricao', array($parameters));
  }

  /**
   * @param Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoFAC $parameters
   * @access public
   * @return Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoFACResponse
   */
  public function CalcPrecoFAC(Eloom_Correios_Model_CalcPrecoPrazo_CalcPrecoFAC $parameters) {
    return $this->__soapCall('CalcPrecoFAC', array($parameters));
  }

}

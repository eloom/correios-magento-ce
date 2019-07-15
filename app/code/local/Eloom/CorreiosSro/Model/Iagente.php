<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_Iagente extends Mage_Core_Model_Abstract {

	private $logger;

	/**
	 * Initialize resource model
	 */
	public function __construct() {
		$this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
	}

	public function sendSms($mobile, $number, $mensagem) {
		$message = sprintf("Correios(%s) - %s", $number, iconv('UTF-8', 'ISO-8859-1', $mensagem));
		$helper = Mage::helper('eloom_correiossro');

		try {
			$client = new Zend_Http_Client($helper->getIagenteUrl());
			$client->setParameterGet('metodo', 'envio');
			$client->setParameterGet('usuario', trim($helper->getIagenteUsuario()));
			$client->setParameterGet('senha', trim($helper->getIagenteSenha()));
			$client->setParameterGet('celular', $mobile);
			$client->setParameterGet('mensagem', $message);

			$content = $client->request()->getBody();
			if ($content != 'OK') {
				$inbox = Mage::getModel('eloombootstrap/inbox');
				$inbox->addMajor('Correios SRO - Erro ao enviar SMS', $content);

				$this->logger->warn(sprintf("Número: %s - Mensagem: %s", $mobile, $content));
			} else {
				if ($this->logger->isDebugEnabled()) {
					$this->logger->debug(sprintf("Envio confirmado para %s.", $mobile));
				}
			}
		} catch(Exception $exc) {
			$this->logger->fatal('Erro ao enviar SMS. Número ' . $mobile);
		}

		return null;
	}

}

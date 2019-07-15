<?php

##eloom.licenca##

class Eloom_CorreiosSro_Helper_Data extends Mage_Core_Helper_Abstract
{

	const XML_ACTIVE = 'eloom_correiossro/general/active';
	const XML_USUARIO = 'eloom_correiossro/general/usuario';
	const XML_SENHA = 'eloom_correiossro/general/senha';
	const XML_URL = 'eloom_correiossro/general/url';
	const XML_SENDER = 'eloom_correiossro/general/identity';
	const XML_WRITE_LOG = 'eloom_correiossro/general/writelog';

	/**
	 * Templates
	 */
	const XML_TEMPLATE_STATUS_ENTREGA = 'eloom_correiossro/template/status_entrega';
	const XML_TEMPLATE_ENCOMENDA_ENTREGUE = 'eloom_correiossro/template/encomenda_entregue';

	/**
	 *
	 */
	const XML_IAGENT_ACTIVE = 'eloom_correiossro/iagente/active';
	const XML_IAGENT_USUARIO = 'eloom_correiossro/iagente/usuario';
	const XML_IAGENT_SENHA = 'eloom_correiossro/iagente/senha';
	const XML_IAGENT_MOBILE_LOCAL = 'eloom_correiossro/iagente/mobile_local';
	const XML_IAGENT_MOBILE_FIELD = 'eloom_correiossro/iagente/mobile_field';
	const XML_IAGENT_URL = 'eloom_correiossro/iagente/url';

	public function isActive($storeId)
	{
		return Mage::getStoreConfigFlag(self::XML_ACTIVE, $storeId);
	}

	public function isWriteLog()
	{
		return Mage::getStoreConfigFlag(self::XML_WRITE_LOG);
	}

	public function getTemplateStatusEntrega()
	{
		return Mage::getStoreConfig(self::XML_TEMPLATE_STATUS_ENTREGA);
	}

	public function getTemplateEncomendaEntregue()
	{
		return Mage::getStoreConfig(self::XML_TEMPLATE_ENCOMENDA_ENTREGUE);
	}

	public function getSender()
	{
		return Mage::getStoreConfig(self::XML_SENDER);
	}

	public function getUsuario()
	{
		return Mage::getStoreConfig(self::XML_USUARIO);
	}

	public function getSenha()
	{
		return Mage::getStoreConfig(self::XML_SENHA);
	}

	public function getUrl()
	{
		return Mage::getStoreConfig(self::XML_URL);
	}

	/**
	 *
	 * @return
	 */
	public function isIagenteActive()
	{
		return Mage::getStoreConfigFlag(self::XML_IAGENT_ACTIVE);
	}

	public function getIagenteUsuario()
	{
		return Mage::getStoreConfig(self::XML_IAGENT_USUARIO);
	}

	public function getIagenteSenha()
	{
		return Mage::getStoreConfig(self::XML_IAGENT_SENHA);
	}

	public function getIagenteUrl()
	{
		return Mage::getStoreConfig(self::XML_IAGENT_URL);
	}

	public function getIagenteMobileLocal()
	{
		return Mage::getStoreConfig(self::XML_IAGENT_MOBILE_LOCAL);
	}

	public function getIagenteMobileField()
	{
		return Mage::getStoreConfig(self::XML_IAGENT_MOBILE_FIELD);
	}

	public function isMobileInSalesOrder()
	{
		return ($this->getIagenteMobileLocal() == 'order');
	}

	public function isMobileInSalesOrderAddress()
	{
		return ($this->getIagenteMobileLocal() == 'order_address');
	}

}

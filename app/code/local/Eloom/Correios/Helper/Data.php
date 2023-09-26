<?php

##eloom.licenca##

class Eloom_Correios_Helper_Data extends Mage_Core_Helper_Abstract {

  const XML_CORREIOS_ACTIVE = 'carriers/eloom_correios/active';
  const XML_CORREIOS_TITLE = 'carriers/eloom_correios/title';
  const XML_CORREIOS_SERVICO_GRATUITO = 'carriers/eloom_correios/servico_gratuito';
  const XML_CORREIOS_SERVICO_GRATUITO_DESCONTO = 'carriers/eloom_correios/servico_gratuito_desconto';
  const XML_CORREIOS_USUARIO = 'carriers/eloom_correios/usuario';
  const XML_CORREIOS_CODIGO_ACESSO = 'carriers/eloom_correios/codigo_acesso';
  const XML_CORREIOS_CARTAO_POSTAGEM = 'carriers/eloom_correios/cartao_postagem';
  const XML_CORREIOS_CD_FORMATO = 'carriers/eloom_correios/cd_formato';
  const XML_CORREIOS_TP_VALOR_PESO = 'carriers/eloom_correios/tp_vl_peso';
  const XML_CORREIOS_CD_SERVICO = 'carriers/eloom_correios/cd_servico';
  const XML_CORREIOS_VL_VALOR_DECLARADO = 'carriers/eloom_correios/vl_valor_declarado';
  const XML_CORREIOS_PRAZO_ENTREGA = 'carriers/eloom_correios/prazo_entrega';
  const XML_CORREIOS_MENSAGEM_PRAZO_ENTREGA = 'carriers/eloom_correios/mensagem_prazo_entrega';
  const XML_CORREIOS_ALTURA_PADRAO = 'carriers/eloom_correios/altura_padrao';
  const XML_CORREIOS_LARGURA_PADRAO = 'carriers/eloom_correios/largura_padrao';
  const XML_CORREIOS_COMPRIMENTO_PADRAO = 'carriers/eloom_correios/comprimento_padrao';
  const XML_CORREIOS_TAXA_EXTRA = 'carriers/eloom_correios/taxa_extra';
  const XML_CORREIOS_TAXA_EXTRA_VALOR = 'carriers/eloom_correios/taxa_extra_valor';
  const XML_CORREIOS_PRAZO_EXTRA = 'carriers/eloom_correios/prazo_extra';
  const XML_CORREIOS_WRITE_LOG = 'carriers/eloom_correios/writelog';

  public function hasTaxaExtra() {
    return Mage::getStoreConfigFlag(self::XML_CORREIOS_TAXA_EXTRA);
  }

  public function isTaxaExtraInPercentual() {
    return (Mage::getStoreConfig(self::XML_CORREIOS_TAXA_EXTRA) == '1');
  }

  public function isWriteLog() {
    return Mage::getStoreConfigFlag(self::XML_CORREIOS_WRITE_LOG);
  }

  public function isTaxaExtraInValor() {
    return (Mage::getStoreConfig(self::XML_CORREIOS_TAXA_EXTRA) == '2');
  }

  public function getValorTaxaExtra() {
    return Mage::getStoreConfig(self::XML_CORREIOS_TAXA_EXTRA_VALOR);
  }

  public function isActive($storeId) {
    return Mage::getStoreConfigFlag(self::XML_CORREIOS_ACTIVE, $storeId);
  }

  public function getTitle($storeId) {
    return Mage::getStoreConfig(self::XML_CORREIOS_TITLE, $storeId);
  }

  public function getServicoGratuito() {
    return Mage::getStoreConfig(self::XML_CORREIOS_SERVICO_GRATUITO);
  }

    public function getDescontoServicoGratuito() {
        return Mage::getStoreConfig(self::XML_CORREIOS_SERVICO_GRATUITO_DESCONTO);
    }

  public function getUsuario() {
    return Mage::getStoreConfig(self::XML_CORREIOS_USUARIO);
  }

  public function getCodigoAcesso() {
    return Mage::getStoreConfig(self::XML_CORREIOS_CODIGO_ACESSO);
  }

  public function getCartaoPostagem() {
    return Mage::getStoreConfig(self::XML_CORREIOS_CARTAO_POSTAGEM);
  }

  public function getCodigoFormato() {
    return Mage::getStoreConfig(self::XML_CORREIOS_CD_FORMATO);
  }

  public function getTipoValorPeso() {
    return Mage::getStoreConfig(self::XML_CORREIOS_TP_VALOR_PESO);
  }

  public function getCodigoServicos() {
    return Mage::getStoreConfig(self::XML_CORREIOS_CD_SERVICO);
  }

  public function isValorDeclarado() {
    return Mage::getStoreConfigFlag(self::XML_CORREIOS_VL_VALOR_DECLARADO);
  }

  public function isShowPrazoEntrega() {
    return Mage::getStoreConfigFlag(self::XML_CORREIOS_PRAZO_ENTREGA);
  }

  public function getMensagemPrazoEntrega() {
    return Mage::getStoreConfig(self::XML_CORREIOS_MENSAGEM_PRAZO_ENTREGA);
  }

  public function getAlturaPadrao() {
    return Mage::getStoreConfig(self::XML_CORREIOS_ALTURA_PADRAO);
  }

  public function getLarguraPadrao() {
    return Mage::getStoreConfig(self::XML_CORREIOS_LARGURA_PADRAO);
  }

  public function getComprimentoPadrao() {
    return Mage::getStoreConfig(self::XML_CORREIOS_COMPRIMENTO_PADRAO);
  }

  public function getPrazoExtra() {
    return Mage::getStoreConfig(self::XML_CORREIOS_PRAZO_EXTRA);
  }

}

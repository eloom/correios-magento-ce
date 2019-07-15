<?php

##eloom.licenca##

class Eloom_Correios_Sro_Response {

	private $versao;
	private $qtd;
	private $error = null;

	/**
	 *
	 * @var Objeto
	 */
	private $objeto;

	public function getVersao() {
		return $this->versao;
	}

	public function getQtd() {
		return $this->qtd;
	}

	public function getObjeto() {
		return $this->objeto;
	}

	public function setVersao($versao) {
		$this->versao = $versao;
	}

	public function setQtd($qtd) {
		$this->qtd = $qtd;
	}

	public function setObjeto(Objeto $objeto) {
		$this->objeto = $objeto;
	}

	public function getError() {
		return $this->error;
	}

	public function setError($error) {
		$this->error = $error;
	}

	public function parse($object) {
		if (isset($object->error)) {
			$this->error = (string)$object->error;
		} else {
			$this->versao = (string)$object->versao;
			$this->qtd = (string)$object->qtd;
			$this->objeto = new Eloom_Correios_Sro_Objeto();
			$this->objeto->parse($object->objeto);
		}

		return $this;
	}

	public function hasError() {
		if (!is_null($this->error)) {
			return true;
		}

		return false;
	}

}

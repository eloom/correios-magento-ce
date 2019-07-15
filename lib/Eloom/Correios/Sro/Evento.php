<?php

##eloom.licenca##

class Eloom_Correios_Sro_Evento {

	private $tipo;
	private $status;
	private $data;
	private $hora;
	private $descricao;
	private $local;
	private $codigo;
	private $cidade;
	private $uf;
	private $sto;

	/**
	 *
	 * @var Destino
	 */
	private $destino;

	public function getTipo() {
		return $this->tipo;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getData() {
		return $this->data;
	}

	public function getHora() {
		return $this->hora;
	}

	public function getDescricao() {
		return $this->descricao;
	}

	public function getLocal() {
		return $this->local;
	}

	public function getCodigo() {
		return $this->codigo;
	}

	public function getCidade() {
		return $this->cidade;
	}

	public function getUf() {
		return $this->uf;
	}

	public function getSto() {
		return $this->sto;
	}

	public function getDestino() {
		return $this->destino;
	}

	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function setHora($hora) {
		$this->hora = $hora;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}

	public function setLocal($local) {
		$this->local = $local;
	}

	public function setCodigo($codigo) {
		$this->codigo = $codigo;
	}

	public function setCidade($cidade) {
		$this->cidade = $cidade;
	}

	public function setUf($uf) {
		$this->uf = $uf;
	}

	public function setSto($sto) {
		$this->sto = $sto;
	}

	public function setDestino(Destino $destino) {
		$this->destino = $destino;
	}

	public function parse($evento) {
		$this->tipo = (string)$evento->tipo;
		$this->status = (string)$evento->status;
		$this->data = (string)$evento->data;
		$this->hora = (string)$evento->hora;
		$this->descricao = (string)$evento->descricao;
		$this->local = (string)$evento->local;
		$this->codigo = (string)$evento->codigo;
		$this->cidade = (string)$evento->cidade;
		$this->uf = (string)$evento->uf;
		//$this->sto = (string)$evento->sto;

		if (isset($evento->destino)) {
			$this->destino = new Eloom_Correios_Sro_Destino();
			$this->destino->parse($evento->destino);
		}

		return $this;
	}

	/**
	 * Retorna o <strong>Tipo</strong> + <strong>Status</strong> concatenados com <strong>-</strong>
	 */
	public function getTipoStatus() {
		if (!is_null($this->getTipo()) && !is_null($this->getStatus())) {
			return $this->getTipo() . ':' . $this->getStatus();
		}

		return null;
	}

	public function isFinalStep() {
		$status = Eloom_CorreiosSro_Eventos::getStatus($this->getTipoStatus());
		if ($status['step'] == Eloom_CorreiosSro_Eventos::FINAL_STEP) {
			return true;
		}

		return false;
	}

	public function isTemplateStatusEntrega() {
		$status = Eloom_CorreiosSro_Eventos::getStatus($this->getTipoStatus());
		if ($status['template'] == Eloom_CorreiosSro_Eventos::TEMPLATE_STATUS_ENTREGA) {
			return true;
		}

		return false;
	}

	public function isTemplateEntregaRealizada() {
		$status = Eloom_CorreiosSro_Eventos::getStatus($this->getTipoStatus());
		if ($status['template'] == Eloom_CorreiosSro_Eventos::TEMPLATE_ENTREGA_REALIZADA) {
			return true;
		}

		return false;
	}

	public function isTemplateLojista() {
		$status = Eloom_CorreiosSro_Eventos::getStatus($this->getTipoStatus());
		if ($status['template'] == Eloom_CorreiosSro_Eventos::TEMPLATE_LOJISTA) {
			return true;
		}

		return false;
	}

}

<?php

##eloom.licenca##

class Eloom_Correios_Sro_Destino
{

	private $local;
	private $codigo;
	private $cidade;
	private $bairro;
	private $uf;

	public function getLocal()
	{
		return $this->local;
	}

	public function getCodigo()
	{
		return $this->codigo;
	}

	public function getCidade()
	{
		return $this->cidade;
	}

	public function getBairro()
	{
		return $this->bairro;
	}

	public function getUf()
	{
		return $this->uf;
	}

	public function setLocal($local)
	{
		$this->local = $local;
	}

	public function setCodigo($codigo)
	{
		$this->codigo = $codigo;
	}

	public function setCidade($cidade)
	{
		$this->cidade = $cidade;
	}

	public function setBairro($bairro)
	{
		$this->bairro = $bairro;
	}

	public function setUf($uf)
	{
		$this->uf = $uf;
	}

	public function parse($destino)
	{
		$this->local = (string)$destino->local;
		$this->codigo = (string)$destino->codigo;
		$this->cidade = (string)$destino->cidade;
		$this->bairro = (string)$destino->bairro;
		$this->uf = (string)$destino->uf;

		return $this;
	}

}

<?php

##eloom.licenca##

class Eloom_Correios_Sro_Objeto {

  private $numero;
  private $eventos = array();

  public function getNumero() {
    return $this->numero;
  }

  public function setNumero($numero) {
    $this->numero = $numero;
  }

  public function parse($objeto) {
    $this->numero = (string) $objeto->numero;

    if (count($objeto->evento) > 1) {
      foreach ($objeto->evento as $e) {
        $evento = new Eloom_Correios_Sro_Evento();
        $this->eventos[] = $evento->parse($e);
      }
    } else {
      $evento = new Eloom_Correios_Sro_Evento();
      $this->eventos[] = $evento->parse($objeto->evento);
    }

    return $this;
  }

  public function getEventos() {
    return $this->eventos;
  }

  public function getLastEvent() {
    return $this->eventos[0];
  }

}

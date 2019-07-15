<?php

##eloom.licenca##

class Eloom_CorreiosSro_Model_Sonda extends Mage_Core_Model_Abstract {

  private $logger;

  const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

  /**
   * Sonda states
   */
  public function _construct() {
    $this->logger = Eloom_Bootstrap_Logger::getLogger(__CLASS__);
    parent::_construct();
    $this->_init('eloom_correiossro/sonda');
  }

  public function create() {
    $creationAt = Mage::getSingleton('core/date')->gmtDate(self::DATE_TIME_FORMAT);
    $this->setCreatedAt($creationAt);
    $this->setAttempts(1);

    return $this;
  }

  public function canDelete(Eloom_Correios_Sro_Evento $lastEvent) {
    return $lastEvent->isFinalStep();
  }

  public function incrementAttempts() {
    $this->attempts++;
  }

  private function getTotalDaysAfterCreationDate() {
    $date1 = new DateTime($this->getCreatedAt());
    $date2 = new DateTime();
    $interval = $date1->diff($date2);

    //echo "difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days \n";

    return $interval->days;
  }

  /**
   * Notifica o erro dos Correios somente após 1 dia da data da postagem e em dias de semana.
   * 
   * 
   * @return boolean
   */
  public function isNotityError() {
    $dayOfWeek = date("w");
    $diffDays = $this->getTotalDaysAfterCreationDate();

    if ($diffDays > 1 && in_array($dayOfWeek, array(1, 2, 3, 4, 5))) {
      return true;
    }

    return false;
  }

  public function isNotityClient(Eloom_Correios_Sro_Evento $lastEvent) {


    return false;
  }

}

<?php

##eloom.licenca##

class Eloom_CorreiosSro_Eventos {

  /**
   * 
   */
  const INITIAL_STEP = 'I';
  const MIDDLE_STEP = 'M';
  const FINAL_STEP = 'F';

  /**
   * 
   */
  const TEMPLATE_STATUS_ENTREGA = 'STATUS_ENTREGA';

  /**
   * 
   */
  const TEMPLATE_ENTREGA_REALIZADA = 'ENTREGA_REALIZADA';

  /**
   * 
   */
  const TEMPLATE_LOJISTA = 'LOJISTA';

  private static $_status = array(
      'BDE:00' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_ENTREGA_REALIZADA),
      'BDI:00' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_ENTREGA_REALIZADA),
      'BDR:00' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_ENTREGA_REALIZADA),
      'BDE:01' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_ENTREGA_REALIZADA),
      'BDI:01' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_ENTREGA_REALIZADA),
      'BDR:01' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_ENTREGA_REALIZADA),
      'BDE:23' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_LOJISTA),
      'BDI:23' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_LOJISTA),
      'BDR:23' => array('step' => self::FINAL_STEP, 'template' => self::TEMPLATE_LOJISTA),
  );

  public static function getStatus($tipoStatus) {
    if (array_key_exists($tipoStatus, self::$_status)) {
      return self::$_status[$tipoStatus];
    }

    return null;
  }

}

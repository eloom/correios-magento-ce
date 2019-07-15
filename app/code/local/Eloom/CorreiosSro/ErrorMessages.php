<?php

##eloom.licenca##

class Eloom_CorreiosSro_ErrorMessages {

  private static $_errors = array(
      '999' => 'Erro inesperado.',
      '001' => 'Falha de conexão com Web Services.',
      '002' => 'País de origem/destino deve ser Brasil.',
      '003' => 'Código Postal da Loja está incorreto.',
  );

  public static function getMessage($code) {
    if (array_key_exists($code, self::$_errors)) {
      return self::$_errors[$code];
    } else {
      return self::$_errors['999'];
    }
  }

}

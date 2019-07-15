<?php

##eloom.licenca##

class Eloom_Correios_ErrorMessages {

  private static $_errors = array(
      '999' => 'Erro inesperado.',
      '001' => 'Falha na conexão com os Correios. Por favor, tente mais tarde.',
      '002' => 'País de origem/destino deve ser Brasil.',
      '003' => 'Código Postal da Loja está incorreto.',
      '004' => 'Dimensões não encontradas para o produto %s.',
  );

  public static function getMessage($code) {
    if (array_key_exists($code, self::$_errors)) {
      return self::$_errors[$code];
    } else {
      return self::$_errors['999'];
    }
  }

}

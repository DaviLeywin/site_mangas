<?php

class CapitulosValidator {
    static function ValidarNumeroCapitulo(string $Capitulo){
        if(!is_numeric($Capitulo) or $Capitulo < 0 ){
            return false;
        }
        return true;
    }
}
?>
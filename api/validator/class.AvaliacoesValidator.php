<?php

class AvaliacoesValidator {
    static function ValidarNota($Nota,$max,$min){
        if($max < $Nota  or $Nota < $min){
            return false;
        }
        return true;
    }
}
?>
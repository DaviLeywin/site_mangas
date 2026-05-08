<?php

class RankingsValidator {
    const POPULARIDADE = "POPULARIDADE";
    const AVALIACAO = "AVALIACAO";
    const VENDAS = "VENDAS";

    static function ValidarTipo($tipo){
        $validos = [self::POPULARIDADE,self::AVALIACAO,self::VENDAS];
        if(!in_array($tipo,$validos)){
            return false;
        }
        return true;
    }
}

?>
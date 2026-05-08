<?php
class Response {
    static function Success(string $mensagem = null, array|object $resposta = []){
        if(is_null($mensagem)){
            throw new InvalidArgumentException("Mensagem de sucesso obrigatoria!");
        }
        $retorno = [
            "sucesso" => true,
            "mensagem" => $mensagem,
        ];
        if(!empty($resposta)){
            $retorno["resposta"] = (array) $resposta;
        }     
        return $retorno;
    }

    static function Fail(string $mensagem = null, array|object $resposta = []){
        if(is_null($mensagem)){
            throw new InvalidArgumentException("Mensagem de falha obrigatoria!");
        }
        $retorno = [
            "sucesso" => false,
            "mensagem" => $mensagem,
        ];
        if(!empty($resposta)){
            $retorno["resposta"] = (array) $resposta;
        }     
        return $retorno;
    }
}
?>
<?php
require_once __DIR__ . '\..\validator\class.GenerosValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Generos {
    public string $NOME;
    public string $DESCRICAO;
    
    function AlterarNome(string $Nome){
        if(!BaseValidator::Validartamanho($Nome,100)){
            throw new InvalidArgumentException("Nome excedeu o maximo de 100 caracteres!");
        }
        $this->NOME = $Nome;
    }

    function AlterarDescricao(string $Descricao){
        $this->DESCRICAO = $Descricao;
    }

    function GetNome(){
        return $this->NOME;
    }

    function GetDescricao(){
        return $this->DESCRICAO;
    }
}
?>
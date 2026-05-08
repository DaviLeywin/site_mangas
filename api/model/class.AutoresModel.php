<?php
require_once __DIR__ . '\..\validator\class.AutoresValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Autores {
    public string $NOME;
    public ?string $BIOGRAFIA = null;
    public ?string $DATA_NASCIMENTO = null;
    public ?string $NACIONALIDADE = null;
    
    function AlterarNome(string $Nome){
        if(!BaseValidator::ValidarTamanho($Nome, 200)){
            throw new InvalidArgumentException("Nome excedeu o maximo de 200 caracteres!");
        }
        $this->NOME = $Nome;
    }

    function AlterarBiografia(?string $Biografia){
        $this->BIOGRAFIA = $Biografia ?: null; 
    }

    function AlterarDataNascimento(?string $DataNascimento){
        if(empty($DataNascimento)){
            $this->DATA_NASCIMENTO = null;
            return;
        }

        if(!BaseValidator::ValidarData($DataNascimento)){
            throw new InvalidArgumentException("Data de nascimento invalida!");
        }
        $this->DATA_NASCIMENTO = $DataNascimento; 
    }

    function AlterarNacionalidade(?string $Nacionalidade){
        if(empty($Nacionalidade)){
            $this->NACIONALIDADE = null;
            return;
        }

        if(!BaseValidator::ValidarTamanho($Nacionalidade,100)){
            throw new InvalidArgumentException("Nacionalidade excedeu o limite de 100 caracteres!");
        }
        $this->NACIONALIDADE = $Nacionalidade;
    }

    function GetNome(){
        return $this->NOME;
    }

    function GetBiografia(){
        return $this->BIOGRAFIA;
    }

    function GetDataNascimento(){
        return $this->DATA_NASCIMENTO;
    }

    function GetNacionalidade(){
        return $this->NACIONALIDADE;
    }
}
?>

<?php
require_once __DIR__ . '\..\validator\class.CapitulosValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Capitulos {
    public int $MANGAS_ID;
    public string $NUMERO_CAPITULO;
    public string $TITULO_CAPITULO;
    public string $DATA_LANCAMENTO;
    public string $SINOPSE;
    
    function AlterarMangasId(int $MangasId){
        if(!BaseValidator::ValidarPositivoInteiro($MangasId)){
            throw new InvalidArgumentException("MANGAS_ID tem que ser um numero inteiro positivo maior que zero!");
        }
        $this->MANGAS_ID = $MangasId;
    }
    
    function AlterarNumeroCapitulo(string $NumeroCapitulo){
        if(!CapitulosValidator::ValidarNumeroCapitulo($NumeroCapitulo)){
            throw new InvalidArgumentException("Numero do capitulo deve ser um valor numerico maior ou igual a zero!");
        }
        if(!BaseValidator::ValidarTamanho($NumeroCapitulo,20)){
            throw new InvalidArgumentException("Capitulo excedeu o maximo de 20 caracteres!");
        }
        $this->NUMERO_CAPITULO = $NumeroCapitulo;
    }
    
    function AlterarTituloCapitulo(string $TituloCapitulo){
        if(!BaseValidator::ValidarTamanho($TituloCapitulo,200)){
            throw new InvalidArgumentException("Titulo do capitulo excedeu o maximo de 200 caracteres!");
        }
        $this->TITULO_CAPITULO = $TituloCapitulo;
    }
    
    function AlterarDataLancamento(string $DataLancamento){
        if(!BaseValidator::ValidarData($DataLancamento)){
            throw new InvalidArgumentException("Data invalida!");
        }
        $this->DATA_LANCAMENTO = $DataLancamento;
    }
    
    function AlterarSinopse(string $Sinopse){
        $this->SINOPSE = $Sinopse;
    }

    function GetSinopse(){ return $this->SINOPSE;}

    function GetDataLancamento(){ return $this->DATA_LANCAMENTO;}

    function GetTituloCapitulo(){ return $this->TITULO_CAPITULO;}

    function GetNumeroCapitulo(){ return $this->NUMERO_CAPITULO;}

    function GetMangasId(){ return $this->MANGAS_ID;}

}
?>
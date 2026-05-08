<?php
require_once __DIR__ . '\..\validator\class.MangasValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Mangas {
    const TIPO_MANGA = 'MANGA';//mangasValidator tem esse dois tambem pra quando for validar todos os campos
    const TIPO_NOVEL = 'NOVEL';
    public int $ID ;
    public string $TITULO ;
    public int $AUTORES_ID ;
    public string $DATA_PUBLICACAO ;
    public string $SINOPSE ;
    public string $TIPO ;
    public string $STATUS ;
    public string $CAPA_URL ;

    
    function AlterarTitulo(string $Titulo){
        if(!BaseValidator::ValidarTamanho($Titulo, 200)){
            throw new InvalidArgumentException("TITULO excedeu o tamanho máximo de 200 caracteres!");
        }
        $this->TITULO = $Titulo;
    }
    
    function AlterarDataPublicacao(string $DataPublicacao){
        if(!BaseValidator::ValidarData($DataPublicacao)){
            throw new InvalidArgumentException("Data em formato errado!");
        }
        $this->DATA_PUBLICACAO = $DataPublicacao;
    }
    
    function AlterarAutoresId(int $AutoresId){
        if(!BaseValidator::ValidarPositivoInteiro($AutoresId)){
            throw new InvalidArgumentException("AUTORES_ID tem que ser positivo maior que zero!");
        }
        $this->AUTORES_ID = $AutoresId;
    }
    
    function AlterarTipo($Tipo){
        if(!MangasValidator::ValidarTipo($Tipo)){
            throw new InvalidArgumentException("TIPO invalido, valores validos: ".self::TIPO_MANGA.",".self::TIPO_NOVEL."!");
        }
        $this->TIPO = $Tipo;
    }
    
    function AlterarStatus(string $Status){
        if(!BaseValidator::ValidarTamanho($Status, 50)){
            throw new InvalidArgumentException("STATUS excedeu o tamanho máximo de 50 caracteres!");
        }
        if(!MangasValidator::ValidarStatus($Status)){
            throw new InvalidArgumentException("STATUS inválido. Escolha uma opção válida.");
        }
        $this->STATUS = $Status;
    }
    
    function AlterarSinopse(string $Sinopse){ $this->SINOPSE = $Sinopse;}

    function AlterarCapaUrl(string $CapaUrl){
        if(!BaseValidator::ValidarTamanho($CapaUrl, 255)){
            throw new InvalidArgumentException("CAPA_URL excedeu o tamanho máximo de 255 caracteres!");
        }
        $this->CAPA_URL = $CapaUrl;
    }
    
    function GetAutoresId(){ return $this->AUTORES_ID;}
    
    function GetId(){ return $this->ID;}

    function GetTitulo(){ return $this->TITULO;}
    
    function GetDataPublicacao(){ return $this->DATA_PUBLICACAO;}

    function GetSinopse(){ return $this->SINOPSE;}

    function GetTipo(){ return $this->TIPO;}

    function GetStatus(){ return $this->STATUS;}

    function GetCapaUrl(){ return $this->CAPA_URL;}
}
?>
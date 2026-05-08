<?php
require_once __DIR__ . '\..\validator\class.ComentariosMangaValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class ComentariosManga {
    public int $MANGAS_ID;
    public int $USUARIOS_ID;
    public string $CONTEUDO;
    
    function AlterarMangasId(int $MangasId){
        if(!BaseValidator::ValidarPositivoInteiro($MangasId)){
            throw new InvalidArgumentException("MANGAS_ID tem que ser um numero inteiro positivo maior que zero!");
        }
        $this->MANGAS_ID = $MangasId;
    }
    
    function AlterarUsuariosId(int $UsuariosId){
        if(!BaseValidator::ValidarPositivoInteiro($UsuariosId)){
            throw new InvalidArgumentException("USUARIOS_ID tem que ser um numero inteiro positivo maior que zero!");
        }
        $this->USUARIOS_ID = $UsuariosId;
    }
    
    function AlterarConteudo(string $Conteudo){
        $this->CONTEUDO = $Conteudo;
    }

    function GetMangasId(){return $this->MANGAS_ID;}

    function GetUsuariosId(){return $this->USUARIOS_ID;}

    function GetConteudo(){return $this->CONTEUDO;}
}
?>
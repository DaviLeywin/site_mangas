<?php
require_once __DIR__ . '\..\validator\class.FavoritosValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Favoritos {
    public int $MANGAS_ID;
    public int $USUARIOS_ID;
    
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

    function GetMangasId(){
        return $this->MANGAS_ID;
    }

    function GetUsuariosId(){
        return $this->USUARIOS_ID;
    }
}
?>
<?php
require_once __DIR__ . '\..\validator\class.AvaliacoesValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Avaliacoes {
    public int $MANGAS_ID;
    public int $USUARIOS_ID;
    public int $NOTA;
    public string $COMENTARIO;
    
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

    function AlterarNota(int $Nota){
        if(!AvaliacoesValidator::ValidarNota($Nota,10,1)){
            throw new InvalidArgumentException("Nota tem que maior ou igual a 1 ou menor ou iguala 10!");
        }
        $this->NOTA = $Nota;
    }

    function AlterarComentario(string $Comentario){
        $this->COMENTARIO = $Comentario;
    }

    function GetMangasId(){
        return $this->MANGAS_ID;
    }

    function GetUsuariosId(){
        return $this->USUARIOS_ID;
    }

    function GetNota(){
        return $this->NOTA;
    }

    function GetComentario(){
        return $this->COMENTARIO;
    }
}
?>
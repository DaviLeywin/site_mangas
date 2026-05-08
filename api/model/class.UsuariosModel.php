<?php
require_once __DIR__ . '\..\validator\class.UsuariosValidator.php';
require_once __DIR__ . '\..\validator\class.BaseValidator.php';


class Usuarios {
    public string $NOME_DE_USUARIO;
    public string $EMAIL;
    public string $SENHA_HASH;
    
    
    function AlterarNomeDeUsuario(string $NomeDeUsuario){
        if(!BaseValidator::ValidarTamanho($NomeDeUsuario,50)){
            throw new InvalidArgumentException("Nome de usuario excedeu o limite de 50 caracteres!");
        }
        $this->NOME_DE_USUARIO = $NomeDeUsuario;
    }

    function AlterarEmail(string $Email){
        if(!BaseValidator::ValidarTamanho($Email,100)){
            throw new InvalidArgumentException("email excedeu o limite de 100 caracteres!");
        }
        $this->EMAIL = $Email;
    }

    function AlterarSenhaHash(string $SenhaHash){
        if(!BaseValidator::ValidarTamanho($SenhaHash,100)){
            throw new InvalidArgumentException("senha excedeu o limite de 100 caracteres!");
        }
        if(!BaseValidator::ValidarCaracteres($SenhaHash,"A-Za-z0-9_@.\-")){
            throw new InvalidArgumentException("senha com caracteres invalidos, validos: A-Za-z0-9_@.\-");
        }
        $SenhaHash = password_hash($SenhaHash, PASSWORD_DEFAULT);
        $this->SENHA_HASH = $SenhaHash;
    }

    function GetSenhaHash(){
        return $this->SENHA_HASH;
    }

    function GetEmail(){
        return $this->EMAIL;
    }

    function GetNomeDeUsuario(){
        return $this->NOME_DE_USUARIO;
    }
}
?>
<?php 
require_once __DIR__ . "\..\model\class.UsuariosModel.php";
require_once __DIR__ . "\..\dao\class.UsuariosDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class UsuariosService {
    static function GetTodos(){
        return UsuariosDAO::GetTodos();
    }
    
    static function Get($url){
        return UsuariosDAO::Get($url);
    }

    static function Post($request){
        $descricao = UsuariosDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Usuarios");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Usuarios = new Usuarios();
        
        $Usuarios->AlterarNomeDeUsuario($request['NOME_DE_USUARIO']);
        $Usuarios->AlterarEmail($request['EMAIL']);
        $Usuarios->AlterarSenhaHash($request['SENHA_HASH']);

        return UsuariosDAO::Post($Usuarios);
    }

    static function Put($request, $url){
        $descricao = UsuariosDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Usuarios",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Usuarios = new Usuarios();
        
        $Usuarios->AlterarNomeDeUsuario($request['NOME_DE_USUARIO']);
        $Usuarios->AlterarEmail($request['EMAIL']);
        $Usuarios->AlterarSenhaHash($request['SENHA_HASH']);

        return UsuariosDAO::Put($Usuarios, $url);
    }
    
    static function Delete($url){
        return UsuariosDAO::Delete($url);
    }
}
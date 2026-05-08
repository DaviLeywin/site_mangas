<?php 
require_once __DIR__ . "\..\model\class.GenerosModel.php";
require_once __DIR__ . "\..\dao\class.GenerosDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class GenerosService {
    static function GetTodos(){
        return GenerosDAO::GetTodos();
    }
    
    static function GetMangaPorId($url){
        return GenerosDAO::GetMangaPorId($url);
    }

    static function Get($url){
        return GenerosDAO::Get($url);
    }

    static function Post($request){
        $descricao = GenerosDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Generos");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Generos = new Generos();
        
        $Generos->AlterarNome($request['NOME']);
        $Generos->AlterarDescricao($request['DESCRICAO']);

        return GenerosDAO::Post($Generos);
    }

    static function Put($request, $url){
        $descricao = GenerosDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Generos",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Generos = new Generos();
        
        $Generos->AlterarNome($request['NOME']);
        $Generos->AlterarDescricao($request['DESCRICAO']);

        return GenerosDAO::Put($Generos, $url);
    }
    
    static function Delete($url){
        return GenerosDAO::Delete($url);
    }
}
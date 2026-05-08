<?php 
require_once __DIR__ . "\..\model\class.GenerosMangasModel.php";
require_once __DIR__ . "\..\dao\class.GenerosMangasDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class GenerosMangasService {
    static function GetTodos(){
        return GenerosMangasDAO::GetTodos();
    }
    
    static function Get($url){
        return GenerosMangasDAO::Get($url);
    }

    static function Post($request){
        $descricao = GenerosMangasDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"GenerosMangas");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $GenerosMangas = new GenerosMangas();

        $GenerosMangas->AlterarMangasId($request['MANGAS_ID']);
        $GenerosMangas->AlterarGenerosId($request['GENEROS_ID']);

        return GenerosMangasDAO::Post($GenerosMangas);
    }

    static function Put($request, $url){
        $descricao = GenerosMangasDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"GenerosMangas",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $GenerosMangas = new GenerosMangas();

        $GenerosMangas->AlterarMangasId($request['MANGAS_ID']);
        $GenerosMangas->AlterarGenerosId($request['GENEROS_ID']);

        return GenerosMangasDAO::Put($GenerosMangas, $url);
    }
    
    static function Delete($url){
        return GenerosMangasDAO::Delete($url);
    }
}
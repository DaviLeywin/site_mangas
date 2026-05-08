<?php 
require_once __DIR__ . "\..\model\class.ComentariosMangaModel.php";
require_once __DIR__ . "\..\dao\class.ComentariosMangaDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class ComentariosMangaService {
    static function GetTodos(){
        return ComentariosMangaDAO::GetTodos();
    }
    
    static function Get($url){
        return ComentariosMangaDAO::Get($url);
    }

    static function Post($request){
        $descricao = ComentariosMangaDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"ComentariosManga");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $ComentariosManga = new ComentariosManga();

        $ComentariosManga->AlterarMangasId($request['MANGAS_ID']);
        $ComentariosManga->AlterarUsuariosId($request['USUARIOS_ID']);
        $ComentariosManga->AlterarConteudo($request['CONTEUDO']);

        return ComentariosMangaDAO::Post($ComentariosManga);
    }

    static function Put($request, $url){
        $descricao = ComentariosMangaDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"ComentariosManga",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $ComentariosManga = new ComentariosManga();

        $ComentariosManga->AlterarMangasId($request['MANGAS_ID']);
        $ComentariosManga->AlterarUsuariosId($request['USUARIOS_ID']);
        $ComentariosManga->AlterarConteudo($request['CONTEUDO']);

        return ComentariosMangaDAO::Put($ComentariosManga, $url);
    }
    
    static function Delete($url){
        return ComentariosMangaDAO::Delete($url);
    }
}
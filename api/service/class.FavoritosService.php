<?php 
require_once __DIR__ . "\..\model\class.FavoritosModel.php";
require_once __DIR__ . "\..\dao\class.FavoritosDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class FavoritosService {
    static function GetTodos(){
        return FavoritosDAO::GetTodos();
    }
    
    static function Get($url){
        return FavoritosDAO::Get($url);
    }

    static function Post($request){
        $descricao = FavoritosDAO::Describe();
        return $descricao;
        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Favoritos");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Favoritos = new Favoritos();

        $Favoritos->AlterarMangasId($request["MANGAS_ID"]);
        $Favoritos->AlterarUsuariosId($request["USUARIOS_ID"]);

        return FavoritosDAO::Post($Favoritos);
    }

    static function Put($request, $url){
        $descricao = FavoritosDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Favoritos",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Favoritos = new Favoritos();

        $Favoritos->AlterarMangasId($request["MANGAS_ID"]);
        $Favoritos->AlterarUsuariosId($request["USUARIOS_ID"]);

        return FavoritosDAO::Put($Favoritos, $url);
    }
    
    static function Delete($url){
        return FavoritosDAO::Delete($url);
    }
}
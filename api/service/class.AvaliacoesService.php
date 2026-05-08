<?php 
require_once __DIR__ . "\..\model\class.AvaliacoesModel.php";
require_once __DIR__ . "\..\dao\class.AvaliacoesDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class AvaliacoesService {
    static function GetTodos(){
        return AvaliacoesDAO::GetTodos();
    }
    
    static function Get($url){
        return AvaliacoesDAO::Get($url);
    }

    static function Post($request){
        $descricao = AvaliacoesDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Avaliacoes");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Avaliacoes = new Avaliacoes();

        $Avaliacoes->AlterarMangasId($request["MANGAS_ID"]);
        $Avaliacoes->AlterarUsuariosId($request["USUARIOS_ID"]);
        $Avaliacoes->AlterarNota($request["NOTA"]);
        $Avaliacoes->AlterarComentario($request["COMENTARIO"]);
        
        return AvaliacoesDAO::Post($Avaliacoes);
    }

    static function Put($request, $url){
        $descricao = AvaliacoesDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Avaliacoes",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Avaliacoes = new Avaliacoes();

        $Avaliacoes->AlterarMangasId($request["MANGAS_ID"]);
        $Avaliacoes->AlterarUsuariosId($request["USUARIOS_ID"]);
        $Avaliacoes->AlterarNota($request["NOTA"]);
        $Avaliacoes->AlterarComentario($request["COMENTARIO"]);
        
        return AvaliacoesDAO::Put($Avaliacoes, $url);
    }
    
    static function Delete($url){
        return AvaliacoesDAO::Delete($url);
    }
}
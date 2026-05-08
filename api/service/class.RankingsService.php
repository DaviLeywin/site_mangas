<?php 
require_once __DIR__ . "\..\model\class.RankingsModel.php";
require_once __DIR__ . "\..\dao\class.RankingsDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class RankingsService {
    static function GetTodos(){
        return RankingsDAO::GetTodos();
    }
    
    static function Get($url){
        return RankingsDAO::Get($url);
    }

    static function Post($request){
        $descricao = RankingsDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Rankings");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Rankings = new Rankings();

        $Rankings->AlterarMangaID($request['MANGAS_ID']);
        $Rankings->AlterarTipoRanking($request['TIPO_RANKING']);
        $Rankings->AlterarPosicao($request['POSICAO']);
        $Rankings->AlterarDataRanking($request['DATA_RANKING']);

        return RankingsDAO::Post($Rankings);
    }

    static function Put($request, $url){
        $descricao = RankingsDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Rankings",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Rankings = new Rankings();

        $Rankings->AlterarMangaID($request['MANGAS_ID']);
        $Rankings->AlterarTipoRanking($request['TIPO_RANKING']);
        $Rankings->AlterarPosicao($request['POSICAO']);
        $Rankings->AlterarDataRanking($request['DATA_RANKING']);

        return RankingsDAO::Put($Rankings, $url);
    }
    
    static function Delete($url){
        return RankingsDAO::Delete($url);
    }
}
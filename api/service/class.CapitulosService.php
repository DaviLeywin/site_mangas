<?php 
require_once __DIR__ . "\..\model\class.CapitulosModel.php";
require_once __DIR__ . "\..\dao\class.CapitulosDAO.php";
require_once __DIR__ . '\..\validator\class.BaseValidator.php';

class CapitulosService {
    static function GetTodos(){
        return CapitulosDAO::GetTodos();
    }
    
    static function Get($url){
        return CapitulosDAO::Get($url);
    }

    static function Post($request){
        $descricao = CapitulosDAO::Describe();
        
        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Capitulos");
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Capitulos = new Capitulos();

        $Capitulos->AlterarMangasId($request["MANGAS_ID"]); 
        $Capitulos->AlterarNumeroCapitulo($request["NUMERO_CAPITULO"]); 
        $Capitulos->AlterarTituloCapitulo($request["TITULO_CAPITULO"]); 
        $Capitulos->AlterarDataLancamento($request["DATA_LANCAMENTO"]); 
        $Capitulos->AlterarSinopse($request["SINOPSE"]); 

        return CapitulosDAO::Post($Capitulos);
    }

    static function Put($request, $url){
        $descricao = CapitulosDAO::Describe();

        $resposta = BaseValidator::CampoSobrando($request, $descricao);
        if($resposta) return Response::Fail("Campos extras!",$resposta);
        
        $resposta = BaseValidator::ValidarNotNull($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar campos nao nulos!",$resposta);
        
        $resposta = BaseValidator::ValidarTipoArray($request, $descricao,"Capitulos",$url);
        if($resposta) return Response::Fail("Erro ao validar tipo dos campos",$resposta);
        
        $resposta = BaseValidator::ValidarTamanhoArray($request, $descricao);
        if($resposta) return Response::Fail("Erro ao validar tamanho dos campos",$resposta);
        
        $Capitulos = new Capitulos();

        $Capitulos->AlterarMangasId($request["MANGAS_ID"]); 
        $Capitulos->AlterarNumeroCapitulo($request["NUMERO_CAPITULO"]); 
        $Capitulos->AlterarTituloCapitulo($request["TITULO_CAPITULO"]); 
        $Capitulos->AlterarDataLancamento($request["DATA_LANCAMENTO"]); 
        $Capitulos->AlterarSinopse($request["SINOPSE"]); 

        return CapitulosDAO::Put($Capitulos, $url);
    }
    
    static function Delete($url){
        return CapitulosDAO::Delete($url);
    }
}
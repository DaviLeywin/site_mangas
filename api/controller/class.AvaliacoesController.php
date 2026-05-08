<?php 
require_once __DIR__ . "\..\service\class.AvaliacoesService.php";

class AvaliacoesController {
    static function GetTodos($request, $url){
        return AvaliacoesService::GetTodos();
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return AvaliacoesService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return AvaliacoesService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return AvaliacoesService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return AvaliacoesService::Delete($url);
    }
}
<?php 
require_once __DIR__ . "\..\service\class.GenerosService.php";

class GenerosController {
    static function GetTodos($request, $url){
        return GenerosService::GetTodos();
    }
    
    static function GetMangaPorId($request, $url){
        $url["id"] = (int) $url["id"];
        return GenerosService::GetMangaPorId($url);
    }
    
    static function Get($request, $url){
        return GenerosService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return GenerosService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return GenerosService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return GenerosService::Delete($url);
    }
}
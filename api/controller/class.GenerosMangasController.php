<?php 
require_once __DIR__ . "\..\service\class.GenerosMangasService.php";

class GenerosMangasController {
    static function GetTodos($request, $url){
        return GenerosMangasService::GetTodos();
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return GenerosMangasService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return GenerosMangasService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return GenerosMangasService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return GenerosMangasService::Delete($url);
    }
}
<?php 
require_once __DIR__ . "\..\service\class.FavoritosService.php";

class FavoritosController {
    static function GetTodos($request, $url){
        return FavoritosService::GetTodos();
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return FavoritosService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return FavoritosService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return FavoritosService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return FavoritosService::Delete($url);
    }
}
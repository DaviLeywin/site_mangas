<?php 
require_once __DIR__ . "\..\service\class.UsuariosService.php";

class UsuariosController {
    static function GetTodos($request, $url){
        return UsuariosService::GetTodos();
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return UsuariosService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return UsuariosService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return UsuariosService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return UsuariosService::Delete($url);
    }
}
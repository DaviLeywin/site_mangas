<?php 
require_once __DIR__ . "\..\service\class.ComentariosMangaService.php";

class ComentariosMangaController {
    static function GetTodos($request, $url){
        return ComentariosMangaService::GetTodos();
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return ComentariosMangaService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return ComentariosMangaService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return ComentariosMangaService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return ComentariosMangaService::Delete($url);
    }
}
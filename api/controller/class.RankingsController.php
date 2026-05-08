<?php 
require_once __DIR__ . "\..\service\class.RankingsService.php";

class RankingsController {
    static function GetTodos($request, $url){
        return RankingsService::GetTodos();
    }
    
    static function Get($request, $url){
        $url["id"] = (int) $url["id"];
        return RankingsService::Get($url);
    }    

    static function Post($request, $url){
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return RankingsService::Post($request->BODY);
    }

    static function Put($request, $url){
        $url["id"] = (int) $url["id"];
        if(empty($request->BODY)){
            return Response::Fail("Dados vazios!");
        }
        return RankingsService::Put($request->BODY, $url);
    }    

    static function Delete($request, $url){
        $url["id"] = (int) $url["id"];
        return RankingsService::Delete($url);
    }
}
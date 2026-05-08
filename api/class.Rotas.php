<?php
require "class.Request.php";
// require_once "controller/class.AuthController.php";

class Rotas{
    public $rotas;

    function __construct(){ $this->rotas = []; }

    function get($url, $acao ,$protegida = false){$this->add($url, $acao, "GET" ,$protegida);}
    function post($url, $acao ,$protegida = false){$this->add($url, $acao, "POST" ,$protegida);}
    function put($url, $acao ,$protegida = false){$this->add($url, $acao, "PUT" ,$protegida);}
    function delete($url, $acao ,$protegida = false){$this->add($url, $acao, "DELETE" ,$protegida);}

    function add($url, $acao, $metodo , $protegida ){$this->rotas[] = compact("url","acao","metodo","protegida");}

    function executar(){
        $request = new Request();
        $metodo = $_SERVER["REQUEST_METHOD"];
        $urlBase = "/SiteLivros/api";
        $url = str_ireplace($urlBase,"",$_SERVER["REQUEST_URI"]);
        $url = urldecode($url);
        $url = $url == "/" ? $url : rtrim($url,"/");
        foreach($this->rotas as $rota){
            if($rota["metodo"] != $metodo) continue;
            $regex = "#^".preg_replace_callback(   '/\{([^\}]+)\}/' , function($m){ return "(?P<".$m[1].">[^/]+)";} , $rota["url"])."$#";
            if(preg_match($regex, $url, $parametros)){
                $parametros = array_filter($parametros,fn($chave) => !is_int($chave),ARRAY_FILTER_USE_KEY);
                [$controller,$metodo] = explode("@",$rota["acao"]);
                require_once "./controller/class.$controller.php";
                return call_user_func_array([$controller,$metodo], ["url" => $parametros,"request" => $request]);
                exit;
            }
        }
        http_response_code(405);
        return ["erro" => true,"mensagem" => "Rota não encontrada",];
    }
}

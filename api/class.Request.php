<?php
class Request {
    public  $All = [];
    public  $BODY = [];
    public  $GET = [];
    public  $POST = [];

    function __construct(){
        $this->GET = $_GET ?:  [];
        $this->POST = $_POST ?:  [];

        $raw = file_get_contents("php://input");
        $json = json_decode($raw, true);
        if ($json !== null) {
            $this->BODY = $json;
        }else {
            $temp = [];
            parse_str($raw, $temp);
            if (!empty($temp))$this->BODY = $temp;
        }
        $this->All = array_merge($this->POST,$this->GET,$this->BODY);
    }

}
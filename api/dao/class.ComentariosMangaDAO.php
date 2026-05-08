<?php

class ComentariosMangaDAO {
    static function GetTodos(){
        return DAO::Get()->Table("ComentariosManga")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("ComentariosManga")->Where($where)->Execute();
    }
    static function Post(ComentariosManga $dados){
        return DAO::Post()->Table("ComentariosManga")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("ComentariosManga")->Where($where)->Execute();     
    }
    static function Put(ComentariosManga $dados,$where){
        return DAO::Put()->Table("ComentariosManga")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("ComentariosManga")->Execute();   
    }
}



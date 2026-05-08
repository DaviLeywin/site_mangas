<?php

class GenerosMangasDAO {
    static function GetTodos(){
        return DAO::Get()->Table("GenerosMangas")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("GenerosMangas")->Where($where)->Execute();
    }
    static function Post(GenerosMangas $dados){
        return DAO::Post()->Table("GenerosMangas")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("GenerosMangas")->Where($where)->Execute();     
    }
    static function Put(GenerosMangas $dados,$where){
        return DAO::Put()->Table("GenerosMangas")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("GenerosMangas")->Execute();   
    }
}



<?php

class CapitulosDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Capitulos")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Capitulos")->Where($where)->Execute();
    }
    static function Post(Capitulos $dados){
        return DAO::Post()->Table("Capitulos")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Capitulos")->Where($where)->Execute();     
    }
    static function Put(Capitulos $dados,$where){
        return DAO::Put()->Table("Capitulos")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Capitulos")->Execute();   
    }
}



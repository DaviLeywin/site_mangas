<?php

class RankingsDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Rankings")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Rankings")->Where($where)->Execute();
    }
    static function Post(Rankings $dados){
        return DAO::Post()->Table("Rankings")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Rankings")->Where($where)->Execute();     
    }
    static function Put(Rankings $dados,$where){
        return DAO::Put()->Table("Rankings")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Rankings")->Execute();   
    }
}


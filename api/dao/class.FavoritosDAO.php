<?php

class FavoritosDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Favoritos")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Favoritos")->Where($where)->Execute();
    }
    static function Post(Favoritos $dados){
        return DAO::Post()->Table("Favoritos")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Favoritos")->Where($where)->Execute();     
    }
    static function Put(Favoritos $dados,$where){
        return DAO::Put()->Table("Favoritos")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Favoritos")->Execute();   
    }
}



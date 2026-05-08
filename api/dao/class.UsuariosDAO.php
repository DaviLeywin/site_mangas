<?php

class UsuariosDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Usuarios")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Usuarios")->Where($where)->Execute();
    }
    static function Post(Usuarios $dados){
        return DAO::Post()->Table("Usuarios")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Usuarios")->Where($where)->Execute();     
    }
    static function Put(Usuarios $dados,$where){
        return DAO::Put()->Table("Usuarios")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Usuarios")->Execute();   
    }
}



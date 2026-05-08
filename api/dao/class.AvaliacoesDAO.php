<?php

class AvaliacoesDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Avaliacoes")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Avaliacoes")->Where($where)->Execute();
    }
    static function Post(Avaliacoes $dados){
        return DAO::Post()->Table("Avaliacoes")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Avaliacoes")->Where($where)->Execute();     
    }
    static function Put(Avaliacoes $dados,$where){
        return DAO::Put()->Table("Avaliacoes")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Avaliacoes")->Execute();   
    }
}



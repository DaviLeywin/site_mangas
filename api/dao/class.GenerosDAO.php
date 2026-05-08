<?php
declare(strict_types=1);
require_once __DIR__ . "\..\..\Framework.php";
require_once  __DIR__ . "\..\class.Response.php";

class GenerosDAO {
    static function GetTodos(){
        return DAO::Get()->Table("Generos")->Execute();            
    }   
    static function Get($where){
        return DAO::Get()->Table("Generos")->Where($where)->Execute();
    }
    static function Post(Generos $dados){
        return DAO::Post()->Table("Generos")->Dados($dados)->Execute();
    }
    static function Delete($where){
        return DAO::Delete()->Table("Generos")->Where($where)->Execute();     
    }
    static function Put(Generos $dados,$where){
        return DAO::Put()->Table("Generos")->Dados($dados)->Where($where)->Execute();   
    }
    static function Describe(){
        return DAO::Describe()->Table("Generos")->Execute();   
    }
    static function GetMangaPorId($url){
        $pdo = Banco::BuscarConexao();
        $id = $url['id'];
        
        $sql = "SELECT M.* 
        FROM MANGAS M
        INNER JOIN GENEROSMANGAS GM
        ON GM.MANGAS_ID = M.ID
        INNER JOIN GENEROS G
        ON GM.GENEROS_ID = G.ID
        WHERE G.ID = $id;";
    
        $stmt = $pdo->query($sql);
        $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return Response::Success('Dados encontrados com sucesso!',$resposta);
    }
}


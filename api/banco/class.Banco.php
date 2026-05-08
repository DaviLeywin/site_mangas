<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../class.Response.php";

class Banco {
    static $pdo;
    static $sql;
    static $insert;
    static $host;
    static $senha;
    static $usuario;
    static $banco;

    static function init(){
        self::$host = $GLOBALS["conn"]["host"];
        self::$senha = $GLOBALS["conn"]["senha"];
        self::$usuario = $GLOBALS["conn"]["usuario"];
        self::$banco = $GLOBALS["conn"]["banco"];
        self::$sql = $GLOBALS["sql"];
        self::$insert = $GLOBALS["insert"];
    }

    static function CriarBanco(){
        self::init();
        try{
            $conn = new PDO("mysql:host=".self::$host.";charset=utf8",self::$usuario, self::$senha);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
            $sql = "CREATE DATABASE IF NOT EXISTS ".self::$banco.";";
            $conn->exec($sql);
            return Response::Success("Banco criado com sucesso!");
        }catch(Exception $e){
            throw new PDOException("Erro ao criar com o banco: ".$e->getMessage());
        }
    }

    static function DeletarBanco(){
        self::init();
        try{
            $conn = new PDO("mysql:host=".self::$host.";charset=utf8",self::$usuario, self::$senha);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
            $sql = "DROP DATABASE IF EXISTS ".self::$banco.";";
            $conn->exec($sql);
            return Response::Success("Banco apagado com sucesso!");
        }catch(PDOException $e){
            throw new PDOException("Erro ao apagar com o banco: ".$e->getMessage());
        }
    }

    static function BuscarConexao(){
        self::init();
        try{
            $conn = new PDO("mysql:host=".self::$host.";dbname=".self::$banco,self::$usuario, self::$senha);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
            return $conn;
        }catch(Exception $e){
            throw new PDOException("Erro ao conectar com o banco: ".$e->getMessage());
        }
    }

    static function CriarSql(){
        self::$pdo = self::BuscarConexao();
        try{
            foreach(self::$sql as $sql){
                self::$pdo->exec($sql);
            }
            foreach(self::$insert as $insert){
                self::$pdo->exec($insert);
            }
        }catch(Exception $e){
            throw new PDOException("Erro ao criar SQL: ".$e->getMessage());
        }
    }

    static function RecriarBanco(){
        self::DeletarBanco();
        self::CriarBanco();
        self::CriarSql();
        return Response::Success("Banco recriado com sucesso!");
    }
}
// echo json_encode(Banco::DeletarBanco());
// echo json_encode(Banco::RecriarBanco());
// echo json_encode(Banco::CriarBanco());
// echo json_encode(Banco::CriarSql());
?>
<?php
class Conexao {
    private static $instancia = null;

    public static function getInstancia() { // Este é o nome correto
        if (self::$instancia === null) {
            try {
                $host = 'localhost';
                $db   = 'le_cafeteria'; // VERIFIQUE O NOME NO PHPADMIN
                $user = 'root';
                $pass = ''; 
                self::$instancia = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro de Conexão: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}
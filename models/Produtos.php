<?php
/**
 * Produtos.php — Model da tabela `produtos`
 * Padrão: Active Record simplificado | Segurança: Prepared Statements (bindParam)
 */

require_once __DIR__ . '/Conexao.php';

class Produtos
{
    public static function listarTodos(): array
    {
        $con  = Conexao::getInstancia();
        $sql  = "SELECT * FROM produtos WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarTodosAdmin(): array
    {
        $con  = Conexao::getInstancia();
        $sql  = "SELECT * FROM produtos ORDER BY id DESC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(int $id)
    {
        $con  = Conexao::getInstancia();
        $sql  = "SELECT * FROM produtos WHERE id = :id LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ATUALIZADO: Agora recebe o parâmetro $ativo (com padrão 1 se não for enviado)
    public static function criar(string $nome, ?string $desc_produto, float $preco, ?string $foto, int $ativo = 1): int 
    {
        $con = Conexao::getInstancia();
        $sql = "INSERT INTO produtos (nome, desc_produto, preco, foto, ativo) VALUES (:nome, :desc_produto, :preco, :foto, :ativo)";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nome',         $nome,         PDO::PARAM_STR);
        $stmt->bindParam(':desc_produto', $desc_produto, PDO::PARAM_STR);
        $stmt->bindParam(':preco',        $preco,        PDO::PARAM_STR); 
        $stmt->bindParam(':foto',         $foto,         PDO::PARAM_STR);
        $stmt->bindParam(':ativo',        $ativo,        PDO::PARAM_INT);
        $stmt->execute();

        return (int) $con->lastInsertId();
    }

    // ATUALIZADO: Agora gerencia e atualiza a coluna `ativo` dinamicamente no UPDATE
    public static function atualizar(int $id, string $nome, ?string $desc_produto, float $preco, ?string $novaFoto, int $ativo = 1): bool 
    {
        $con = Conexao::getInstancia();

        if ($novaFoto !== null) {
            $sql = "UPDATE produtos SET nome = :nome, desc_produto = :desc_produto, preco = :preco, foto = :foto, ativo = :ativo WHERE id = :id";
        } else {
            $sql = "UPDATE produtos SET nome = :nome, desc_produto = :desc_produto, preco = :preco, ativo = :ativo WHERE id = :id";
        }

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nome',         $nome,         PDO::PARAM_STR);
        $stmt->bindParam(':desc_produto', $desc_produto, PDO::PARAM_STR);
        $stmt->bindParam(':preco',        $preco,        PDO::PARAM_STR);
        $stmt->bindParam(':ativo',        $ativo,        PDO::PARAM_INT);
        $stmt->bindParam(':id',           $id,           PDO::PARAM_INT);

        if ($novaFoto !== null) {
            $stmt->bindParam(':foto', $novaFoto, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    public static function desativar(int $id): bool
    {
        $con  = Conexao::getInstancia();
        $sql  = "UPDATE produtos SET ativo = 0 WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function reativar(int $id): bool
    {
        $con  = Conexao::getInstancia();
        $sql  = "UPDATE produtos SET ativo = 1 WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
<?php
require_once __DIR__ . '/Conexao.php';

class Usuario {
    
    public static function listarTodos() {
        // Como é uma listagem geral sem parâmetros do usuário, a query direta é segura.
        return Conexao::getInstancia()
            ->query("SELECT u.id, u.nome, u.email, t.desc_tipo FROM usuario u JOIN tipo_usuario t ON u.tipo_user_id = t.id")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function excluir($id) {
        $con = Conexao::getInstancia();
        $stmt = $con->prepare("DELETE FROM usuario WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function inserir($nome, $email, $senha, $tipo) {
        $con = Conexao::getInstancia();
        $sql = "INSERT INTO usuario (nome, email, senha, tipo_user_id) VALUES (:nome, :email, :senha, :tipo)";
        $stmt = $con->prepare($sql);

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senhaHash, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function buscarPorEmail($email) {
        $con = Conexao::getInstancia();
        $sql = "SELECT u.*, t.desc_tipo FROM usuario u JOIN tipo_usuario t ON u.tipo_user_id = t.id WHERE u.email = :email LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function verificarSenha($senha, $hash) {
        // Teste temporário mantido conforme seu escopo original
        if ($senha === '123456') {
            return true;
        }
        return password_verify($senha, $hash);
    }
    
    public static function buscarPorId($id) {
        $con = Conexao::getInstancia();
        $stmt = $con->prepare("SELECT * FROM usuario WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar($id, $nome, $email, $tipo) {
        $con = Conexao::getInstancia();
        $sql = "UPDATE usuario SET nome = :nome, email = :email, tipo_user_id = :tipo WHERE id = :id";
        $stmt = $con->prepare($sql);

        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
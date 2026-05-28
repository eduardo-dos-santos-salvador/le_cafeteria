<?php
// models/Pedido.php
require_once 'Conexao.php';

class Pedido {
    public $cliente_nome;
    public $endereco;
    public $pizza_id;

    // Método para salvar o pedido no banco
    public function salvar() {
        $con = Conexao::getConexao();

        // O SQL agora envia apenas os 3 campos obrigatórios.
        // O MariaDB cuidará do 'status' e da 'data_pedido' automaticamente.
        $sql = "INSERT INTO pedidos (cliente_nome, endereco, pizza_id) 
                VALUES (:nome, :endereco, :pizza_id)";

        $stmt = $con->prepare($sql);

        // Vinculando os valores de forma segura contra SQL Injection
        $stmt->bindValue(':nome', $this->cliente_nome);
        $stmt->bindValue(':endereco', $this->endereco);
        $stmt->bindValue(':pizza_id', $this->pizza_id);

        return $stmt->execute();
    }
    // Método para listar todos os pedidos no painel Admin
    public static function listarTodos() {
        $con = Conexao::getConexao();

        // O INNER JOIN une as tabelas para trazer o nome da pizza em vez do ID numérico
        $sql = "SELECT 
                    pd.id, 
                    pd.cliente_nome, 
                    pd.endereco, 
                    pd.data_pedido, 
                    pd.status, 
                    p.sabor, 
                    p.preco 
                FROM pedidos pd
                INNER JOIN pizzas p ON pd.pizza_id = p.id
                ORDER BY pd.data_pedido DESC"; // Traz os mais recentes primeiro

        $stmt = $con->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //  Busca um pedido específico para preencher o formulário
    public static function buscarPorId($id) {
        $con = Conexao::getConexao();
        // Usamos INNER JOIN para já trazer o nome da pizza
        $sql = "SELECT pd.*, p.sabor 
                FROM pedidos pd 
                INNER JOIN pizzas p ON pd.pizza_id = p.id 
                WHERE pd.id = :id";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Salva o novo status no banco de dados
    public static function atualizarStatus($id, $status) {
        $con = Conexao::getConexao();
        $sql = "UPDATE pedidos SET status = :status WHERE id = :id";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    // Método para deletar um pedido pelo ID
    public static function excluir($id) {
        $con = Conexao::getConexao();
        // SQL direto e reto. O WHERE id é vital para não apagar a tabela toda!
        $sql = "DELETE FROM pedidos WHERE id = :id";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
}
?>
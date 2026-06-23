<?php
require_once __DIR__ . '/Conexao.php';

class ItensPedido {
    private $id;
    private $pedido_id;
    private $produto_id;
    private $quantidade;
    private $preco_unit; // 🔍 Ajustado para bater com o print do banco

    // ====== MÉTODO: SALVAR ITEM NO BANCO ======
    // Usado no Controller do Cliente ao finalizar o carrinho de compras
public static function inserir($pedido_id, $produto_id, $quantidade, $preco_unit) {
    $con = Conexao::getInstancia();
    
    // 🧹 TRATAMENTO: Remove o R$, espaços e troca vírgula por ponto
    $preco_unit = str_replace(['R$', ' ', ','], ['', '', '.'], $preco_unit);
    // Converte explicitamente para float (número decimal)
    $preco_unit = (float)$preco_unit;

    $sql = "INSERT INTO itens_pedido (pedido_id, produto_id, quantity, preco_unit) 
            VALUES (:pedido_id, :produto_id, :quantidade, :preco_unit)";
            
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
    $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
    $stmt->bindParam(':preco_unit', $preco_unit);
    
    return $stmt->execute();
}

    // ====== MÉTODO: BUSCAR ITENS DE UM PEDIDO ======
    // Usado pelo BaristaController ou direto na View para listar os produtos do card
    public static function buscarPorPedido($pedido_id) {
        $con = Conexao::getInstancia();
        
        // Faz o JOIN com produtos para capturar o 'nome' do café/doce
        $sql = "SELECT ip.pedido_id, pr.nome, ip.quantidade, ip.preco_unit
                FROM itens_pedido ip
                JOIN produtos pr ON pr.id = ip.produto_id
                WHERE ip.pedido_id = :pedido_id";
                
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
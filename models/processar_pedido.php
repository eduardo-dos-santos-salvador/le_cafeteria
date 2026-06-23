<?php
// models/processar_pedido.php
require_once __DIR__ . '/Conexao.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? null;
    $forma_pagamento = $_POST['forma_pagamento'] ?? null;
    $carrinho_json = $_POST['carrinho_itens'] ?? null;

    if (!$usuario_id || !$forma_pagamento || !$carrinho_json) {
        die("Erro: Dados incompletos.");
    }

    $itens = json_decode($carrinho_json, true);
    if (empty($itens)) {
        die("Erro: O carrinho chegou vazio no servidor PHP.");
    }

    try {
        $con = Conexao::getInstancia(); 
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->beginTransaction();

        $sqlPedido = "INSERT INTO pedido (usuario_id, status, criado_em, updated_at) VALUES (:user, 'aguardando', NOW(), NOW())";
        $stmtPedido = $con->prepare($sqlPedido);
        $stmtPedido->bindParam(':user', $usuario_id, PDO::PARAM_INT);
        $stmtPedido->execute();
        
        $pedido_id = $con->lastInsertId();

        $sqlItem = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade) VALUES (:pedido_id, :produto_id, :quantidade)";
        $stmtItem = $con->prepare($sqlItem);

        foreach ($itens as $item) {
            $stmtItem->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
            $stmtItem->bindValue(':produto_id', $item['id'], PDO::PARAM_INT);
            $stmtItem->bindValue(':quantidade', $item['quantidade'], PDO::PARAM_INT);
            $stmtItem->execute();
        }

        $con->commit();
        header("Location: /le_cafeteria/index.php?controller=barista&acao=fila");
        exit;

    } catch (PDOException $e) {
        if (isset($con) && $con->inTransaction()) {
            $con->rollBack();
        }
        die("Erro de Banco de Dados: " . $e->getMessage());
    } catch (Exception $e) {
        die("Erro Geral: " . $e->getMessage());
    }
}
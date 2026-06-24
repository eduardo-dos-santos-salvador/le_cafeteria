<?php
require_once __DIR__ . '/../models/Conexao.php';

class ClienteController {
    
    public static function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Verifica se é Cliente (tipo 3)
        if (!isset($_SESSION['tipo_user_id']) || $_SESSION['tipo_user_id'] != 3) {
            header('Location: /le_cafeteria/index.php?controller=auth&action=login');
            exit;
        }
        
        require_once __DIR__ . '/../views/cliente.php';
    }

    /** * Processa o fechamento do pedido vindo do localStorage via POST JSON
     */
    public static function finalizarPedido() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_user_id'] != 3) {
            header('Location: /le_cafeteria/index.php?controller=auth&action=login');
            exit;
        }

        $carrinhoJson = $_POST['carrinho_dados'] ?? '';
        $carrinho = json_decode($carrinhoJson, true);

        if (empty($carrinho)) {
            $_SESSION['msg_erro'] = "Carrinho vazio.";
            header('Location: /le_cafeteria/index.php?controller=cliente');
            exit;
        }

        try {
            $con = Conexao::getInstancia();
            $con->beginTransaction();

            // Calcula valor total com segurança
            $valor_total = 0;
            foreach ($carrinho as $item) {
                $preco = floatval(str_replace(',', '.', $item['preco'] ?? 0));
                $qtd = (int)($item['quantidade'] ?? 1);
                $valor_total += ($preco * $qtd);
            }

            $sqlPedido = "INSERT INTO pedido (usuario_id, status_id, valor_total, criado_em) 
                          VALUES (:usuario_id, 1, :valor_total, NOW())";
            $stmtPedido = $con->prepare($sqlPedido);
            $stmtPedido->bindValue(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
            $stmtPedido->bindValue(':valor_total', $valor_total);
            $stmtPedido->execute();

            $pedido_id = $con->lastInsertId();

            $sqlItens = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade) 
                         VALUES (:pedido_id, :produto_id, :quantidade)";
            $stmtItens = $con->prepare($sqlItens);

            foreach ($carrinho as $item) {
                $produto_id = (int)$item['id']; 
                $quantidade = (int)$item['quantidade'];

                $stmtItens->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
                $stmtItens->bindValue(':produto_id', $produto_id, PDO::PARAM_INT);
                $stmtItens->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
                $stmtItens->execute();
            }

            $con->commit();

            $_SESSION['msg_sucesso'] = "Pedido realizado com sucesso!";
            header('Location: /le_cafeteria/index.php?controller=cliente');
            exit;

        } catch (Exception $e) {
            if (isset($con) && $con->inTransaction()) {
                $con->rollBack();
            }
            error_log('[LE_CAFETERIA] Erro: ' . $e->getMessage());
            header('Location: /le_cafeteria/index.php?controller=cliente');
            exit;
        }
    }
}
// CORREÇÃO: Códigos soltos e loops remanescentes fora da classe foram eliminados completamente.
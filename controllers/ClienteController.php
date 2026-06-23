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

    
    /** Processa o fechamento do pedido vindo do localStorage via POST JSON
     */
    public static function finalizarPedido() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_user_id'] != 3) {
            header('Location: /le_cafeteria/index.php?controller=auth&action=login');
            exit;
        }

        // Recupera o JSON enviado pelo formulário invisível
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

            $usuario_id = $_SESSION['usuario_id'];
            $status_inicial = 'aguardando'; // Status crucial para o painel do barista

            // Salva o pedido inicial
            $sqlPedido = "INSERT INTO pedido (usuario_id, status, criado_em, atualizado_em) 
                          VALUES (:usuario_id, :status, NOW(), NOW())";
            
            $stmt = $con->prepare($sqlPedido);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status_inicial, PDO::PARAM_STR);
            $stmt->execute();

            $pedido_id = $con->lastInsertId();

            // Salva os itens vinculados
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
		
		$pedido_id = Pedido::criar($_SESSION['user_id'], $valor_total); // Cria o pedido pai

// Percorre os itens que estão salvos na sessão do carrinho
foreach ($_SESSION['carrinho'] as $item) {
    // Torna a classe útil inserindo cada linha no banco
    ItensPedido::inserir($pedido_id, $item['id'], $item['quantidade'], $item['preco']);
}
    }
}
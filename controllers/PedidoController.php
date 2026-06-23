<?php
// controllers/PedidoController.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$caminhoConexao = $_SERVER['DOCUMENT_ROOT'] . '/le_cafeteria/models/Conexao.php';
if (file_exists($caminhoConexao)) {
    require_once $caminhoConexao;
} else {
    die("Erro Crítico: Não foi possível encontrar o arquivo Conexao.php");
}

class PedidoController {
    
    public static function finalizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_POST['usuario_id'] ?? null;
            $forma_pagamento = $_POST['forma_pagamento'] ?? null;
            $carrinho_json = $_POST['carrinho_itens'] ?? null;
            $valor_total = $_POST['valor_total'] ?? 0;

            if (empty($carrinho_json)) {
                die("<h2>Erro: O carrinho chegou vazio no PHP.</h2>");
            }

            if (!$usuario_id || !$forma_pagamento) {
                die("Erro no servidor: ID Usuário ou Forma de Pagamento ausentes.");
            }

            $itens = json_decode($carrinho_json, true);

            if (floatval($valor_total) <= 0 && is_array($itens)) {
                $valor_total = 0;
                foreach ($itens as $item) {
                    $preco = floatval(str_replace(',', '.', $item['preco'] ?? 0));
                    $qtd = intval($item['quantidade'] ?? 1);
                    $valor_total += ($preco * $qtd);
                }
            }

            try {
                $con = method_exists('Conexao', 'getInstancia') ? Conexao::getInstancia() : Conexao::getConexao();
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $con->beginTransaction();

                $sqlPedido = "INSERT INTO pedido (usuario_id, status, total, criado_em) VALUES (:user, 'aguardando', :total, NOW())";
                $stmtPedido = $con->prepare($sqlPedido);
                $stmtPedido->bindParam(':user', $usuario_id, PDO::PARAM_INT);
                $stmtPedido->bindParam(':total', $valor_total, PDO::PARAM_STR); 
                $stmtPedido->execute();

$pedido_id = $con->lastInsertId();

// 🛠️ CORREÇÃO: Adicionada a coluna preco_unit na consulta SQL
$sqlItem = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unit) 
            VALUES (:pedido_id, :produto_id, :quantidade, :preco_unit)";
$stmtItem = $con->prepare($sqlItem);

foreach ($itens as $item) {
    $produto_id = $item['id'] ?? $item['produto_id'] ?? null;
    $quantidade = $item['quantidade'] ?? 1;
    
    // 🛠️ CORREÇÃO: Captura o preço do item vindo do carrinho JSON e trata o formato decimal
    $preco_unit = floatval(str_replace(',', '.', $item['preco'] ?? 0));

    if (!$produto_id) {
        throw new Exception("ID de produto inválido no carrinho.");
    }

    $stmtItem->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmtItem->bindValue(':produto_id', $produto_id, PDO::PARAM_INT);
    $stmtItem->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
    // 🛠️ CORREÇÃO: Adicionado o bind correspondente ao preço unitário
    $stmtItem->bindValue(':preco_unit', $preco_unit);
    
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
                if (isset($con) && $con->inTransaction()) {
                    $con->rollBack();
                }
                die("Erro Geral: " . $e->getMessage());
            }
        }
    }
}

PedidoController::finalizar();
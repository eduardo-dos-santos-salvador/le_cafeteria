<?php
/**
 * admin.php — Ponto de entrada único do painel administrativo
 * Ações via GET ?acao=:
 *   login    — processa formulário de login (POST)
 *   logout   — encerra a sessão
 *   listar   — lista de produtos (padrão)
 *   criar    — formulário de novo produto
 *   editar   — formulário de edição
 *   salvar   — salva criar ou editar (POST)
 *   desativar | reativar — altera campo `ativo`
 *   pedidos  — listagem de pedidos (admin)
 *   usuarios — listagem de usuários (admin)
 */

session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProdutosController.php';

$acao = $_GET['acao'] ?? 'listar';

// ── Rotas abertas (sem autenticação) ─────────────────────────────
if ($acao === 'login') {
    AuthController::login();
    exit;
}

if ($acao === 'logout') {
    AuthController::logout();
    exit;
}

// ── Rotas especiais do admin (fora do ProdutosController) ────────
AuthController::exigirAdmin(); // protege tudo abaixo

if ($acao === 'pedidos') {
    require_once __DIR__ . '/models/Conexao.php';
    $con  = Conexao::getConexao();
    $stmt = $con->query(
        "SELECT p.*, u.nome AS cliente
         FROM pedido p JOIN usuario u ON u.id = p.usuario_id
         ORDER BY p.criado_em DESC LIMIT 100"
    );
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require_once __DIR__ . '/views/admin/pedidos_lista.php';
    exit;
}

if ($acao === 'usuarios') {
    require_once __DIR__ . '/models/Conexao.php';
    $con  = Conexao::getConexao();
    $stmt = $con->query(
        "SELECT u.*, t.desc_tipo AS tipo
         FROM usuario u JOIN tipo_usuario t ON t.id = u.tipo_user_id
         ORDER BY u.id ASC"
    );
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require_once __DIR__ . '/views/admin/usuarios_lista.php';
    exit;
}

// ── Demais ações → ProdutosController ────────────────────────────
ProdutosController::despachar();

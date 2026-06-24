<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/BaristaController.php';
require_once __DIR__ . '/controllers/ClienteController.php';

$controller = $_GET['controller'] ?? 'home';

// 🔄 CORREÇÃO DE NOMENCLATURA: Aceita tanto o padrão 'action' quanto o 'acao' dos links
$action = $_GET['action'] ?? $_GET['acao'] ?? 'index';

// Se o controller é 'home', carregamos a dashboard pública diretamente
if ($controller === 'home') {
    require_once __DIR__ . '/views/dashboard.php';
    exit;
}

switch ($controller) {
    case 'auth':
        if ($action === 'login') {
            AuthController::login();
        } elseif ($action === 'logout') {
            AuthController::logout();
        } elseif ($action === 'cadastro') {
            AuthController::cadastro();
        } 
        // ── NOVAS ROTAS DE RECUPERAÇÃO DE SENHA LIBERADAS AQUI ──
        elseif ($action === 'processar_recuperacao') {
            AuthController::processar_recuperacao();
        } elseif ($action === 'redefinir_tela') {
            AuthController::redefinir_tela();
        } elseif ($action === 'atualizar_senha') {
            AuthController::atualizar_senha();
        }
        break;

    case 'admin':
        // Captura a ação aceitando tanto 'action' quanto 'acao' para evitar conflitos de digitação
        $action = $_GET['action'] ?? $_GET['acao'] ?? '';

        // ── Rotas de Usuários ──
        if ($action === 'usuarios') {
            AdminController::listarUsuarios();
        } elseif ($action === 'adicionar_usuario') {
            AdminController::adicionarUsuario();
        } elseif ($action === 'excluir_usuario') {
            AdminController::excluirUsuario();
        } elseif ($action === 'editar_usuario') {
            AdminController::editarUsuario();
            
        // ── Gerenciamento de Produtos ──
        } elseif ($action === 'produtos') {
            AdminController::produtos();
        } elseif ($action === 'criar') {
            AdminController::criar();
        } elseif ($action === 'editar') {
            AdminController::editar();
        } elseif ($action === 'salvar') {
            AdminController::salvar();
        } elseif ($action === 'desativar') {
            AdminController::desativar();
        } elseif ($action === 'reativar') {
            AdminController::reativar();
        } elseif ($action === 'excluir') { // ── ROTA DE EXCLUSÃO DE PRODUTO ADICIONADA AQUI ──
            AdminController::excluir();
            
        // ── ROTA DE PEDIDOS: Mapeia perfeitamente a URL para o método pedidos() ──
        } elseif ($action === 'pedidos') {
            AdminController::pedidos();
            
        // ── 🛠️ NOVA ROTA DE FEEDBACKS ADICIONADA AQUI ──
        } elseif ($action === 'feedbacks') {
            AdminController::feedbacks();
            
        } else {
            // Se não passar nenhuma ação válida, renderiza a página principal do painel
            AdminController::index();
        }
        break;

    case 'barista':
        if ($action === 'atualizar') {
            BaristaController::index(); 
        } else {
            BaristaController::index();
        }
        break;

    case 'cliente':
        if ($action === 'finalizarPedido') {
            ClienteController::finalizarPedido();
        } else {
            ClienteController::index();
        }
        break;

    default:
        require_once __DIR__ . '/views/dashboard.php';
        break;
}
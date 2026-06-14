<?php
/**
 * admin.php — Ponto de entrada único do painel administrativo
 * Toda ação do CRUD passa por aqui via ?acao=
 *
 * Coloque este arquivo na RAIZ do projeto: le_cafeteria/admin.php
 */

session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProdutosController.php';

// Despacha para o controller correto
// Por enquanto só temos produtos; futuramente adicionar outros controllers aqui
ProdutosController::despachar();

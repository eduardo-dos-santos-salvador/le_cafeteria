<?php

class BaristaController {
    public static function index() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Controle de acesso: garante que apenas baristas (tipo 2) ou admins acessem
        if (!isset($_SESSION['tipo_user_id']) || ($_SESSION['tipo_user_id'] != 2 && $_SESSION['tipo_user_id'] != 1)) {
            header('Location: /le_cafeteria/index.php?controller=auth&action=login');
            exit;
        }

        // Carrega a view que já faz a busca dos pedidos e dos itens_pedido
        require_once __DIR__ . '/../views/barista.php';
    }
}
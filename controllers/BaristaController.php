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

        // 🚀 O PULO DO GATO: Garante que a variável 'acao' chegue sã e salva na View
        // Se nenhuma ação for informada, o padrão vira 'fila'
        if (!isset($_GET['acao']) && isset($_GET['action'])) {
            $_GET['acao'] = $_GET['action'];
        }
        
        if (!isset($_GET['acao'])) {
            $_GET['acao'] = 'fila';
        }

        // Carrega a view que agora vai ler o $_GET['acao'] reconfigurado corretamente
        require_once __DIR__ . '/../views/barista.php';
    }
}
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pausa.Café - Painel</title>
    
    <link rel="stylesheet" href="/le_cafeteria/assets/css/admin.css">
</head>
<body>
    <header class="header-admin">
        <div class="logo">Pausa.Café</div>
        
        <nav>
            <ul class="menu-admin">
                <?php if (!isset($isBarista)): ?>
                    <li><a href="/le_cafeteria/index.php?controller=admin">Início</a></li>
                    <li><a href="/le_cafeteria/index.php?controller=admin&action=produtos">Cardápio</a></li>
					<li><a href="/le_cafeteria/index.php?controller=admin&action=usuarios">Usuários</a></li>
                    <li><a href="/le_cafeteria/index.php?controller=admin&action=pedidos">Pedidos</a></li>
                    <li><a href="/le_cafeteria/index.php?controller=admin&action=feedbacks">Feedbacks</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="user-box">
            <span>Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Admin'); ?></span>
            <a href="/le_cafeteria/index.php?controller=auth&action=logout" class="btn-logout">Sair</a>
        </div>
    </header>
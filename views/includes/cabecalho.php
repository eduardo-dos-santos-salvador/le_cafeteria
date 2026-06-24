<?php
// Garante que a sessão está ativa para ler os dados do cliente logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário realizou o login ou cadastro
$usuarioLogado = isset($_SESSION['usuario_id']);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pausa.Café</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata&display=swap" rel="stylesheet">    
    <link rel="stylesheet" href="/le_cafeteria/assets/css/styles.css"> 
</head>

<body>

    <nav>
        <div class="nav-container">

            <a href="/le_cafeteria/index.php" class="nav-logo-text">
                Pausa.Café
            </a>
            
            <input type="checkbox" id="menu-check" style="display: none;">
            <label for="menu-check" class="menu-toggle">☰</label>
            
<div class="nav-links <?= !$usuarioLogado ? 'menu-deslogado-centralizado' : '' ?>" id="navLinks">
                <a href="/le_cafeteria/index.php?acao=#home">Home</a>
                <a href="/le_cafeteria/index.php?acao=#about">Sobre</a>
                <a href="/le_cafeteria/index.php?acao=#menu">Menu</a>
                
                <?php if ($usuarioLogado): ?>
                    <a href="/le_cafeteria/views/contato.php">Fale Conosco</a>
                    <a href="/le_cafeteria/views/cliente.php">Meus Pedidos</a>
                <?php endif; ?>
                
                <?php if (!$usuarioLogado): ?>
                    <a href="/le_cafeteria/views/cadastro.php">Cadastre-se</a>
                    <a href="/le_cafeteria/views/login.php">Login</a>
                <?php endif; ?>
            </div>
            
            <div class="nav-actions">

                <?php if ($usuarioLogado): ?>
                
                    <button
                        class="cart-button"
                        id="cartButton"
                        aria-label="Carrinho de compras">

                        <a href="/le_cafeteria/views/carrinho.php">
                            <span class="cart-icon">🛒</span>
                        </a>

                        <span class="cart-counter" id="cartCounter">
                            0
                        </span>
                    </button>

                    <div class="user-box">
                        <span>Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Cliente'); ?></span>
                        <a href="/le_cafeteria/index.php?controller=auth&action=logout" class="btn-logout">Sair</a>
                    </div>
                <?php endif; ?>
                
            </div>   
        </div>
    </nav>
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pausa.Café</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inconsolata&display=swap" rel="stylesheet">    
        <link rel= "stylesheet" href="/le_cafeteria/assets/css/styles.css"> 
    </head>

    <body>

        <!-- BARRA DE NAVEGAÇÃO -->
        <nav>

        <div class="nav-container">

        <!-- Logo -->
        <a href="/le_cafeteria/index.php" class="nav-logo-text">
        Pausa.Café
        </a>
        
        <!-- Links -->
        <div class="nav-links" id="navLinks">
                <a href="/le_cafeteria/index.php?acao=#home">Home</a>
                <a href="/le_cafeteria/index.php?acao=#about">Sobre</a>
                <a href="/le_cafeteria/index.php?acao=#menu">Menu</a>
                <a href="/le_cafeteria/views/cadastro.php">Cadastre-se</a>
                <a href="/le_cafeteria/views/login.php">Login</a>
                <a href="/le_cafeteria/views/contato.php">Fale Conosco</a>
            </div>
        
            <!-- Área Direita da Navbar -->
            <div class="nav-actions">

            <!-- ÍCONE HAMBÚGER --> 
            <button 
                class="menu-toggle" 
                onclick="toggleNav()" 
                aria-label="Abrir menu de navegação" 
                aria-expanded="false" 
                role="button">
                ☰
            </button>

            <!-- CARRINHO -->
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
            
        </div>   
    
        </nav>

       
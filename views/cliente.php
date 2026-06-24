<?php
/**
 * cliente.php — Painel do cliente logado
 * Exibe os pedidos do cliente e permite acompanhar o status
 * Acesso: somente usuários com perfil 'cliente' (ou admin para teste)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /le_cafeteria/index.php?controller=auth&action=login');
    exit;
}

require_once __DIR__ . '/../models/Conexao.php';

$acao       = $_GET['acao'] ?? 'pedidos';
$usuario_id = (int) $_SESSION['usuario_id'];
$usuarioLogado = isset($_SESSION['usuario_id']);

$con  = Conexao::getInstancia();
$sql  = "SELECT p.*
         FROM pedido p
         WHERE p.usuario_id = :uid
         ORDER BY p.criado_em DESC
         LIMIT 50";
$stmt = $con->prepare($sql);
$stmt->bindParam(':uid', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$itensPorPedido = [];
if (!empty($pedidos)) {
    $ids   = implode(',', array_column($pedidos, 'id'));
    
    $sqlIt = "SELECT ip.pedido_id, pr.nome, ip.quantidade, pr.preco
              FROM itens_pedido ip
              JOIN produtos pr ON pr.id = ip.produto_id
              WHERE ip.pedido_id IN ($ids)";
              
    $stmtIt = $con->query($sqlIt);
    foreach ($stmtIt->fetchAll(PDO::FETCH_ASSOC) as $item) {
        $itensPorPedido[$item['pedido_id']][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pausa.Café — Meus Pedidos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="/le_cafeteria/assets/css/styles.css"> 
</head>
<body class="pagina-meus-pedidos-cliente">

<nav>
    <div class="nav-container">
        <a href="/le_cafeteria/index.php" class="nav-logo-text">
            Pausa.Café
        </a>
        
        <input type="checkbox" id="menu-check" style="display: none;">
        <label for="menu-check" class="menu-toggle">☰</label>
        
        <div class="nav-links" id="navLinks">
            <a href="/le_cafeteria/index.php?acao=#home">Home</a>
            <a href="/le_cafeteria/index.php?acao=#about">Sobre</a>
            <a href="/le_cafeteria/index.php?acao=#menu">Menu</a>
            
            <?php if (!$usuarioLogado): ?>
                <a href="/le_cafeteria/views/cadastro.php">Cadastre-se</a>
                <a href="/le_cafeteria/views/login.php">Login</a>
            <?php endif; ?>
            
            <a href="/le_cafeteria/views/contato.php">Fale Conosco</a>
            <a href="/le_cafeteria/views/cliente.php">Meus Pedidos</a>
        </div>
        
        <div class="nav-actions">
            <?php if ($usuarioLogado): ?>
                <button class="cart-button" id="cartButton" aria-label="Carrinho de compras">
                    <a href="/le_cafeteria/views/carrinho.php">
                        <span class="cart-icon">🛒</span>
                    </a>
                    <span class="cart-counter" id="cartCounter">0</span>
                </button>

                <div class="user-box">
                    <span>Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Cliente'); ?></span>
                    <a href="/le_cafeteria/index.php?controller=auth&action=logout" class="btn-logout">Sair</a>
                </div>
            <?php endif; ?>
        </div>   
    </div>
</nav>

    <div class="wrapper-cliente-esquerda">
        
        <h1 class="view-title-cliente">
            Meus Pedidos
        </h1>

        <?php if (empty($pedidos)): ?>
            <p class="texto-cardapio-centralizado">
                Você ainda não fez nenhum pedido. <a href="/le_cafeteria/index.php#menu">Veja nosso cardápio!</a>
            </p>
        <?php else: ?>
            <div class="pedidos-grid">
                <?php foreach ($pedidos as $ped): ?>
                    <div class="pedido-card">
                        <div class="cabecalho">
                            <span class="numero">Pedido #<?= $ped['id'] ?></span>
                            <span class="status-badge status-<?= $ped['status'] ?>"><?= $ped['status'] ?></span>
                        </div>
                        
                        <span class="hora">Análise criada em: <?= date('d/m/Y H:i', strtotime($ped['criado_em'])) ?></span>

                        <ul class="itens-pedido">
                            <?php foreach ($itensPorPedido[$ped['id']] ?? [] as $it): ?>
                                <li>
                                    <?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?>
                                    — R$ <?= number_format($it['preco'] * $it['quantidade'], 2, ',', '.') ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <span class="total-pedido">
                            Total: R$ <?= number_format($ped['total'], 2, ',', '.') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <p class="texto-cardapio-centralizado">
                Quer fazer um novo pedido? <a href="/le_cafeteria/index.php#menu">Veja nosso cardápio!</a>
            </p>
        <?php endif; ?>

    </div>

</body>
</html>
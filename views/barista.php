<?php
/**
 * barista.php — Painel do barista (Versão ID Numérico)
 */

$caminhoConexao = $_SERVER['DOCUMENT_ROOT'] . '/le_cafeteria/models/Conexao.php';
if (file_exists($caminhoConexao)) {
    require_once $caminhoConexao;
} else {
    die("Erro Crítico: Não foi possível encontrar o arquivo Conexao.php");
}

$acao = $_GET['acao'] ?? $_GET['action'] ?? 'fila';

// Mapeamento dos IDs do Banco de Dados
define('STATUS_AGUARDANDO', 1);
define('STATUS_PREPARANDO', 2);
define('STATUS_PRONTO', 3);
define('STATUS_ENTREGUE', 4);
define('STATUS_CANCELADO', 5);

// Processa a atualização de status vinda dos botões
if (isset($_GET['acao']) && $_GET['acao'] === 'atualizar' && isset($_GET['id'], $_GET['status_id'])) {
    $novoStatusId = filter_var($_GET['status_id'], FILTER_VALIDATE_INT);
    $pedidoId     = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($pedidoId && $novoStatusId >= 1 && $novoStatusId <= 5) {
        $con  = Conexao::getInstancia();
        $stmt = $con->prepare("UPDATE pedido SET status_id = :s WHERE id = :id");
        $stmt->bindParam(':s',  $novoStatusId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $pedidoId,   PDO::PARAM_INT);
        $stmt->execute();
    }
    
    header('Location: /le_cafeteria/index.php?controller=barista&acao=fila');
    exit;
}

$con = Conexao::getInstancia();

// Consultas agora filtram por status_id e trazem a descrição textual via JOIN para a badge
if ($acao === 'prontos') {
    $sql    = "SELECT p.*, COALESCE(u.nome, 'Cliente Não Encontrado') AS cliente, s.desc_status
               FROM pedido p 
               LEFT JOIN usuario u ON u.id = p.usuario_id
               JOIN status_pedido s ON s.id = p.status_id
               WHERE p.status_id = " . STATUS_PRONTO . "
               ORDER BY p.atualizado_em DESC LIMIT 50";
    $titulo = '✅ Pedidos Prontos';
} else {
    $sql    = "SELECT p.*, COALESCE(u.nome, 'Cliente Não Encontrado') AS cliente, s.desc_status
               FROM pedido p 
               LEFT JOIN usuario u ON u.id = p.usuario_id
               JOIN status_pedido s ON s.id = p.status_id
               WHERE p.status_id IN (" . STATUS_AGUARDANDO . ", " . STATUS_PREPARANDO . ")
               ORDER BY p.criado_em ASC LIMIT 50";
    $titulo = '☕ Fila de Pedidos';
}

$stmt    = $con->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$itensPorPedido = [];
if (!empty($pedidos)) {
    $ids     = implode(',', array_column($pedidos, 'id'));
    $sqlIt   = "SELECT ip.pedido_id, pr.nome, ip.quantidade
                FROM itens_pedido ip
                JOIN produtos pr ON pr.id = ip.produto_id
                WHERE ip.pedido_id IN ($ids)";
    $stmtIt  = $con->query($sqlIt);
    foreach ($stmtIt->fetchAll(PDO::FETCH_ASSOC) as $item) {
        $itensPorPedido[$item['pedido_id']][] = $item;
    }
}

$isBarista = true;

$caminhoCabecalho = $_SERVER['DOCUMENT_ROOT'] . '/le_cafeteria/views/admin/cabecalho_admin.php';
if (file_exists($caminhoCabecalho)) {
    require_once $caminhoCabecalho;
}
?>

<link rel="stylesheet" href="/le_cafeteria/assets/css/admin.css">

<div class="view-container-barista">
    <h1 class="titulo-barista"><?= $titulo ?></h1>

    <div class="abas">
        <a href="/le_cafeteria/index.php?controller=barista&acao=fila" class="aba <?= $acao === 'fila' ? 'ativa' : '' ?>">☕ Fila</a>
        <a href="/le_cafeteria/index.php?controller=barista&acao=prontos" class="aba <?= $acao === 'prontos' ? 'ativa' : '' ?>">✅ Prontos</a>
    </div>

    <?php if (empty($pedidos)): ?>
        <p class="empty-list-text">Nenhum pedido nesta fila no momento. 😊</p>
    <?php else: ?>

    <div class="pedidos-grid">
        <?php foreach ($pedidos as $ped): ?>
        <div class="pedido-card">
            <div class="cabecalho">
                <span class="numero">Pedido #<?= $ped['id'] ?></span>
                <span class="status-badge status-<?= htmlspecialchars($ped['desc_status']) ?>">
                    <?= htmlspecialchars(ucfirst($ped['desc_status'])) ?>
                </span>
            </div>

            <span class="cliente">👤 <?= htmlspecialchars($ped['cliente']) ?></span>
            <span class="hora">🕐 <?= date('H:i', strtotime($ped['criado_em'])) ?></span>

            <span class="pagamento-tag">
                💳 
                <?php 
                    switch(strtolower($ped['forma_pagto'] ?? '')) {
                        case 'pix': echo 'Pix'; break;
                        case 'credito': echo 'Cartão de Crédito'; break;
                        case 'debito': echo 'Cartão de Débito'; break;
                        case 'dinheiro': echo 'Dinheiro / Caixa'; break;
                        default: echo htmlspecialchars(ucfirst($ped['forma_pagto'] ?? 'Não informada'));
                    }
                ?>
            </span>

            <ul class="itens-pedido">
                <?php foreach ($itensPorPedido[$ped['id']] ?? [] as $it): ?>
                    <li><?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?></li>
                <?php endforeach; ?>
            </ul>

            <div class="card-acoes">
    <?php if ((int)$ped['status_id'] === STATUS_AGUARDANDO): ?>
        <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status_id=<?= STATUS_PREPARANDO ?>"
           class="btn-card btn-preparar">▶ Preparar</a>
           
    <?php elseif ((int)$ped['status_id'] === STATUS_PREPARANDO): ?>
        <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status_id=<?= STATUS_PRONTO ?>"
           class="btn-card btn-pronto">✅ Pronto</a>
           
    <?php elseif ((int)$ped['status_id'] === STATUS_PRONTO): ?>
        <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status_id=<?= STATUS_ENTREGUE ?>"
           class="btn-card btn-entregue" style="background: var(--purple-bg); color: var(--purple-cor); padding: 6px 10px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 0.85rem; text-align: center;">
           📦 Entregue
        </a>
    <?php endif; ?>

    <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status_id=<?= STATUS_CANCELADO ?>"
       class="btn-card btn-cancelar">
        ✖ Cancelar
    </a>
</div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
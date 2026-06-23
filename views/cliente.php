<?php
/**
 * cliente.php — Painel do cliente logado
 * Exibe os pedidos do cliente e permite acompanhar o status
 * Acesso: somente usuários com perfil 'cliente' (ou admin para teste)
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /le_cafeteria/index.php?controller=auth&action=login');
    exit;
}

require_once __DIR__ . '/../models/Conexao.php';

$acao       = $_GET['acao'] ?? 'pedidos';
$usuario_id = (int) $_SESSION['usuario_id'];

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

require_once __DIR__ . '/../views/admin/cabecalho_admin.php';
?>

<h1 class="view-title-cliente">
    🛍️ Meus Pedidos
</h1>

<?php if (empty($pedidos)): ?>
    <p class="empty-list-text">
        Você ainda não fez nenhum pedido. <a href="/le_cafeteria/index.php#menu">Veja nosso cardápio!</a>
    </p>
<?php else: ?>
<div class="pedidos-grid">
    <?php foreach ($pedidos as $ped): ?>
    <div class="pedido-card">
        <div class="card-header-flex">
            <span class="numero">Pedido #<?= $ped['id'] ?></span>
            <span class="status-badge status-<?= $ped['status'] ?>"><?= $ped['status'] ?></span>
        </div>
        <span class="hora">🕐 <?= date('d/m/Y H:i', strtotime($ped['criado_em'])) ?></span>

        <ul class="itens-pedido">
    <?php foreach ($itensPorPedido[$ped['id']] ?? [] as $it): ?>
        <li>
            <?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?>
            — R$ <?= number_format($it['preco'] * $it['quantidade'], 2, ',', '.') ?>
        </li>
    <?php endforeach; ?>
</ul>

        <span class="total-pedido">Total: R$ <?= number_format($ped['total'], 2, ',', '.') ?></span>
    </div>
    <?php endforeach; ?>
</div>

<p class="empty-list-text">
        Quer fazer um pedido?. <a href="/le_cafeteria/index.php#menu">Veja nosso cardápio!</a>
</p>
<?php endif; ?>

</div></body>
</html>
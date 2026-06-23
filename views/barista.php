<?php
/**
 * barista.php — Painel do barista
 * Exibe pedidos em fila (aguardando / preparando) e permite atualizar o status
 * Acesso: somente usuários com perfil 'barista' ou 'admin'
 */

require_once __DIR__ . '/../models/Conexao.php';

$acao = $_GET['acao'] ?? $_GET['action'] ?? 'fila';

define('STATUS_AGUARDANDO', 1);
define('STATUS_PREPARANDO', 2);
define('STATUS_PRONTO', 3);
define('STATUS_CANCELADO', 4);

if (isset($_GET['acao']) && $_GET['acao'] === 'atualizar' && isset($_GET['id'], $_GET['status'])) {
    $statusValidos = ['preparando', 'pronto', 'cancelado'];
    $novoStatus    = $_GET['status'];
    $pedidoId      = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($pedidoId && in_array($novoStatus, $statusValidos)) {
        $con  = Conexao::getInstancia();
        $stmt = $con->prepare("UPDATE pedido SET status = :s WHERE id = :id");
        $stmt->bindParam(':s',  $novoStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $pedidoId,   PDO::PARAM_INT);
        $stmt->execute();
    }
    
    header('Location: /le_cafeteria/index.php?controller=barista&acao=fila');
    exit;
}

$con = Conexao::getInstancia();

if ($acao === 'prontos') {
    $sql    = "SELECT p.*, u.nome AS cliente
               FROM pedido p JOIN usuario u ON u.id = p.usuario_id
               WHERE p.status = 'pronto'
               ORDER BY p.atualizado_em DESC LIMIT 50";
    $titulo = '✅ Pedidos Prontos';
} else {
    $sql    = "SELECT p.*, u.nome AS cliente
               FROM pedido p JOIN usuario u ON u.id = p.usuario_id
               WHERE p.status IN ('aguardando', 'preparando')
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

// ... todo o seu código PHP existente do barista ...
require_once __DIR__ . '/../views/admin/cabecalho_admin.php';
?>

<link rel="stylesheet" href="/le_cafeteria/assets/css/admin.css">

<div class="view-container-barista">
    <?= $titulo ?>
</h1>

<div class="abas">
    <a href="/le_cafeteria/index.php?controller=barista&acao=fila" class="aba <?= $acao === 'fila' ? 'ativa' : '' ?>">☕ Fila</a>
    <a href="/le_cafeteria/index.php?controller=barista&acao=prontos" class="aba <?= $acao === 'prontos' ? 'ativa' : '' ?>">✅ Prontos</a>
</div>

<?php if (empty($pedidos)): ?>
    <p class="empty-list-text">
        Nenhum pedido nesta fila no momento. 😊
    </p>
<?php else: ?>

<div class="pedidos-grid">
    <?php foreach ($pedidos as $ped): ?>
    <div class="pedido-card">
        <div class="cabecalho">
            <span class="numero">Pedido #<?= $ped['id'] ?></span>
            <span class="status-badge status-<?= htmlspecialchars($ped['status']) ?>">
                <?= htmlspecialchars($ped['status']) ?>
            </span>
        </div>

        <span class="cliente">👤 <?= htmlspecialchars($ped['cliente']) ?></span>
        <span class="hora">🕐 <?= date('H:i', strtotime($ped['criado_em'])) ?></span>

        <ul class="itens-pedido">
            <?php foreach ($itensPorPedido[$ped['id']] ?? [] as $it): ?>
                <li><?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="card-acoes">
            <?php if ($ped['status'] === 'aguardando'): ?>
                <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status=preparando"
                   class="btn-card btn-preparar">▶ Preparar</a>
            <?php elseif ($ped['status'] === 'preparando'): ?>
                <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status=pronto"
                   class="btn-card btn-pronto">✅ Pronto</a>
            <?php endif; ?>

            <a href="/le_cafeteria/index.php?controller=barista&acao=atualizar&id=<?= $ped['id'] ?>&status=cancelado"
               class="btn-card btn-cancelar"
               onclick="return confirm('Cancelar pedido #<?= $ped['id'] ?>?')">
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
<?php
/**
 * cliente.php — Painel do cliente logado
 * Exibe os pedidos do cliente e permite acompanhar o status
 * Acesso: somente usuários com perfil 'cliente' (ou admin para teste)
 */

session_start();
require_once __DIR__ . '/controllers/AuthController.php';

// Protege a rota: apenas clientes autenticados
if (empty($_SESSION['usuario_id'])) {
    header('Location: /le_cafeteria/views/login.php');
    exit;
}

require_once __DIR__ . '/models/Conexao.php';

$acao       = $_GET['acao'] ?? 'pedidos';
$usuario_id = (int) $_SESSION['usuario_id'];

// ── Busca os pedidos do cliente logado ────────────────────────────
$con  = Conexao::getConexao();
$sql  = "SELECT p.*
         FROM pedido p
         WHERE p.usuario_id = :uid
         ORDER BY p.criado_em DESC
         LIMIT 50";
$stmt = $con->prepare($sql);
$stmt->bindParam(':uid', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Busca itens de cada pedido ────────────────────────────────────
$itensPorPedido = [];
if (!empty($pedidos)) {
    $ids   = implode(',', array_column($pedidos, 'id'));
    $sqlIt = "SELECT ip.pedido_id, pr.nome, ip.quantidade, ip.preco_unit
              FROM itens_pedido ip
              JOIN produtos pr ON pr.id = ip.produto_id
              WHERE ip.pedido_id IN ($ids)";
    $stmtIt = $con->query($sqlIt);
    foreach ($stmtIt->fetchAll(PDO::FETCH_ASSOC) as $item) {
        $itensPorPedido[$item['pedido_id']][] = $item;
    }
}

// ── Cabeçalho com menu dinâmico ───────────────────────────────────
require_once __DIR__ . '/views/admin/cabecalho_admin.php';
?>

<style>
    .pedidos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
        margin-top: 1rem;
    }
    .pedido-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .pedido-card .numero   { font-weight: 700; font-size: 1rem; color: var(--cafe-escuro); }
    .pedido-card .hora     { font-size: 0.78rem; color: #999; }
    .status-badge {
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: capitalize;
        display: inline-block;
    }
    .status-aguardando { background: #FFF8E1; color: #F57F17; }
    .status-preparando { background: #E3F2FD; color: #1565C0; }
    .status-pronto     { background: #E8F5E9; color: #2E7D32; }
    .status-entregue   { background: #EDE7F6; color: #4527A0; }
    .status-cancelado  { background: #FFEBEE; color: #C62828; }
    .itens-pedido      { font-size: 0.88rem; color: #444; line-height: 1.7; }
    .itens-pedido li   { list-style: disc; margin-left: 1.2rem; }
    .total-pedido      { font-weight: 700; color: var(--cafe-escuro); }
</style>

<h1 style="font-size:1.5rem; color:var(--cafe-escuro); margin-bottom:1.25rem;">
    🛍️ Meus Pedidos
</h1>

<?php if (empty($pedidos)): ?>
    <p style="text-align:center; padding:3rem; color:#888;">
        Você ainda não fez nenhum pedido. <a href="/le_cafeteria/index.php#menu">Veja nosso cardápio!</a>
    </p>
<?php else: ?>
<div class="pedidos-grid">
    <?php foreach ($pedidos as $ped): ?>
    <div class="pedido-card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span class="numero">Pedido #<?= $ped['id'] ?></span>
            <span class="status-badge status-<?= $ped['status'] ?>"><?= $ped['status'] ?></span>
        </div>
        <span class="hora">🕐 <?= date('d/m/Y H:i', strtotime($ped['criado_em'])) ?></span>

        <ul class="itens-pedido">
            <?php foreach ($itensPorPedido[$ped['id']] ?? [] as $it): ?>
                <li><?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?>
                    — R$ <?= number_format($it['preco_unit'] * $it['quantidade'], 2, ',', '.') ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <span class="total-pedido">Total: R$ <?= number_format($ped['total'], 2, ',', '.') ?></span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

</div><!-- .admin-container -->
</body>
</html>

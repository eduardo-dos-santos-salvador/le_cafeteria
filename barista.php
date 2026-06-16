<?php
/**
 * barista.php — Painel do barista
 * Exibe pedidos em fila (aguardando / preparando) e permite atualizar o status
 * Acesso: somente usuários com perfil 'barista' ou 'admin'
 */

session_start();
require_once __DIR__ . '/controllers/AuthController.php';

// Protege a rota: apenas barista ou admin
if (
    empty($_SESSION['usuario_id']) ||
    !in_array($_SESSION['usuario_tipo'] ?? '', ['barista', 'admin'])
) {
    header('Location: /le_cafeteria/views/login.php');
    exit;
}

require_once __DIR__ . '/models/Conexao.php';

$acao = $_GET['acao'] ?? 'fila';

// ── Atualiza status de um pedido ──────────────────────────────────
if ($acao === 'atualizar' && isset($_GET['id'], $_GET['status'])) {
    $statusValidos = ['preparando', 'pronto', 'cancelado'];
    $novoStatus    = $_GET['status'];
    $pedidoId      = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($pedidoId && in_array($novoStatus, $statusValidos)) {
        $con  = Conexao::getConexao();
        $stmt = $con->prepare("UPDATE pedido SET status = :s WHERE id = :id");
        $stmt->bindParam(':s',  $novoStatus, PDO::PARAM_STR);
        $stmt->bindParam(':id', $pedidoId,   PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['msg_sucesso'] = "Pedido #$pedidoId atualizado para «$novoStatus».";
    }
    header('Location: /le_cafeteria/barista.php?acao=fila');
    exit;
}

// ── Busca os pedidos conforme a aba ──────────────────────────────
$con = Conexao::getConexao();

if ($acao === 'prontos') {
    $sql    = "SELECT p.*, u.nome AS cliente
               FROM pedido p JOIN usuario u ON u.id = p.usuario_id
               WHERE p.status = 'pronto'
               ORDER BY p.atualizado_em DESC LIMIT 50";
    $titulo = '✅ Pedidos Prontos';
} else {
    // Fila padrão: aguardando + preparando
    $sql    = "SELECT p.*, u.nome AS cliente
               FROM pedido p JOIN usuario u ON u.id = p.usuario_id
               WHERE p.status IN ('aguardando','preparando')
               ORDER BY p.criado_em ASC LIMIT 50";
    $titulo = '☕ Fila de Pedidos';
}

$stmt    = $con->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Busca itens de cada pedido ────────────────────────────────────
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

// ── Inclui o cabeçalho (com menu dinâmico do banco) ──────────────
require_once __DIR__ . '/views/admin/cabecalho_admin.php';
?>

<style>
    /* ── Grade de cards de pedidos ── */
    .pedidos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-top: 1rem;
    }

    /* ── Card de pedido ── */
    .pedido-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .pedido-card .cabecalho {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .pedido-card .numero { font-weight: 700; font-size: 1rem; color: var(--cafe-escuro); }
    .pedido-card .cliente { font-size: 0.85rem; color: #555; }
    .pedido-card .hora    { font-size: 0.78rem; color: #999; }

    /* Badge de status colorido */
    .status-badge {
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: capitalize;
    }
    .status-aguardando { background: #FFF8E1; color: #F57F17; }
    .status-preparando { background: #E3F2FD; color: #1565C0; }
    .status-pronto     { background: #E8F5E9; color: #2E7D32; }

    /* Lista de itens do pedido */
    .itens-pedido { font-size: 0.88rem; color: #444; line-height: 1.7; }
    .itens-pedido li { list-style: disc; margin-left: 1.2rem; }

    /* Botões de ação do card */
    .card-acoes { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem; }
    .btn-card {
        flex: 1;
        text-align: center;
        padding: 0.45rem 0.5rem;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-card:hover { opacity: 0.82; }
    .btn-preparar { background: #1565C0; color: #fff; }
    .btn-pronto   { background: var(--sucesso); color: #fff; }
    .btn-cancelar { background: var(--erro); color: #fff; }

    /* Abas de navegação */
    .abas { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
    .aba {
        padding: 0.55rem 1.2rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        background: #E0D6CF;
        color: var(--cafe-escuro);
        transition: background 0.2s;
    }
    .aba.ativa, .aba:hover { background: var(--cafe-escuro); color: var(--cafe-claro); }
</style>

<h1 style="font-size:1.5rem; color:var(--cafe-escuro); margin-bottom:1.25rem;">
    <?= $titulo ?>
</h1>

<!-- Abas de navegação entre fila e prontos -->
<div class="abas">
    <a href="/le_cafeteria/barista.php?acao=fila"    class="aba <?= $acao === 'fila'    ? 'ativa' : '' ?>">☕ Fila</a>
    <a href="/le_cafeteria/barista.php?acao=prontos" class="aba <?= $acao === 'prontos' ? 'ativa' : '' ?>">✅ Prontos</a>
</div>

<?php if (empty($pedidos)): ?>
    <p style="text-align:center; padding:3rem; color:#888;">
        Nenhum pedido nesta fila no momento. 😊
    </p>
<?php else: ?>

<div class="pedidos-grid">
    <?php foreach ($pedidos as $ped): ?>
    <div class="pedido-card">
        <div class="cabecalho">
            <span class="numero">Pedido #<?= $ped['id'] ?></span>
            <span class="status-badge status-<?= $ped['status'] ?>">
                <?= $ped['status'] ?>
            </span>
        </div>

        <span class="cliente">👤 <?= htmlspecialchars($ped['cliente']) ?></span>
        <span class="hora">🕐 <?= date('H:i', strtotime($ped['criado_em'])) ?></span>

        <!-- Itens do pedido -->
        <ul class="itens-pedido">
            <?php foreach ($itensPorPedido[$ped['id']] ?? [] as $it): ?>
                <li><?= $it['quantidade'] ?>x <?= htmlspecialchars($it['nome']) ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Botões de ação conforme o status atual -->
        <div class="card-acoes">
            <?php if ($ped['status'] === 'aguardando'): ?>
                <a href="/le_cafeteria/barista.php?acao=atualizar&id=<?= $ped['id'] ?>&status=preparando"
                   class="btn-card btn-preparar">▶ Preparar</a>
            <?php elseif ($ped['status'] === 'preparando'): ?>
                <a href="/le_cafeteria/barista.php?acao=atualizar&id=<?= $ped['id'] ?>&status=pronto"
                   class="btn-card btn-pronto">✅ Pronto</a>
            <?php endif; ?>

            <a href="/le_cafeteria/barista.php?acao=atualizar&id=<?= $ped['id'] ?>&status=cancelado"
               class="btn-card btn-cancelar"
               onclick="return confirm('Cancelar pedido #<?= $ped['id'] ?>?')">
               ✖ Cancelar
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

</div><!-- .admin-container -->
</body>
</html>

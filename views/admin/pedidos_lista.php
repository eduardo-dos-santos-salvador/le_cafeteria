<?php
/**
 * pedidos_lista.php — Listagem geral de pedidos para o admin
 */
require_once __DIR__ . '/cabecalho_admin.php';

// Labels e cores de status
$badges = [
    'aguardando' => ['bg'=>'#FFF8E1','cor'=>'#F57F17'],
    'preparando' => ['bg'=>'#E3F2FD','cor'=>'#1565C0'],
    'pronto'     => ['bg'=>'#E8F5E9','cor'=>'#2E7D32'],
    'entregue'   => ['bg'=>'#EDE7F6','cor'=>'#4527A0'],
    'cancelado'  => ['bg'=>'#FFEBEE','cor'=>'#C62828'],
];
?>
<style>
    .tabela-wrapper { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow-x:auto; }
    table { width:100%; border-collapse:collapse; min-width:580px; }
    thead { background:var(--cafe-escuro); color:var(--cafe-claro); }
    thead th { padding:.85rem 1rem; text-align:left; font-size:.82rem; letter-spacing:1px; }
    tbody tr { border-bottom:1px solid var(--borda); transition:background .15s; }
    tbody tr:hover { background:#FAF7F4; }
    tbody td { padding:.72rem 1rem; vertical-align:middle; font-size:.9rem; }
    .badge { display:inline-block; padding:.22rem .6rem; border-radius:999px; font-size:.75rem; font-weight:700; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem; color:var(--cafe-escuro);">📋 Todos os Pedidos</h1>
</div>

<?php if (empty($pedidos)): ?>
    <p style="text-align:center; padding:3rem; color:#888;">Nenhum pedido registrado.</p>
<?php else: ?>
<div class="tabela-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Cliente</th><th>Total</th><th>Status</th><th>Data</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pedidos as $p):
            $b = $badges[$p['status']] ?? ['bg'=>'#eee','cor'=>'#333'];
        ?>
            <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['cliente']) ?></td>
                <td><strong>R$ <?= number_format($p['total'], 2, ',', '.') ?></strong></td>
                <td>
                    <span class="badge" style="background:<?= $b['bg'] ?>; color:<?= $b['cor'] ?>">
                        <?= $p['status'] ?>
                    </span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

</div></body></html>

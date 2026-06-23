<?php
/**
 * pedidos_lista.php — Listagem geral de pedidos para o admin
 */
require_once __DIR__ . '/cabecalho_admin.php';

// Labels e cores de status (Os mapeamentos agora utilizam classes mapeadas nativamente no admin.css)
$badges = [
    'aguardando' => 'background: var(--alerta-bg); color: var(--alerta);',
    'preparando' => 'background: var(--info-bg); color: var(--info-cor);',
    'pronto'     => 'background: var(--sucesso-bg); color: var(--sucesso);',
    'entregue'   => 'background: var(--purple-bg); color: var(--purple-cor);',
    'cancelado'  => 'background: var(--erro-bg); color: var(--erro);',
];
?>

<div class="pedidos-header-block">
    <h1 class="pedidos-title">📋 Todos os Pedidos</h1>
</div>

<?php if (empty($pedidos)): ?>
    <p class="pedidos-vazio">Nenhum pedido registrado.</p>
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
            $estiloDinamico = $badges[$p['status']] ?? 'background:#eee; color:#333;';
        ?>
            <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['cliente']) ?></td>
                <td><strong>R$ <?= number_format($p['total'], 2, ',', '.') ?></strong></td>
                <td>
                    <span class="badge" style="<?= $estiloDinamico ?>">
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
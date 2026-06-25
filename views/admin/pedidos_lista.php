<?php
/**
 * pedidos_lista.php — Listagem geral de pedidos para o admin
 */
require_once __DIR__ . '/cabecalho_admin.php';

// Labels e cores de status (Mapeamento baseado nas novas descrições de banco vindas da tabela status_pedido)
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
            // 🔄 ATUALIZADO: Agora busca a cor com base em $p['desc_status']
            $statusNome = strtolower($p['desc_status'] ?? '');
            $estiloDinamico = $badges[$statusNome] ?? 'background:#eee; color:#333;';
        ?>
            <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['cliente'] ?? 'Cliente Desconhecido') ?></td>
                <td><strong>R$ <?= number_format($p['total'], 2, ',', '.') ?></strong></td>
                <td>
                    <span class="badge" style="<?= $estiloDinamico ?>">
                        <?= htmlspecialchars(ucfirst($statusNome)) ?>
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
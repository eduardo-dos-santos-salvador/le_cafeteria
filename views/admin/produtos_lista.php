<?php
/**
 * produtos_lista.php — Listagem de produtos para o painel admin
 * Exibe: foto, nome, descrição resumida, preço, status (ativo/inativo) e ações
 */
require_once __DIR__ . '/cabecalho_admin.php';
?>

<style>
    /* ── Cabeçalho da página ── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .page-header h1 { font-size: 1.5rem; color: var(--cafe-escuro); }

    /* ── Botões ── */
    .btn {
        display: inline-block;
        padding: 0.55rem 1.2rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: opacity 0.2s;
    }
    .btn:hover     { opacity: 0.82; }
    .btn-primary   { background: var(--cafe-escuro); color: var(--cafe-claro); }
    .btn-warning   { background: #F57F17; color: #fff; }
    .btn-success   { background: var(--sucesso);     color: #fff; }
    .btn-danger    { background: var(--erro);         color: #fff; }
    .btn-sm        { padding: 0.32rem 0.7rem; font-size: 0.8rem; }

    /* ── Tabela ── */
    .tabela-wrapper {
        background: var(--branco);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        overflow-x: auto;   /* scroll horizontal em telas estreitas */
    }
    table { width: 100%; border-collapse: collapse; min-width: 600px; }
    thead { background: var(--cafe-escuro); color: var(--cafe-claro); }
    thead th { padding: 0.85rem 1rem; text-align: left; font-size: 0.82rem; letter-spacing: 1px; }
    tbody tr { border-bottom: 1px solid var(--borda); transition: background 0.15s; }
    tbody tr:hover { background: #FAF7F4; }
    tbody td { padding: 0.72rem 1rem; vertical-align: middle; font-size: 0.9rem; }

    /* ── Foto do produto ── */
    .foto-produto {
        width: 56px; height: 56px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--borda);
    }
    .sem-foto {
        width: 56px; height: 56px;
        background: #EEE;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: #AAA;
    }

    /* ── Badge de status ── */
    .badge {
        display: inline-block;
        padding: 0.22rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .badge-ativo   { background: #E8F5E9; color: var(--sucesso); }
    .badge-inativo { background: #FFEBEE; color: var(--erro); }

    /* ── Ações ── */
    .acoes { display: flex; gap: 0.4rem; flex-wrap: wrap; }
</style>

<div class="page-header">
    <h1>🧁 Gerenciar Produtos</h1>
    <a href="/le_cafeteria/admin.php?acao=criar" class="btn btn-primary">+ Novo Produto</a>
</div>

<?php if (empty($produtos)): ?>
    <p style="text-align:center; padding:3rem; color:#888;">Nenhum produto cadastrado ainda.</p>
<?php else: ?>

<div class="tabela-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produtos as $p): ?>
            <tr>
                <td>#<?= $p['id'] ?></td>

                <!-- Foto -->
                <td>
                    <?php if (!empty($p['foto'])): ?>
                        <img src="/le_cafeteria/<?= htmlspecialchars($p['foto']) ?>"
                             alt="<?= htmlspecialchars($p['nome']) ?>"
                             class="foto-produto">
                    <?php else: ?>
                        <div class="sem-foto" title="Sem foto">🍽️</div>
                    <?php endif; ?>
                </td>

                <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>

                <!-- Descrição resumida a 80 caracteres -->
                <td style="max-width:240px; color:#666;">
                    <?= htmlspecialchars(mb_strimwidth($p['desc_produto'] ?? '—', 0, 80, '…')) ?>
                </td>

                <td><strong>R$ <?= number_format($p['preco'], 2, ',', '.') ?></strong></td>

                <!-- Badge de status ativo/inativo -->
                <td>
                    <?php if ($p['ativo']): ?>
                        <span class="badge badge-ativo">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-inativo">Inativo</span>
                    <?php endif; ?>
                </td>

                <!-- Botões de ação -->
                <td>
                    <div class="acoes">
                        <a href="/le_cafeteria/admin.php?acao=editar&id=<?= $p['id'] ?>"
                           class="btn btn-warning btn-sm">✏️ Editar</a>

                        <?php if ($p['ativo']): ?>
                            <a href="/le_cafeteria/admin.php?acao=desativar&id=<?= $p['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Desativar «<?= htmlspecialchars(addslashes($p['nome'])) ?>»?')">
                               🗑️ Desativar
                            </a>
                        <?php else: ?>
                            <a href="/le_cafeteria/admin.php?acao=reativar&id=<?= $p['id'] ?>"
                               class="btn btn-success btn-sm">✅ Reativar</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

</div><!-- .admin-container -->
</body>
</html>

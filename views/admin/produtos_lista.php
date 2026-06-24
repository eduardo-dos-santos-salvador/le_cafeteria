<?php
/**
 * produtos_lista.php — Listagem de produtos para o painel admin com exclusão permanente
 */
require_once __DIR__ . '/cabecalho_admin.php';
?>

<div class="page-header">
    <h1>🧁 Gerenciar Produtos</h1>
    <a href="/le_cafeteria/index.php?controller=admin&acao=criar" class="btn btn-primary">+ Novo Produto</a>
</div>

<?php if (empty($produtos)): ?>
    <p class="prod-lista-vazio">Nenhum produto cadastrado ainda.</p>
<?php else: ?>

<div class="tabela-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produtos as $p): 
            $isComida = (strpos($p['desc_produto'] ?? '', '[TIPO:comida]') !== false);
            $isBebida = (strpos($p['desc_produto'] ?? '', '[TIPO:bebida]') !== false);
            
            $tipoTexto = 'Beber';
            $tipoClasse = 'badge-beber';
            if ($isComida || (!$isBebida && (int)$p['id'] <= 6)) {
                $tipoTexto = 'Comer';
                $tipoClasse = 'badge-comer';
            }

            $descLimpa = trim(preg_replace('/\[TIPO:(comida|bebida)\]/', '', $p['desc_produto'] ?? ''));
            if (empty($descLimpa)) { $descLimpa = '—'; }
        ?>
            <tr>
                <td>#<?= $p['id'] ?></td>

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

                <td>
                    <span class="badge <?= $tipoClasse ?>"><?= $tipoTexto ?></span>
                </td>

                <td class="prod-lista-td-desc">
                    <?= htmlspecialchars(mb_strimwidth($descLimpa, 0, 80, '…')) ?>
                </td>

                <td><strong>R$ <?= number_format($p['preco'], 2, ',', '.') ?></strong></td>

                <td>
                    <?php if ($p['ativo']): ?>
                        <span class="badge badge-ativo">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-inativo">Inativo</span>
                    <?php endif; ?>
                </td>

                <td>
                    <div class="acoes">
                        <a href="/le_cafeteria/index.php?controller=admin&acao=editar&id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>

                        <?php if ($p['ativo']): ?>
                            <a href="/le_cafeteria/index.php?controller=admin&acao=desativar&id=<?= $p['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Desativar «<?= htmlspecialchars(addslashes($p['nome'])) ?>»?')">
                                🗑️ Desativar
                            </a>
                        <?php else: ?>
                            <a href="/le_cafeteria/index.php?controller=admin&acao=reativar&id=<?= $p['id'] ?>" 
                               class="btn btn-success btn-sm">
                                 ✅ Reativar
                            </a>
                        <?php endif; ?>

<a href="/le_cafeteria/index.php?controller=admin&action=excluir&id=<?= $p['id'] ?>"
   class="btn btn-delete-perm btn-sm"
   onclick="return confirm('PERIGO: Tem certeza que deseja apagar permanentemente o produto «<?= htmlspecialchars(addslashes($p['nome'])) ?>» do banco de dados? Essa ação não pode ser desfeita!')">
   ❌ Excluir
</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

</div></body>
</html>

<?php
require_once __DIR__ . '/cabecalho_admin.php';

// Define se é criação ou edição
$editando = isset($produto) && !empty($produto);
$titulo   = $editando ? '✏️ Editar Produto' : '➕ Novo Produto';

// Dados a exibir: prioriza valores com erro (redigitados), depois os do banco
$val = [
    'nome'         => htmlspecialchars($antigos['nome']         ?? $produto['nome']         ?? ''),
    'desc_produto' => htmlspecialchars($antigos['desc_produto'] ?? $produto['desc_produto'] ?? ''),
    'preco'        => htmlspecialchars($antigos['preco']        ?? $produto['preco']        ?? ''),
];
?>

<style>
    .form-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        padding: 2rem;
        max-width: 680px;
    }
    .form-card h1 { font-size: 1.4rem; color: var(--cafe-escuro); margin-bottom: 1.5rem; }

    .campo { margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.4rem; }
    .campo label { font-size: 0.875rem; font-weight: 600; color: #444; }
    .campo input,
    .campo textarea {
        padding: 0.7rem 1rem;
        border: 1px solid var(--borda);
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .campo input:focus,
    .campo textarea:focus {
        outline: none;
        border-color: var(--cafe-medio);
        box-shadow: 0 0 0 3px rgba(141,110,99,0.15);
    }
    .campo textarea { min-height: 110px; resize: vertical; }
    .campo .hint { font-size: 0.78rem; color: #888; }

    /* Preview da foto atual */
    .foto-atual { margin-top: 0.5rem; }
    .foto-atual img { width: 90px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid var(--borda); }

    /* Erros de validação */
    .lista-erros {
        background: #FFEBEE;
        border-left: 4px solid var(--erro);
        padding: 0.85rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.25rem;
        color: var(--erro);
        font-size: 0.9rem;
    }
    .lista-erros ul { padding-left: 1.2rem; }
    .lista-erros li { margin-top: 0.3rem; }

    /* Botões */
    .form-actions { display: flex; gap: 1rem; margin-top: 1.5rem; }
    .btn { display: inline-block; padding: 0.6rem 1.4rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: opacity 0.2s; }
    .btn:hover { opacity: 0.85; }
    .btn-primary { background: var(--cafe-escuro); color: var(--cafe-claro); }
    .btn-secondary { background: #eee; color: #333; }
</style>

<div class="form-card">
    <h1><?= $titulo ?></h1>

    <?php if (!empty($erros)): ?>
        <div class="lista-erros">
            <strong>Corrija os erros abaixo:</strong>
            <ul>
                <?php foreach ($erros as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        action="/le_cafeteria/admin.php?acao=salvar"
        method="POST"
        enctype="multipart/form-data">

        <!-- ID oculto (apenas na edição) -->
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
        <?php endif; ?>

        <!-- NOME -->
        <div class="campo">
            <label for="nome">Nome do Produto *</label>
            <input
                type="text"
                id="nome"
                name="nome"
                value="<?= $val['nome'] ?>"
                maxlength="100"
                required
                placeholder="Ex: Croissant de Manteiga">
        </div>

        <!-- DESCRIÇÃO -->
        <div class="campo">
            <label for="desc_produto">Descrição / Ingredientes</label>
            <textarea
                id="desc_produto"
                name="desc_produto"
                maxlength="500"
                placeholder="Descreva os ingredientes e diferenciais do produto..."><?= $val['desc_produto'] ?></textarea>
            <span class="hint">Máximo 500 caracteres.</span>
        </div>

        <!-- PREÇO -->
        <div class="campo">
            <label for="preco">Preço (R$) *</label>
            <input
                type="number"
                id="preco"
                name="preco"
                value="<?= $val['preco'] ?>"
                min="0.01"
                step="0.01"
                required
                placeholder="Ex: 9.90">
        </div>

        <!-- FOTO -->
        <div class="campo">
            <label for="foto">Foto do Produto (JPG, PNG ou WebP — máx. 2 MB)</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">

            <?php if ($editando && !empty($produto['foto'])): ?>
                <div class="foto-atual">
                    <span class="hint">Foto atual:</span><br>
                    <img
                        src="/le_cafeteria/<?= htmlspecialchars($produto['foto']) ?>"
                        alt="Foto atual do produto">
                    <p class="hint">Envie uma nova imagem para substituir.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- BOTÕES -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $editando ? '💾 Salvar Alterações' : '✅ Cadastrar Produto' ?>
            </button>
            <a href="/le_cafeteria/admin.php?acao=listar" class="btn btn-secondary">Cancelar</a>
        </div>

    </form>
</div>

</div><!-- .admin-container -->
</body>
</html>

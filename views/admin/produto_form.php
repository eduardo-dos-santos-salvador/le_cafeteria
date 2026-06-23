<?php 
/**
 * produto_form.php — Versão Final com Extração de Categoria via Descrição
 */
require_once __DIR__ . '/cabecalho_admin.php'; 

$id_produto = $produto['id'] ?? '';
$nome_produto = $produto['nome'] ?? '';
$desc_original = $produto['desc_produto'] ?? '';
$foto_produto = $produto['foto'] ?? '';

// CAPTURA A CATEGORIA ESCONDIDA NA DESCRIÇÃO:
$categoria_produto = 'bebida'; 
if (strpos($desc_original, '[TIPO:comida]') !== false) {
    $categoria_produto = 'comida';
}

$desc_produto = trim(preg_replace('/\[TIPO:(comida|bebida)\]/', '', $desc_original));

$preco_produto = '';
if (isset($produto['preco']) && is_numeric($produto['preco'])) {
    $preco_produto = number_format($produto['preco'], 2, ',', '');
}

$tituloForm = (!empty($id_produto)) ? "Editar Produto #" . $id_produto : "Adicionar Novo Produto";
?>

<div class="admin-form-container">
    <div class="admin-form-title">
        <span><?= mb_strtoupper($tituloForm, 'UTF-8') ?></span>
    </div>
    
    <form action="/le_cafeteria/index.php?controller=admin&action=salvar" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_produto) ?>">
        <input type="hidden" name="foto_atual" value="<?= htmlspecialchars($foto_produto) ?>">

        <div class="admin-input-block">
            <label for="nome">Nome do Produto</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome_produto) ?>" placeholder="Ex: Cappuccino Italiano" required>
        </div>

        <div class="admin-input-block">
            <label for="categoria">Tipo do Produto</label>
            <select id="categoria" name="categoria" required>
                <option value="bebida" <?= $categoria_produto === 'bebida' ? 'selected' : '' ?>>De beber (Bebidas / Cafés)</option>
                <option value="comida" <?= $categoria_produto === 'comida' ? 'selected' : '' ?>>De comer (Lanches / Doces)</option>
            </select>
        </div>

        <div class="admin-input-block">
            <label for="desc_produto">Descrição</label>
            <textarea id="desc_produto" name="desc_produto" placeholder="Descreva os ingredientes ou detalhes do produto..."><?= htmlspecialchars($desc_produto) ?></textarea>
        </div>

        <div class="admin-input-block">
            <label for="preco">Preço (R$)</label>
            <input type="text" id="preco" name="preco" value="<?= htmlspecialchars($preco_produto) ?>" placeholder="Ex: 8,50" required>
        </div>

        <div class="admin-input-block">
            <label for="foto">Foto do Produto</label>
            <input type="file" id="foto" name="foto" accept="image/*">
            
            <?php if (!empty($foto_produto)): ?>
                <div class="prod-form-img-box">
                    <span class="prod-form-img-label">Imagem atual do produto:</span>
                    <img src="/le_cafeteria/<?= htmlspecialchars($foto_produto) ?>" class="prod-form-img-preview">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="admin-btn-submit">Salvar Produto</button>
        
        <div class="admin-form-footer">
            <a href="/le_cafeteria/index.php?controller=admin&action=produtos" class="admin-cancel-link">← Cancelar e Voltar</a>
        </div>
    </form>
</div>

</body>
</html>
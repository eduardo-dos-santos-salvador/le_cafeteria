<?php
include_once 'includes/cabecalho.php';

$token = $_GET['token'] ?? '';
?>

<div class="login-container">
    <div class="section-title">
        <span>CRIAR NOVA SENHA</span>
    </div>

    <?php if (isset($erro)): ?>
        <p class="alert-message-error-simple"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form action="/le_cafeteria/index.php?controller=auth&action=atualizar_senha" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="input-group">
            <label for="senha">Criar nova senha</label>
            <input type="password" id="senha" name="senha" required placeholder="********" minlength="6">
        </div>
		
        <div class="input-group">
            <label for="confirmar_senha">Confirmar nova senha</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="********" minlength="6">
        </div>

        <button type="submit" class="btn-login">Salvar Nova Senha</button>
    </form>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
<?php 
// views/login.php
include_once __DIR__ . '/includes/cabecalho.php'; 
?>

<div class="login-container">
    <div class="section-title"><span>LOGIN</span></div>

    <?php if (isset($erro)): ?>
        <p class="alert-message-error-simple"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form action="/le_cafeteria/index.php?controller=auth&action=login" method="POST">
        <div class="input-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn-login">Entrar</button>
		
		<div class="login-footer-links">
            <a href="/le_cafeteria/views/esqueceuSenha.php" class="forgot-link">Esqueceu sua senha?</a>
        </div>
    </form>
</div>
</body>
</html>
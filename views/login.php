<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container">
    <div class="section-title">
        <span>LOGIN</span>
    </div>


    <form action="login.php" method="POST">
        <div class="input-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="seu@email.com">
        </div>

        <div class="input-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required placeholder="********">
        </div>

        <a href="../index.php?acao=#menu">
        <button type="button" class="btn-login">Entrar</button>
</form>

    <div class="login-footer-links">
        <a href="esqueceuSenha.php" class="forgot-link">Esqueceu sua senha?</a>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
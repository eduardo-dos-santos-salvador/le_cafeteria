<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container">
    <div class="section-title">
        <span>RECUPERAR SENHA</span>
    </div>

    <p>Digite os seus dados abaixo para validar a recuperação de senha.</p>

    <form action="/le_cafeteria/index.php?controller=auth&action=processar_recuperacao" method="POST">
        <div class="input-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="seu@email.com">
        </div>

        <div class="input-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" required placeholder="***.***.***-**">
        </div>

        <div class="input-group">
            <label for="data_nascimento">Data de Nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" required>
        </div>
        
        <button type="submit" class="btn-login">Enviar Link</button>
    </form>

    <div class="login-footer-links">
        <a href="/le_cafeteria/index.php?controller=auth&action=login" class="forgot-link">Voltar para o Login</a>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
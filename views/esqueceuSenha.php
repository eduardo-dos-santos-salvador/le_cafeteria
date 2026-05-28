<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container">
    <div class="section-title">
        <span>RECUPERAR SENHA</span>
    </div>

    <p style="text-align: center; color: #655850; font-size: 14px; margin-bottom: 20px;">
        Digite o seu e-mail cadastrado abaixo para receber as instruções de redefinição de senha.
    </p>

    <form action="enviar-recuperacao.php" method="POST">
        <div class="input-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required placeholder="seu@email.com">
        </div>
		
		<div class="input-group">
            <label for="senha">Criar nova senha</label>
            <input type="password" id="senha" name="senha" required placeholder="********">
        </div>
		
		<div class="input-group">
            <label for="senha">Confirmar nova senha</label>
            <input type="password" id="senha" name="senha" required placeholder="********">
        </div>

<a href="login.php">
        <button type="button" class="btn-login">Enviar Link</button>
    </form>

</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
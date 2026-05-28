<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container">
    <div class="section-title">
        <span>CONTATO & FEEDBACK</span>
    </div>

    <form method="POST">

<div class="input-group">
<label for="nome">Nome Completo</label>
        <input type="text" name="nome" placeholder="Digite seu nome" required>
		</div>
		
		<div class="input-group">
		<label for="nome">E-Mail</label>
        <input type="email" name="email" placeholder="Digite seu e-mail" required>
		</div>
		
		<div class="input-group">
		<label for="nome">Seu feedback</label>
        <textarea name="mensagem" style="box-sizing: border-box; margin: 0px; width: 584px; height: 130px;" placeholder="Digite seu feedback" required=""></textarea>
		</div>
		
		
        <button type="submit" class="btn-login">Enviar Feedback</button>
    </form>

</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container" style="display: block;">
    <div class="section-title">
        <span>FORMA DE PAGAMENTO</span>
    </div>

    <!-- Container com a mesma estética do Cadastro/Menu -->
    <div class="login-container" style="display: block;">
        <form action="#" method="POST" class="signup-form">
            
            <div class="form-group">
                <label style="font-family: 'Inconsolata', sans-serif; font-weight: bold; color: #3E2723; text-transform: uppercase; margin-bottom: 15px;">
                    Como você deseja pagar?
                </label>
                
                <select name="forma_pagamento" required style="background-color: transparent; border: none; border-bottom: 1px solid #ccc; padding: 10px 5px; width: 100%; font-family: 'Inconsolata', sans-serif; font-size: 16px;">
                    <option value="" disabled selected>Escolha a opção de pagamento</option>
                    <option value="pix">Pix (Aprovação imediata)</option>
                    <option value="credito">Cartão de Crédito</option>
                    <option value="debito">Cartão de Débito</option>
                    <option value="dinheiro">Dinheiro / Pagar no Balcão</option>
                </select>
            </div>

            <!-- Botão de Finalização da Compra -->
            <button type="button" class="btn-login" style="width: 100%; margin-top: 30px; border: none;" onclick="limparCarrinho()">
    CONFIRMAR E PAGAR
</button>
			
			<script src="/le_cafeteria/assets/js/script.js"></script>
			</a>
        </form>
    </div>
</div>
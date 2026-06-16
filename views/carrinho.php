<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container" style="margin-top: 90px">
    <div class="section-title">
        <span>SEU CARRINHO</span>
    </div>

    <div class="login-container" style="display: block;">
        <div id="lista-carrinho"></div>

        <div class="cart-total">
            <span>TOTAL:</span>
            <span id="valor-total">R$ 0.00</span>
        </div>

        <form action="tipoPagamento.php" method="GET">
            <button type="submit" class="btn-login" id="btn-pagamento">
                Avançar para o Pagamento
            </button>
        </form>
        
		<div class="pausaCafeCarrinho">
        <button type="button" onclick="limparCarrinho()" class="button-tab-btn">
    ESVAZIAR CARRINHO
</button>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
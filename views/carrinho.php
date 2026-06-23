<?php
include_once 'includes/cabecalho.php';
?>

<div class="carrinho-pagina-exclusiva">
    <div class="section-title">
        <span>SEU CARRINHO</span>
    </div>

    <div class="carrinho-conteudo-interno">
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
        
        <div class="pausaCafeCarrinho" style="margin-top: 15px; text-align: center;">
            <button type="button" onclick="limparCarrinho()" class="button-tab-btn">
                ESVAZIAR CARRINHO
            </button>
        </div>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
<?php
include_once 'includes/cabecalho.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<div class="login-container display-block-wrapper pagamento-wrapper">
    <div class="section-title">
        <span>FORMA DE PAGAMENTO</span>
    </div>

    <div class="login-container display-block-wrapper">
        <form action="/le_cafeteria/controllers/PedidoController.php" method="POST" id="form-finalizar-pagamento" class="signup-form">
            
            <input type="hidden" name="usuario_id" value="<?= $_SESSION['usuario_id'] ?? '' ?>">
            <input type="hidden" name="carrinho_itens" id="carrinho_itens">
            <input type="hidden" name="valor_total" id="valor_total_input">

            <div class="form-group">
                <label class="label-pagamento-custom">
                    Como você deseja pagar?
                </label>
                
                <select name="forma_pagamento" id="select-forma-pagamento" required class="select-pagamento-custom">
                    <option value="" disabled selected>Escolha a opção de pagamento</option>
                    <option value="pix">Pix (Aprovação imediata)</option>
                    <option value="credito">Cartão de Crédito</option>
                    <option value="debito">Cartão de Débito</option>
                    <option value="dinheiro">Dinheiro / Pagar no Balcão</option>
                </select>
            </div>

<div class="form-group pix-box" id="container-chave-pix">
                <label class="label-pagamento-custom pix-box-title">
                    CHAVE PIX PARA PAGAMENTO:
                </label>
                
                <div class="pix-key-container">
                    <code class="pix-key">12.345.678/0001-99</code>
                </div>
                
                <p class="pix-box-info">
                    O pedido será enviado para a fila de preparo assim que o pagamento for confirmado.
                </p>
            </div>

            <button type="submit" id="btn-confirmar-pagamento" class="btn-login btn-pagamento-submit">
                CONFIRMAR E PAGAR
            </button>
            
        </form>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
<?php
include_once 'views/includes/cabecalho.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
    

         <!-- INFORMAÇÕES SOBRE A CAFETERIA - 1° imagem -->
        <header id="home">
            <h1>Pausa.Café</h1>
            <p>Aberto das 06h às 17h</p>
            <div class="header-address">Área Especial 1, Conjunto A, lote 4, Setor Tradicional, Brazlândia/DF - CEP: 73.334-660</div>
        </header>

        <!-- SOBRE NÓS -->
        <div class="container" id="about">
            <div class="section-title">
                <span>SOBRE NÓS</span>
            </div>
            <p>O Pausa.Café foi fundado em 2026 por amantes do grão. Nosso objetivo é trazer a melhor experiência sensorial em cada xícara.</p>
            <br>
            
            <div class="quote-box">
                <p><i>"O café é a única bebida que, quando quente, aquece a alma e, quando gelada, refresca a mente."</i></p><br>
                <p><strong>Dono: Tadeu Adhemar</strong></p>
            </div>
            <img src="assets/img/equipe.png" alt="Café" class="about-img">
            <br><br><br>
            
               <div class="img-produtores" id="about">
                <p>Nossos Fornecedores:</p>
            </div>
            <img src="assets/img/produtores.png" alt="Café" class="about-img">
            <br><br>
			
			<p class="texto-justificado">
                A cafeteria opta pela produção manual por acreditar em um modelo que une qualidade, sustentabilidade e valorização humana. A colheita seletiva e o manejo mais cuidadoso contribuem para a preservação do meio ambiente, reduzindo impactos ao solo, à água e à vegetação natural. Além disso, esse método favorece a produção de cafés mais puros e aromáticos, ao mesmo tempo em que promove melhores condições de trabalho e respeito às futuras gerações.
            </p>

        </div>

        <!-- MENU -->
        <div class="container" id="menu">
            <div class="section-title">
                <span>MENU</span>
                </div>

                <!-- Botões COMER e BEBER -->
                <div class="menu-tabs">
                <button class="tab-btn active" onclick="toggleMenu(event, 'eat')">Comer</button>
                <button class="tab-btn" onclick="toggleMenu(event, 'drink')">Beber</button>
            </div>

            <div id="eat" class="menu-content active">

                <div class="menu-item"
                    data-produto-id="1"
                    data-produto-nome="Cesta de Pães"
                    data-produto-preco="5.50">

                    <h4>Brownie Tradicional</h4>
                    <p>Denso, com nozes e uma bola de sorvete de creme. R$ 5.50</p>

                    <button
                        class="pausa-carrinho-btn-adicionar" type="button">

                        Adicionar ao carrinho

                    </button>

                    <!-- Feedback visual do item -->
                    <!-- Atualizado dinamicamente pela View -->
                    <span class="pausa-carrinho-feedback"></span>


                </div>

                <div class="menu-item"
                    data-produto-id="2"
                    data-produto-nome="Waffle Belga"
                    data-produto-preco="7.50">

                    <h4>Waffle Belga</h4>
                    <p>Massa com baunilha e farinha maltada R$ 7.50</p>

                    <button
                    class="pausa-carrinho-btn-adicionar" type="button">

                    Adicionar ao carrinho

                    </button>

                    <span class="pausa-carrinho-feedback"></span>

                </div>
            </div>

            <div id="drink" class="menu-content">

                <div class="menu-item"
                    data-produto-id="3"
                    data-produto-nome="Café Regular"
                    data-produto-preco="2.50">

                    <h4>Café Regular</h4>
                    <p>Grão selecionado da região R$ 2.50</p>

                    <button
                        class="pausa-carrinho-btn-adicionar"
                        type="button">

                        Adicionar ao carrinho

                    </button>

                    <span class="pausa-carrinho-feedback"></span>

                </div>

                <div class="menu-item"
                    data-produto-id="4"
                    data-produto-nome="Chocolato"
                    data-produto-preco="4.50">

                    <h4>Chocolato</h4>
                    <p>Espresso com chocolate e leite R$ 4.50</p>

                    <button
                        class="pausa-carrinho-btn-adicionar"
                        type="button">

                        Adicionar ao carrinho

                    </button>

                    <span class="pausa-carrinho-feedback"></span>

                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer>
            <p> &copy 2026 Pausa.Café - Todos os direitos reservados.</p>
        </footer>

        <!-- LINK DO JS -->
        <script src="assets/js/script.js" defer></script>

    </body>
</html>
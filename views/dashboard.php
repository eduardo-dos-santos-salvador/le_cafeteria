<?php
include_once 'views/includes/cabecalho.php';

if (!isset($produtos)) {
    require_once __DIR__ . '/../models/Produtos.php';
    $produtos = Produtos::listarTodos();
}

// DEFINIÇÃO GLOBAL: Garante que a trava seja idêntica para o arquivo inteiro
$usuarioLogado = isset($_SESSION['usuario_id']) || isset($_SESSION['user_id']);
?>

<header id="home">
    <h1>Pausa.Café</h1>
    <p>Aberto das 06h às 17h</p>
    <div class="header-address">Rua das Rosas, Conjunto B, Lote 05, Setor 7, Brazlândia/DF - CEP: 73.334-660</div>
</header>

<div class="container" id="about">
    <div class="section-title">
        <span>SOBRE NÓS</span>
    </div>
    <p>O Pausa.Café foi fundado em 2026 por amantes do grão. Nosso objetivo é trazer a melhor experiência sensorial em cada xícara.</p>
    
    <div class="quote-box">
        <p><i>"O café é a única bebida que, quando quente, aquece a alma e, quando gelada, refresca a mente."</i></p>
        <p><strong>Dono: Tadeu Adhemar</strong></p>
    </div>
    
    <img src="assets/img/sobre/equipe.png" alt="Nossa Equipe" class="about-img">
    
    <div class="img-produtores" id="suppliers">
        <p>Nossos Fornecedores:</p>
    </div>
    
    <img src="assets/img/sobre/produtores.png" alt="Nossos Produtores" class="about-img">
    
    <p class="texto-justificado">
        A cafeteria opta pela produção manual por acreditar em um modelo que une qualidade, sustentabilidade e valorização humana. A colheita seletiva e o manejo mais cuidadoso contribuem para a preservação do meio ambiente...
    </p>
</div>

<div class="container" id="menu">
    <div class="section-title">
        <span>MENU</span>
    </div>

    <div class="menu-tabs">
        <button class="tab-btn active" data-target="eat">Comer</button>
        <button class="tab-btn" data-target="drink">Beber</button>
    </div>

    <div id="eat" class="menu-content active">
        <?php 
        $temComida = false;
        if (!empty($produtos)): 
            foreach ($produtos as $p): 
                $isComidaTag = (strpos($p['desc_produto'], '[TIPO:comida]') !== false);
                $isBebidaTag = (strpos($p['desc_produto'], '[TIPO:bebida]') !== false);
                
                if ($isComidaTag || (!$isBebidaTag && (int)$p['id'] <= 6)):
                    if ((int)$p['ativo'] === 1): 
                        $temComida = true;
                        $descExibicao = trim(preg_replace('/\[TIPO:(comida|bebida)\]/', '', $p['desc_produto'] ?? ''));
        ?>
                    <div class="menu-item"
                         data-produto-id="<?= $p['id'] ?>"
                         data-produto-nome="<?= htmlspecialchars($p['nome']) ?>"
                         data-produto-preco="<?= number_format($p['preco'], 2, '.', '') ?>">

                        <div class="menu-item-texto">
                            <h4><?= htmlspecialchars($p['nome']) ?></h4>
                            <p><?= htmlspecialchars($descExibicao) ?></p>
                            <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        </div>

                        <div class="menu-item-botao">
                            <?php if ($usuarioLogado): ?>
                                <button class="pausa-carrinho-btn-adicionar" type="button">
                                    Adicionar ao carrinho
                                </button>
                            <?php else: ?>
                                <button class="pausa-carrinho-btn-adicionar" type="button" disabled style="background-color: #a0a0a0; cursor: not-allowed;" title="Faça login ou cadastre-se para fazer pedidos">
                                    Faça login para pedir
                                </button>
                            <?php endif; ?>
                            <span class="pausa-carrinho-feedback"></span>
                        </div>

                        <div class="menu-item-foto">
                            <img src="/le_cafeteria/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                        </div>
                    </div>
        <?php 
                    endif;
                endif;
            endforeach; 
        endif; 
        
        if (!$temComida): ?>
            <p style="text-align:center; color:#888; padding: 1rem;">Nenhuma opção de comida disponível no momento.</p>
        <?php endif; ?>
    </div>

    <div id="drink" class="menu-content">
        <?php 
        $temBebida = false;
        if (!empty($produtos)): 
            foreach ($produtos as $p): 
                $isComidaTag = (strpos($p['desc_produto'], '[TIPO:comida]') !== false);
                $isBebidaTag = (strpos($p['desc_produto'], '[TIPO:bebida]') !== false);

                if ($isBebidaTag || (!$isComidaTag && (int)$p['id'] >= 7)):
                    if ((int)$p['ativo'] === 1): 
                        $temBebida = true;
                        $descExibicao = trim(preg_replace('/\[TIPO:(comida|bebida)\]/', '', $p['desc_produto'] ?? ''));
        ?>
                    <div class="menu-item"
                         data-produto-id="<?= $p['id'] ?>"
                         data-produto-nome="<?= htmlspecialchars($p['nome']) ?>"
                         data-produto-preco="<?= number_format($p['preco'], 2, '.', '') ?>">

                        <div class="menu-item-texto">
                            <h4><?= htmlspecialchars($p['nome']) ?></h4>
                            <p><?= htmlspecialchars($descExibicao) ?></p>
                            <p class="preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        </div>

                        <div class="menu-item-botao">
                            <?php if ($usuarioLogado): ?>
                                <button class="pausa-carrinho-btn-adicionar" type="button">
                                    Adicionar ao carrinho
                                </button>
                            <?php else: ?>
                                <button class="pausa-carrinho-btn-adicionar" type="button" disabled style="background-color: #a0a0a0; cursor: not-allowed;" title="Faça login ou cadastre-se para fazer pedidos">
                                    Faça login para pedir
                                </button>
                            <?php endif; ?>
                            <span class="pausa-carrinho-feedback"></span>
                        </div>

                        <div class="menu-item-foto">
                            <img src="/le_cafeteria/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                        </div>
                    </div>
        <?php 
                    endif;
                endif;
            endforeach; 
        endif; 
        
        if (!$temBebida): ?>
            <p style="text-align:center; color:#888; padding: 1rem;">Nenhuma opção de bebida disponível no momento.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p> &copy 2026 Pausa.Café - Todos os direitos reservados.</p>
</footer>

<script src="assets/js/script.js" defer></script>

</body>
</html>
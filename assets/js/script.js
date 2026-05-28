// --- MENU / NAVEGAÇÃO ---
function toggleNav() {
    const nav = document.getElementById("navLinks");
    const btn = document.querySelector(".menu-toggle");
    const isActive = nav.classList.toggle("active");
    btn.setAttribute("aria-expanded", isActive);
}

function toggleMenu(evt, menuName) {
    const contents = document.querySelectorAll(".menu-content");
    const buttons = document.querySelectorAll(".tab-btn");

    contents.forEach(content => content.classList.remove("active"));
    buttons.forEach(btn => btn.classList.remove("active"));

    document.getElementById(menuName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        const nav = document.getElementById("navLinks");
        if (nav) nav.classList.remove("active");
        
        const btn = document.querySelector(".menu-toggle");
        if (btn) btn.setAttribute("aria-expanded", "false");
    });
});


// --- CORE DO CARRINHO (LÓGICA) ---
const CHAVE_CARRINHO = 'pausa_cafe_cart';

// Busca os itens do localStorage de forma segura
function obterCarrinho() {
    const dados = localStorage.getItem(CHAVE_CARRINHO);
    return dados ? JSON.parse(dados) : [];
}

// Salva o carrinho atualizado no localStorage
function salvarCarrinho(carrinho) {
    localStorage.setItem(CHAVE_CARRINHO, JSON.stringify(carrinho));
}

// Atualiza o contador na tela (Centralizado)
function atualizarContadorMenu() {
    const carrinho = obterCarrinho();
    const contador = document.getElementById('cartCounter');
    
    if (contador) {
        const totalItens = carrinho.reduce((acc, item) => acc + item.quantidade, 0);
        contador.innerText = totalItens;
    }
}

// FUNÇÃO INTEGRADA: Renderiza a lista se estiver na página de carrinho
function renderizarCarrinho() {
    const container = document.getElementById('lista-carrinho');
    const totalDisplay = document.getElementById('valor-total');
    const btnPagamento = document.getElementById('btn-pagamento');
    
    atualizarContadorMenu();

    // Segurança: Se não encontrar o container da lista, para a execução aqui (evita erros em outras páginas)
    if (!container) return;

    const carrinho = obterCarrinho();

    // CARRINHO VAZIO
    if (carrinho.length === 0) {
        container.innerHTML = "<p style='text-align:center; padding: 20px;'>Seu carrinho está vazio.</p>";
        
        if (totalDisplay) {
            totalDisplay.innerText = "R$ 0.00";
        }

        if (btnPagamento) {
            btnPagamento.disabled = true;
            btnPagamento.style.opacity = "0.5";
            btnPagamento.style.cursor = "not-allowed";
        }
        return;
    }

    // Habilita botão caso tenha itens
    if (btnPagamento) {
        btnPagamento.disabled = false;
        btnPagamento.style.opacity = "1";
        btnPagamento.style.cursor = "pointer";
    }

    let html = "";
    let somaTotal = 0;

    carrinho.forEach(item => {
        const subtotal = item.preco * item.quantidade;
        somaTotal += subtotal;
        
        html += `
            <div class="menu-item" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding: 10px 0;">
                <div>
                    <h4 style="margin:0;">${item.nome}</h4>
                    <p style="margin:0; font-size: 14px;">Qtd: ${item.quantidade} x R$ ${item.preco.toFixed(2)}</p>
                </div>
                <span style="font-weight: bold;">R$ ${subtotal.toFixed(2)}</span>
            </div>
        `;
    });

    container.innerHTML = html;

    if (totalDisplay) {
        totalDisplay.innerText = `R$ ${somaTotal.toFixed(2)}`;
    }
}

function limparCarrinho() {
    // Remove os itens do armazenamento local do navegador
    localStorage.removeItem(CHAVE_CARRINHO);
    
    // Força o contador visual a zerar imediatamente na tela atual
    const contador = document.getElementById('cartCounter');
    if (contador) contador.innerText = "0";

    // Tenta limpar a lista visual caso o usuário ainda esteja na página do carrinho
    renderizarCarrinho();
    
    // O TRUQUE: Redireciona o usuário de volta para a Home após garantir a limpeza
    window.location.href = "/le_cafeteria/index.php"; 
}


// --- VIEW (INTERAÇÃO VISUAL) ---
const PausaCafeCarrinhoView = {
    mostrarFeedback(item) {
        const feedback = item.querySelector('.pausa-carrinho-feedback');
        if (!feedback) return;

        feedback.textContent = 'Item adicionado ✓';
        setTimeout(() => { feedback.textContent = ''; }, 1500);
    },

    atualizarBotao(botao) {
        const textoOriginal = botao.textContent;
        botao.textContent = 'Adicionado ✓';
        botao.classList.add('pausa-carrinho-btn-success');

        setTimeout(() => {
            botao.textContent = textoOriginal;
            botao.classList.remove('pausa-carrinho-btn-success');
        }, 1500);
    }
};


// --- CONTROLLER (EVENTOS) ---
const PausaCafeCarrinhoController = {
    iniciar() {
        this.registrarEventos();
        atualizarContadorMenu();
    },

    registrarEventos() {
        const botoes = document.querySelectorAll('.pausa-carrinho-btn-adicionar');

        botoes.forEach((botao) => {
            botao.removeAttribute('onclick'); 
            botao.addEventListener('click', (event) => {
                this.adicionarProduto(event);
            });
        });
    },

    adicionarProduto(event) {
        const botao = event.currentTarget;
        const item = botao.closest('.menu-item');
        if (!item) return;

        const produto = {
            id: item.getAttribute('data-produto-id'),
            nome: item.getAttribute('data-produto-nome') || 'Produto',
            preco: parseFloat(item.getAttribute('data-produto-preco')) || 0,
            quantidade: 1
        };

        let carrinho = obterCarrinho();
        const index = carrinho.findIndex(c => c.id === produto.id);

        if (index > -1) {
            carrinho[index].quantidade += 1;
        } else {
            carrinho.push(produto);
        }

        salvarCarrinho(carrinho);
        atualizarContadorMenu();
        
        PausaCafeCarrinhoView.mostrarFeedback(item);
        PausaCafeCarrinhoView.atualizarBotao(botao);
    }
};


// --- INICIALIZAÇÃO DA APLICAÇÃO ---

// Escuta mudanças feitas em outras abas
window.addEventListener('storage', (event) => {
    if (event.key === CHAVE_CARRINHO) {
        atualizarContadorMenu();
        renderizarCarrinho();
    }
});

// Execução ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    // Inicia os eventos de adicionar produtos (caso esteja na página da loja)
    PausaCafeCarrinhoController.iniciar();

    // Segurança para o formulário de pagamento (só age se o formulário existir na página)
    const form = document.querySelector('form[action="tipoPagamento.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const carrinho = obterCarrinho();
            if (carrinho.length === 0) {
                e.preventDefault();
                alert('Seu carrinho está vazio!');
            }
        });
    }

    // Tenta renderizar o carrinho (se não for a página dele, a própria função para sozinha)
    renderizarCarrinho();
});
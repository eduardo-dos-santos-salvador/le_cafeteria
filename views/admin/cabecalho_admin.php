<?php
/**
 * cabecalho_admin.php — Cabeçalho do painel interno (admin e barista)
 * O menu lateral é carregado DINAMICAMENTE do banco via MenuModel,
 * filtrando pelos itens do perfil do usuário logado na sessão.
 */

// Garante que a sessão está ativa antes de qualquer uso
if (session_status() === PHP_SESSION_NONE) session_start();

// Carrega o model de menu dinâmico
require_once __DIR__ . '/../../models/MenuModel.php';

// Descobre o perfil da sessão (admin | barista | cliente)
$perfil_sessao = $_SESSION['usuario_tipo'] ?? 'cliente';

// Busca os itens de menu do banco para esse perfil
$itens_menu = MenuModel::listarPorPerfil($perfil_sessao);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pausa.Café — Painel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata&display=swap" rel="stylesheet">
    <style>
        /* ── Variáveis de cor do tema Pausa.Café ── */
        :root {
            --cafe-escuro:  #3E2723;
            --cafe-medio:   #8D6E63;
            --cafe-claro:   #F5E6CA;
            --bg:           #F4F1EE;
            --branco:       #FFFFFF;
            --erro:         #C62828;
            --sucesso:      #2E7D32;
            --borda:        #DDDDDD;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, sans-serif;
            background: var(--bg);
            color: #333;
        }

        /* ── NAVBAR RESPONSIVA ── */
        .admin-nav {
            background: var(--cafe-escuro);
            color: var(--cafe-claro);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 60px;
            flex-wrap: wrap;
            gap: 0.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .admin-nav .logo {
            font-size: 1.1rem;
            font-weight: bold;
            letter-spacing: 2px;
            white-space: nowrap;
        }

        /* Links do menu — gerados dinamicamente pelo PHP abaixo */
        .admin-nav .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .admin-nav .nav-links a {
            color: var(--cafe-claro);
            text-decoration: none;
            padding: 0.4rem 0.75rem;
            border-radius: 4px;
            font-size: 0.875rem;
            opacity: 0.85;
            transition: background 0.2s, opacity 0.2s;
            white-space: nowrap;
        }

        .admin-nav .nav-links a:hover {
            background: rgba(255,255,255,0.12);
            opacity: 1;
        }

        /* Link "Sair" sempre em destaque vermelho */
        .admin-nav .nav-links a.link-sair {
            color: #ff8a80;
        }

        .admin-nav .usuario {
            font-size: 0.82rem;
            opacity: 0.65;
            white-space: nowrap;
        }

        /* ── RESPONSIVO: empilha em telas pequenas ── */
        @media (max-width: 640px) {
            .admin-nav { flex-direction: column; padding: 0.75rem 1rem; align-items: flex-start; }
            .admin-nav .nav-links { gap: 0.2rem; }
        }

        /* ── CONTAINER PRINCIPAL ── */
        .admin-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* ── ALERTAS DE FEEDBACK ── */
        .alerta {
            padding: 0.85rem 1.25rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
            font-weight: 500;
        }
        .alerta-sucesso { background: #E8F5E9; color: var(--sucesso); border-left: 4px solid var(--sucesso); }
        .alerta-erro    { background: #FFEBEE; color: var(--erro);    border-left: 4px solid var(--erro);    }
    </style>
</head>
<body>

<nav class="admin-nav">
    <!-- Logotipo -->
    <span class="logo">☕ Pausa.Café</span>

    <!-- ======================================================
         MENU DINÂMICO — itens vindos da tabela `menu_itens`
         filtrados pelo perfil do usuário logado
    ====================================================== -->
    <div class="nav-links">
        <?php foreach ($itens_menu as $item): ?>
            <a href="<?= htmlspecialchars($item['url']) ?>"
               <?= str_contains($item['url'], 'logout') ? 'class="link-sair"' : '' ?>>
                <?= htmlspecialchars($item['icone'] . ' ' . $item['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Nome do usuário logado -->
    <span class="usuario">
        Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>
        &nbsp;·&nbsp;
        <em><?= htmlspecialchars($perfil_sessao) ?></em>
    </span>
</nav>

<div class="admin-container">

<?php
/* ── Exibe mensagens de sessão (sucesso/erro) e as limpa ── */
if (!empty($_SESSION['msg_sucesso'])) {
    echo '<div class="alerta alerta-sucesso">' . htmlspecialchars($_SESSION['msg_sucesso']) . '</div>';
    unset($_SESSION['msg_sucesso']);
}
if (!empty($_SESSION['msg_erro'])) {
    echo '<div class="alerta alerta-erro">' . htmlspecialchars($_SESSION['msg_erro']) . '</div>';
    unset($_SESSION['msg_erro']);
}
?>

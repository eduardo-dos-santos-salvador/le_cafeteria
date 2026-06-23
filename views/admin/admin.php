<?php
// views/admin/admin.php
require_once __DIR__ . '/cabecalho_admin.php';
?>

<main class="admin-dashboard">
    <h1>Painel de Controle</h1>
    <div class="dashboard-grid">
        <div class="card">
            <h3>Produtos</h3>
            <a href="/le_cafeteria/index.php?controller=admin&action=produtos" class="btn-action">Gerenciar</a>
        </div>
        <div class="card">
            <h3>Usuários</h3>
            <a href="/le_cafeteria/index.php?controller=admin&action=usuarios" class="btn-action">Gerenciar</a>
        </div>
        <div class="card">
            <h3>Pedidos</h3>
            <a href="/le_cafeteria/index.php?controller=admin&action=pedidos" class="btn-action">Ver Pedidos</a>
        </div>
		<div class="card">
    <h3>Feedbacks</h3>
    <a href="/le_cafeteria/index.php?controller=admin&action=feedbacks" class="btn-action">Ver Feedbacks</a>
</div>
    </div>
</main>
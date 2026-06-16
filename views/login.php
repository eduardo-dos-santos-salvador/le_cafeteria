<?php
/**
 * login.php — Tela de login do Pausa.Café
 * CORREÇÃO: redireciona barista para barista.php (além de admin)
 * CORREÇÃO: path do cabecalho corrigido para __DIR__
 */
session_start();

// Se já logado, redireciona conforme perfil
if (!empty($_SESSION['usuario_tipo'])) {
    $tipo = $_SESSION['usuario_tipo'];
    if ($tipo === 'admin') {
        header('Location: /le_cafeteria/admin.php');
    } elseif ($tipo === 'barista') {
        header('Location: /le_cafeteria/barista.php');
    } else {
        header('Location: /le_cafeteria/index.php');
    }
    exit;
}
?>
<?php include_once __DIR__ . '/includes/cabecalho.php'; ?>

<div class="login-container">

    <div class="section-title">
        <span>LOGIN</span>
    </div>

    <?php if (!empty($_SESSION['erro_login'])): ?>
        <p style="color:#C62828; text-align:center; margin-bottom:1rem; font-weight:600;">
            <?= htmlspecialchars($_SESSION['erro_login']) ?>
        </p>
        <?php unset($_SESSION['erro_login']); ?>
    <?php endif; ?>

    <form action="/le_cafeteria/admin.php?acao=login" method="POST">

        <div class="input-group">
            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                autocomplete="email"
                placeholder="seu@email.com">
        </div>

        <div class="input-group" style="margin-top: 1rem;">
            <label for="senha">Senha</label>
            <input
                type="password"
                id="senha"
                name="senha"
                required
                autocomplete="current-password"
                placeholder="••••••••">
        </div>

        <button type="submit" class="btn-login" style="margin-top: 1.5rem;">
            Entrar
        </button>

    </form>

    <div class="login-footer-links">
        <a href="esqueceuSenha.php" class="forgot-link">Esqueceu sua senha?</a>
    </div>

    <!-- Credenciais de teste (remover antes de produção) -->
    <div style="margin-top:2rem; padding:1rem; background:#F5E6CA; border-radius:8px; font-size:0.82rem; color:#5D4037;">
        <strong>Contas de teste:</strong><br>
        Admin: admin@lecafeteria.com / admin123<br>
        Barista: barista@lecafeteria.com / bar123<br>
        Cliente: cliente@lecafeteria.com / cli123
    </div>

</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
</body>
</html>

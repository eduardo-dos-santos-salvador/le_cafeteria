<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Pausa.Café</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="caixa-login">
        <h2>Acesso ao Sistema</h2>

        <?php if (!empty($erro)): ?>
            <div class ="erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form id="formLogin" method="POST" action ="index.php">
                <label for ="login">Usuário:</label>
                <input type ="text" id="login" name="login">

                <label for="senha">Senha:</label>
                <input type="password"  id="senha" name="senha">

                <input type="submit" value="Entrar">
            </form>
        </div>
        
        <script src="assets/js/script.js"></script>
    </body>
</html>
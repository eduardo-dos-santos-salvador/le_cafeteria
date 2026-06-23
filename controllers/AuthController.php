<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    
public static function cadastro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../views/cadastro.php';
            return;
        }

        // Dados do Usuário
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $cpf      = trim($_POST['cpf'] ?? null);
        $telefone = trim($_POST['telefone'] ?? null);
        $senha    = $_POST['senha'] ?? '';
        $confirma = $_POST['confirma_senha'] ?? '';

        // Dados do Endereço
        $logradouro  = trim($_POST['logradouro'] ?? '');
        $numero      = trim($_POST['numero'] ?? '');
        $complemento = trim($_POST['complemento'] ?? null);
        $bairro      = trim($_POST['bairro'] ?? '');
        $cidade      = trim($_POST['cidade'] ?? '');
        $uf          = 'DF'; // Forçado pelo backend por segurança
        $cep         = trim($_POST['cep'] ?? '');

        if ($senha !== $confirma) {
            $erro = "As senhas não coincidem.";
            require_once __DIR__ . '/../views/cadastro.php';
            return;
        }

        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();

        // Validação de e-mail duplicado
        $sqlCheck = "SELECT id FROM usuario WHERE email = :email LIMIT 1";
        $stmtCheck = $con->prepare($sqlCheck);
        $stmtCheck->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtCheck->execute();
        $usuarioExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($usuarioExistente) {
            $erro = "Este endereço de e-mail já está cadastrado no sistema.";
            require_once __DIR__ . '/../views/cadastro.php';
            return;
        }

        $senhaSegura = password_hash($senha, PASSWORD_DEFAULT);

        try {
            // Inicia uma transação para garantir integridade dos dados nas duas tabelas
            $con->beginTransaction();

            // 1. Insere o Usuário (Incluindo CPF e Telefone informados no formulário)
            $sqlUser = "INSERT INTO usuario (nome, email, senha, cpf, telefone, tipo_user_id) 
                        VALUES (:nome, :email, :senha, :cpf, :telefone, 3)";
            $stmtUser = $con->prepare($sqlUser);
            $stmtUser->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmtUser->bindParam(':email', $email, PDO::PARAM_STR);
            $stmtUser->bindParam(':senha', $senhaSegura, PDO::PARAM_STR);
            $stmtUser->bindParam(':cpf', $cpf, PDO::PARAM_STR);
            $stmtUser->bindParam(':telefone', $telefone, PDO::PARAM_STR);
            $stmtUser->execute();

            // Captura o ID gerado pelo banco para o usuário recém-criado
            $idUsuarioCriado = $con->lastInsertId();

            // 2. Insere o Endereço vinculado ao ID do usuário
            $sqlEnd = "INSERT INTO endereco (usuario_id, logradouro, numero, complemento, bairro, cidade, uf, cep) 
                       VALUES (:usuario_id, :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :cep)";
            $stmtEnd = $con->prepare($sqlEnd);
            $stmtEnd->bindParam(':usuario_id', $idUsuarioCriado, PDO::PARAM_INT);
            $stmtEnd->bindParam(':logradouro', $logradouro, PDO::PARAM_STR);
            $stmtEnd->bindParam(':numero', $numero, PDO::PARAM_STR);
            $stmtEnd->bindParam(':complemento', $complemento, PDO::PARAM_STR);
            $stmtEnd->bindParam(':bairro', $bairro, PDO::PARAM_STR);
            $stmtEnd->bindParam(':cidade', $cidade, PDO::PARAM_STR);
            $stmtEnd->bindParam(':uf', $uf, PDO::PARAM_STR);
            $stmtEnd->bindParam(':cep', $cep, PDO::PARAM_STR);
            $stmtEnd->execute();

            // Confirma as duas operações no banco
            $con->commit();

            // Inicia a sessão e loga o usuário automaticamente
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['usuario_id']   = $idUsuarioCriado;
            $_SESSION['nome']         = $nome;
            $_SESSION['tipo_user_id'] = 3; 

            session_write_close();
            header('Location: /le_cafeteria/index.php');
            exit;

        } catch (Exception $e) {
            // Desfaz qualquer alteração se uma das tabelas falhar
            $con->rollBack();
            $erro = "Erro ao processar cadastro técnico. Detalhes: " . $e->getMessage();
            require_once __DIR__ . '/../views/cadastro.php';
        }
    }

    public static function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario_id'])) {
            self::redirecionarPorPerfil();
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

        $usuario = Usuario::buscarPorEmail($email);

        if ($usuario && Usuario::verificarSenha($senha, $usuario['senha'])) {
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo_user_id'] = (int)$usuario['tipo_user_id'];

            self::redirecionarPorPerfil();
            exit;
        } else {
            $erro = "E-mail ou senha inválidos.";
            require_once __DIR__ . '/../views/login.php';
        }
    }

    private static function redirecionarPorPerfil() {
        $tipo = (int)($_SESSION['tipo_user_id'] ?? 0);

        switch ($tipo) {
            case 1:
                header('Location: /le_cafeteria/index.php?controller=admin');
                break;
            case 2:
                header('Location: /le_cafeteria/index.php?controller=barista');
                break;
            case 3:
                header('Location: /le_cafeteria/index.php?controller=cliente');
                break;
            default:
                header('Location: /le_cafeteria/index.php?controller=home');
                break;
        }
        exit;
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /le_cafeteria/index.php?controller=auth&action=login');
        exit;
    }

    public static function processar_recuperacao() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /le_cafeteria/views/esqueceuSenha.php');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();

        $sql = "SELECT id, nome FROM usuario WHERE email = :email LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $erro = "O e-mail inserido não foi encontrado no nosso banco de dados.";
            require_once __DIR__ . '/../views/esqueceuSenha.php';
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', time() + 3600);

        $sqlUp = "UPDATE usuario SET token_recuperacao = :token, token_expiracao = :expiracao WHERE id = :id";
        $stmtUp = $con->prepare($sqlUp);
        $stmtUp->bindParam(':token', $token, PDO::PARAM_STR);
        $stmtUp->bindParam(':expiracao', $expiracao, PDO::PARAM_STR);
        $stmtUp->bindParam(':id', $usuario['id'], PDO::PARAM_INT);
        $stmtUp->execute();

        $linkRedefinicao = "http://localhost/le_cafeteria/index.php?controller=auth&action=redefinir_tela&token=" . $token;

        require_once __DIR__ . '/../views/includes/cabecalho.php';
        ?>
        <div class="login-container">
            <div class="section-title">
                <span>ENVIO ACEITO</span>
            </div>
            
            <p><strong>Confere o seu e-mail!</strong></p>
            <p>Enviamos as instruções de redefinição de senha para o endereço de e-mail informado.</p>
            
            <br>
            
            <a href="<?= $linkRedefinicao ?>">
                <button type="button" class="btn-login">Abrir link de redefinição</button>
            </a>
        </div>
        </body>
        </html>
        <?php
        exit;
    }

    public static function redefinir_tela() {
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            echo "Token inválido ou não fornecido.";
            return;
        }

        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();

        $sql = "SELECT id FROM usuario WHERE token_recuperacao = :token AND token_expiracao > NOW() LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            require_once __DIR__ . '/../views/includes/cabecalho.php';
            ?>
            <div class="login-container">
                <p>Este link de recuperação está expirado ou é inválido!</p>
                <br>
                <a href="/le_cafeteria/views/esqueceuSenha.php" class="forgot-link">Solicitar novo link</a>
            </div>
            <?php
            return;
        }

        require_once __DIR__ . '/../views/nova_senha.php';
    }

    public static function atualizar_senha() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /le_cafeteria/views/login.php');
            exit;
        }

        $token    = trim($_POST['token'] ?? '');
        $senha    = $_POST['senha'] ?? '';
        $confirma = $_POST['confirmar_senha'] ?? '';

        if ($senha !== $confirma) {
            $erro = "As senhas inseridas não coincidem.";
            require_once __DIR__ . '/../views/nova_senha.php';
            return;
        }

        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();

        $sqlCheck = "SELECT id FROM usuario WHERE token_recuperacao = :token AND token_expiracao > NOW() LIMIT 1";
        $stmtCheck = $con->prepare($sqlCheck);
        $stmtCheck->bindParam(':token', $token, PDO::PARAM_STR);
        $stmtCheck->execute();
        $usuario = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $erro = "Sessão de redefinição expirada. Tente o processo novamente.";
            require_once __DIR__ . '/../views/nova_senha.php';
            return;
        }

        $senhaSegura = password_hash($senha, PASSWORD_DEFAULT);

        $sqlUp = "UPDATE usuario SET senha = :senha, token_recuperacao = NULL, token_expiracao = NULL WHERE id = :id";
        $stmtUp = $con->prepare($sqlUp);
        $stmtUp->bindParam(':senha', $senhaSegura, PDO::PARAM_STR);
        $stmtUp->bindParam(':id', $usuario['id'], PDO::PARAM_INT);
        
        if ($stmtUp->execute()) {
            header('Location: /le_cafeteria/views/login.php?recuperado=1');
            exit;
        }

        $erro = "Erro interno ao salvar sua nova senha. Tente novamente.";
        require_once __DIR__ . '/../views/nova_senha.php';
    }
}
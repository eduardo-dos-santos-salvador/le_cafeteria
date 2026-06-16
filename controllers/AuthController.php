
<?php
/**
 * AuthController.php — Gerencia login, logout e proteção de rotas admin
 * Segurança: session_regenerate_id(), sem exposição de erros ao usuário
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    /**
     * Processa o formulário de login (POST).
     * Redireciona para o painel admin se credenciais corretas,
     * ou volta ao login com mensagem de erro genérica.
     */
    public static function login(): void
    {
        // Garante que a sessão está ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validação básica dos campos
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = 'Preencha e-mail e senha.';
            header('Location: /le_cafeteria/views/login.php');
            exit;
        }

        try {
            $usuario = Usuario::buscarPorEmail($email);

            // Mensagem GENÉRICA — não revela se o e-mail existe ou não (OWASP)
            if (!$usuario || !Usuario::verificarSenha($senha, $usuario['senha'])) {
                $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
                header('Location: /le_cafeteria/views/login.php');
                exit;
            }
			
			$usuarioEncontrado = Usuario::buscarPorEmail($_POST['email']);

// ADICIONE ESTAS DUAS LINHAS PARA TESTE:
echo "<pre>";
var_dump($usuarioEncontrado);
echo "</pre>";
die();

            // Login bem-sucedido: regenera ID de sessão (previne Session Fixation)
            session_regenerate_id(true);

            // Armazena apenas o necessário na sessão
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            // Redireciona conforme o perfil do usuário logado
            if ($usuario["tipo"] === "admin") {
                header('Location: /le_cafeteria/admin.php');
            } elseif ($usuario["tipo"] === "barista") {
                header('Location: /le_cafeteria/barista.php');
            } else {
                header('Location: /le_cafeteria/index.php');
            }
            exit;

        } catch (Exception $e) {
            error_log('[LE_CAFETERIA] Erro no login: ' . $e->getMessage());
            $_SESSION['erro_login'] = 'Erro interno. Tente novamente.';
            header('Location: /le_cafeteria/views/login.php');
            exit;
        }
    }

    /**
     * Encerra a sessão e redireciona para o login.
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: /le_cafeteria/views/login.php');
        exit;
    }

    /**
     * Protege rotas exclusivas de admin.
     * Chame no TOPO de qualquer página do painel admin.
     * Se o usuário não for admin, redireciona para o login.
     */
    public static function exigirAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            empty($_SESSION['usuario_id']) ||
            ($_SESSION['usuario_tipo'] ?? '') !== 'admin'
        ) {
            header('Location: /le_cafeteria/views/login.php');
            exit;
        }
    }
}

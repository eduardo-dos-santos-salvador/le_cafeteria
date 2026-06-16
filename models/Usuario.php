
<?php
/**
 * Usuario.php — Model da tabela `usuario`
 * Responsável por: buscar usuário para login e verificar senha com password_verify()
 */

require_once __DIR__ . '/Conexao.php';

class Usuario
{
    /**
     * Busca um usuário pelo e-mail.
     * Retorna array com os dados ou false se não encontrado.
     *
     * @param string $email
     * @return array|false
     */
    public static function buscarPorEmail(string $email)
    {
        $con  = Conexao::getConexao();
        $sql  = "SELECT u.*, t.desc_tipo AS tipo
                 FROM usuario u
                 JOIN tipo_usuario t ON t.id = u.tipo_user_id
                 WHERE u.email = :email
                 LIMIT 1";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Verifica se a senha digitada bate com o hash do banco.
     *
     * @param string $senhaDigitada  Texto puro vindo do formulário
     * @param string $hashDoBanco    Hash bcrypt armazenado no banco
     * @return bool
     */
    public static function verificarSenha(string $senhaDigitada, string $hashDoBanco): bool
    {
        return password_verify($senhaDigitada, $hashDoBanco);
    }
}

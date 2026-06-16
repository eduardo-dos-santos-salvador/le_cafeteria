<?php
/**
 * Conexao.php — Conexão PDO com o banco MariaDB (Singleton)
 *
 * CREDENCIAIS PADRÃO XAMPP:
 *   host:   localhost
 *   user:   root
 *   senha:  (vazia)
 *   banco:  le_cafeteria
 *
 * Para alterar, edite as constantes abaixo.
 */
class Conexao
{
    private static $instancia;

    // ── Configurações de conexão ───────────────────────────────────
    private const HOST   = 'localhost';
    private const BANCO  = 'le_cafeteria';
    private const USUARIO = 'root';    // XAMPP padrão
    private const SENHA  = '';         // XAMPP padrão (sem senha)

    /**
     * Retorna a única instância PDO (Singleton).
     * Cria a conexão na primeira chamada e reutiliza nas demais.
     */
    public static function getConexao(): PDO
    {
        if (!isset(self::$instancia)) {
            try {
                $dsn = 'mysql:host=' . self::HOST
                     . ';dbname=' . self::BANCO
                     . ';charset=utf8mb4';          // charset corrigido

                self::$instancia = new PDO(
                    $dsn,
                    self::USUARIO,
                    self::SENHA,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                // Em produção: registre o erro em log e exiba msg genérica
                error_log('[LE_CAFETERIA] BD: ' . $e->getMessage());
                die('Não foi possível conectar ao banco de dados. Verifique o XAMPP e as credenciais em models/Conexao.php');
            }
        }

        return self::$instancia;
    }
}

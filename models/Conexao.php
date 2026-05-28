<?php
class Conexao {
    // Variável estática e privada que vai guardar a nossa única conexão
    private static $instancia;

    // Método estático (pode ser chamado sem precisar dar um 'new Conexao()')
    public static function getConexao() {
        
        // PADRÃO SINGLETON: Só cria a conexão se ela ainda NÃO existir.
        // Isso evita que o sistema abra 50 conexões com o banco se 50 usuários entrarem no site, 
        // economizando muita memória do servidor.
        if (!isset(self::$instancia)) {
            
            // Tenta fazer a conexão (Bloco TRY)
            try {
                // String de conexão (DSN): Qual banco?/ onde está? / e qual o nome do banco?
                $dsn = 'mysql:host=localhost;dbname=le_cafeteria;charset=utf8';
                $usuario = 'root';
                $senha = '';

                // Instancia o objeto PDO (A ponte oficial do PHP com o banco)
                self::$instancia = new PDO($dsn, $usuario, $senha);

                // Configura o PDO para ser "fofoqueiro" e nos avisar de qualquer erro SQL
                // Se der erro no SELECT ou INSERT, o PHP vai parar e mostrar o erro exato na tela
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } 
            // Se a tentativa falhar, ele cai aqui no CATCH e captura o erro ($e)
            catch (PDOException $e) {
                // Mata a execução do sistema e exibe a mensagem de erro
                die("Erro crítico de Banco de Dados: " . $e->getMessage());
            }
        }

        // Se a conexão já existia (ou se acabou de ser criada com sucesso), devolve ela pronta para uso
        return self::$instancia;
    }
}

?>

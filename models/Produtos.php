
<?php
/**
 * Produtos.php — Model da tabela `produtos`
 * Padrão: Active Record simplificado | Segurança: Prepared Statements (bindParam)
 *
 * CORREÇÕES APLICADAS (vs versão original):
 * - Adicionados métodos: buscarPorId(), criar(), atualizar(), desativar(), reativar()
 * - Todos os métodos usam prepared statements (prevenção de SQL Injection)
 */

require_once __DIR__ . '/Conexao.php';

class Produtos
{
    // =========================================================================
    // READ — Listar todos os produtos ativos
    // =========================================================================

    /**
     * Retorna todos os produtos com ativo = 1, ordenados por nome.
     */
    public static function listarTodos(): array
    {
        $con  = Conexao::getConexao();
        $sql  = "SELECT * FROM produtos WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retorna TODOS os produtos (inclusive inativos) — exclusivo para o painel admin.
     */
    public static function listarTodosAdmin(): array
    {
        $con  = Conexao::getConexao();
        $sql  = "SELECT * FROM produtos ORDER BY id DESC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // =========================================================================
    // READ — Buscar produto por ID
    // =========================================================================

    /**
     * Retorna um único produto pelo ID.
     * Retorna false se não encontrado.
     *
     * @param int $id
     * @return array|false
     */
    public static function buscarPorId(int $id)
    {
        $con  = Conexao::getConexao();
        $sql  = "SELECT * FROM produtos WHERE id = :id LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // =========================================================================
    // CREATE — Inserir novo produto
    // =========================================================================

    /**
     * Insere um novo produto no banco.
     * Retorna o ID do produto recém-criado.
     *
     * @param string      $nome
     * @param string|null $desc_produto
     * @param float       $preco
     * @param string|null $foto          Caminho relativo da imagem
     * @return int  ID do novo produto
     */
    public static function criar(
        string  $nome,
        ?string $desc_produto,
        float   $preco,
        ?string $foto
    ): int {
        $con = Conexao::getConexao();

        $sql = "INSERT INTO produtos (nome, desc_produto, preco, foto, ativo)
                VALUES (:nome, :desc_produto, :preco, :foto, 1)";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nome',         $nome,         PDO::PARAM_STR);
        $stmt->bindParam(':desc_produto', $desc_produto, PDO::PARAM_STR);
        $stmt->bindParam(':preco',        $preco,        PDO::PARAM_STR); // DECIMAL via STR
        $stmt->bindParam(':foto',         $foto,         PDO::PARAM_STR);
        $stmt->execute();

        return (int) $con->lastInsertId();
    }

    // =========================================================================
    // UPDATE — Atualizar produto existente
    // =========================================================================

    /**
     * Atualiza os dados de um produto existente.
     * Se $foto for null, mantém a foto atual no banco.
     *
     * @param int         $id
     * @param string      $nome
     * @param string|null $desc_produto
     * @param float       $preco
     * @param string|null $novaFoto     Novo caminho da imagem (ou null para manter)
     * @return bool
     */
    public static function atualizar(
        int     $id,
        string  $nome,
        ?string $desc_produto,
        float   $preco,
        ?string $novaFoto
    ): bool {
        $con = Conexao::getConexao();

        // Se não tiver nova foto, mantém a que já está no banco
        if ($novaFoto !== null) {
            $sql = "UPDATE produtos
                    SET nome = :nome,
                        desc_produto = :desc_produto,
                        preco = :preco,
                        foto = :foto
                    WHERE id = :id";
        } else {
            $sql = "UPDATE produtos
                    SET nome = :nome,
                        desc_produto = :desc_produto,
                        preco = :preco
                    WHERE id = :id";
        }

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nome',         $nome,         PDO::PARAM_STR);
        $stmt->bindParam(':desc_produto', $desc_produto, PDO::PARAM_STR);
        $stmt->bindParam(':preco',        $preco,        PDO::PARAM_STR);
        $stmt->bindParam(':id',           $id,           PDO::PARAM_INT);

        if ($novaFoto !== null) {
            $stmt->bindParam(':foto', $novaFoto, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    // =========================================================================
    // DELETE SEGURO — Desativar produto (ativo = 0)
    // =========================================================================

    /**
     * IMPORTANTE: Não usamos DELETE físico pois o produto pode estar
     * vinculado a pedidos antigos (chave estrangeira em itens_pedido).
     * Em vez disso, marcamos como inativo (ativo = 0).
     *
     * @param int $id
     * @return bool
     */
    public static function desativar(int $id): bool
    {
        $con  = Conexao::getConexao();
        $sql  = "UPDATE produtos SET ativo = 0 WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Reativa um produto desativado.
     *
     * @param int $id
     * @return bool
     */
    public static function reativar(int $id): bool
    {
        $con  = Conexao::getConexao();
        $sql  = "UPDATE produtos SET ativo = 1 WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

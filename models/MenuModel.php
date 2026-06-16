<?php
/**
 * MenuModel.php — Busca os itens do menu dinâmico no banco
 * Filtra pelo perfil do usuário logado (admin | barista | cliente)
 */

require_once __DIR__ . '/Conexao.php';

class MenuModel
{
    /**
     * Retorna os itens de menu ativos para o perfil informado,
     * ordenados pelo campo `ordem`.
     *
     * @param  string $perfil  'admin' | 'barista' | 'cliente'
     * @return array
     */
    public static function listarPorPerfil(string $perfil): array
    {
        $con  = Conexao::getConexao();
        $sql  = "SELECT label, url, icone
                 FROM   menu_itens
                 WHERE  perfil = :perfil AND ativo = 1
                 ORDER  BY ordem ASC";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':perfil', $perfil, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

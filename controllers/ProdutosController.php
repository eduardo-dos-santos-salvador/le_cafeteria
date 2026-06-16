
<?php
/**
 * ProdutosController.php — Gerencia todas as ações do CRUD de produtos
 * Ações disponíveis via GET ?acao=: listar | criar | editar | salvar | desativar | reativar
 *
 * Segurança:
 * - Exige sessão admin em todas as rotas
 * - Sanitiza e valida cada campo antes de salvar
 * - Upload de imagem com validação de tipo e tamanho
 * - Sem SQL Injection (usa bindParam no Model)
 */

require_once __DIR__ . '/../models/Produtos.php';
require_once __DIR__ . '/AuthController.php';

class ProdutosController
{
    // Pasta de upload relativa à raiz do projeto
    private const PASTA_UPLOAD  = __DIR__ . '/../assets/img/itens_menu/';
    private const URL_UPLOAD    = 'assets/img/itens_menu/';
    private const MAX_TAMANHO   = 2 * 1024 * 1024; // 2 MB
    private const TIPOS_VALIDOS = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Ponto de entrada: lê o parâmetro ?acao= e despacha para o método correto.
     */
    public static function despachar(): void
    {
        // Toda rota exige admin logado
        AuthController::exigirAdmin();

        $acao = $_GET['acao'] ?? 'listar';

        match ($acao) {
            'listar'    => self::listar(),
            'criar'     => self::formulario(),
            'editar'    => self::formulario(),
            'salvar'    => self::salvar(),
            'desativar' => self::desativar(),
            'reativar'  => self::reativar(),
            default     => self::listar(),
        };
    }

    // =========================================================================
    // READ
    // =========================================================================

    private static function listar(): void
    {
        $produtos = Produtos::listarTodosAdmin();
        require_once __DIR__ . '/../views/admin/produtos_lista.php';
    }

    // =========================================================================
    // CREATE / UPDATE — exibe o formulário
    // =========================================================================

    private static function formulario(): void
    {
        $produto = null;
        $erros   = $_SESSION['erros_form']  ?? [];
        $antigos = $_SESSION['dados_form']  ?? [];
        unset($_SESSION['erros_form'], $_SESSION['dados_form']);

        // Se tiver ID na URL, é edição — busca o produto
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $produto = Produtos::buscarPorId($id);
            if (!$produto) {
                $_SESSION['msg_erro'] = 'Produto não encontrado.';
                header('Location: /le_cafeteria/admin.php?acao=listar');
                exit;
            }
        }

        require_once __DIR__ . '/../views/admin/produto_form.php';
    }

    // =========================================================================
    // CREATE / UPDATE — processa o formulário enviado (POST)
    // =========================================================================

    private static function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /le_cafeteria/admin.php?acao=listar');
            exit;
        }

        // --- Coleta e sanitiza os campos ---
        $id           = filter_input(INPUT_POST, 'id',   FILTER_VALIDATE_INT);
        $nome         = trim(htmlspecialchars($_POST['nome']         ?? '', ENT_QUOTES, 'UTF-8'));
        $desc_produto = trim(htmlspecialchars($_POST['desc_produto'] ?? '', ENT_QUOTES, 'UTF-8'));
        $preco_raw    = str_replace(',', '.', $_POST['preco'] ?? '');
        $preco        = filter_var($preco_raw, FILTER_VALIDATE_FLOAT);

        // --- Validação ---
        $erros = [];

        if (empty($nome)) {
            $erros[] = 'O nome do produto é obrigatório.';
        } elseif (strlen($nome) > 100) {
            $erros[] = 'O nome deve ter no máximo 100 caracteres.';
        }

        if (strlen($desc_produto) > 500) {
            $erros[] = 'A descrição deve ter no máximo 500 caracteres.';
        }

        if ($preco === false || $preco <= 0) {
            $erros[] = 'O preço deve ser um número positivo (ex: 9.90).';
        }

        // --- Upload de imagem (opcional) ---
        $caminhoFoto = null;
        if (!empty($_FILES['foto']['name'])) {
            $resultado = self::processarUpload($_FILES['foto']);
            if (isset($resultado['erro'])) {
                $erros[] = $resultado['erro'];
            } else {
                $caminhoFoto = $resultado['caminho'];
            }
        }

        // Se tiver erros, volta ao formulário com os dados preenchidos
        if (!empty($erros)) {
            $_SESSION['erros_form'] = $erros;
            $_SESSION['dados_form'] = $_POST;
            $rota = $id ? "editar&id={$id}" : 'criar';
            header("Location: /le_cafeteria/admin.php?acao={$rota}");
            exit;
        }

        try {
            if ($id) {
                // UPDATE
                Produtos::atualizar($id, $nome, $desc_produto ?: null, $preco, $caminhoFoto);
                $_SESSION['msg_sucesso'] = 'Produto atualizado com sucesso!';
            } else {
                // INSERT
                Produtos::criar($nome, $desc_produto ?: null, $preco, $caminhoFoto);
                $_SESSION['msg_sucesso'] = 'Produto cadastrado com sucesso!';
            }
        } catch (Exception $e) {
            error_log('[LE_CAFETERIA] Erro ao salvar produto: ' . $e->getMessage());
            $_SESSION['msg_erro'] = 'Erro ao salvar o produto. Tente novamente.';
        }

        header('Location: /le_cafeteria/admin.php?acao=listar');
        exit;
    }

    // =========================================================================
    // DELETE (soft) — desativa o produto
    // =========================================================================

    private static function desativar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['msg_erro'] = 'ID inválido.';
            header('Location: /le_cafeteria/admin.php?acao=listar');
            exit;
        }

        try {
            Produtos::desativar($id);
            $_SESSION['msg_sucesso'] = 'Produto removido do cardápio.';
        } catch (Exception $e) {
            error_log('[LE_CAFETERIA] Erro ao desativar produto: ' . $e->getMessage());
            $_SESSION['msg_erro'] = 'Não foi possível remover o produto.';
        }

        header('Location: /le_cafeteria/admin.php?acao=listar');
        exit;
    }

    private static function reativar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['msg_erro'] = 'ID inválido.';
            header('Location: /le_cafeteria/admin.php?acao=listar');
            exit;
        }

        try {
            Produtos::reativar($id);
            $_SESSION['msg_sucesso'] = 'Produto reativado no cardápio!';
        } catch (Exception $e) {
            error_log('[LE_CAFETERIA] Erro ao reativar produto: ' . $e->getMessage());
            $_SESSION['msg_erro'] = 'Não foi possível reativar o produto.';
        }

        header('Location: /le_cafeteria/admin.php?acao=listar');
        exit;
    }

    // =========================================================================
    // UPLOAD DE IMAGEM — validação de tipo, tamanho e nome sanitizado
    // =========================================================================

    private static function processarUpload(array $arquivo): array
    {
        // Verifica erros do PHP no upload
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return ['erro' => 'Falha no envio da imagem. Tente novamente.'];
        }

        // Valida tamanho (máx 2 MB)
        if ($arquivo['size'] > self::MAX_TAMANHO) {
            return ['erro' => 'A imagem deve ter no máximo 2 MB.'];
        }

        // Valida tipo MIME real (não confia no nome do arquivo)
        $tipoReal = mime_content_type($arquivo['tmp_name']);
        if (!in_array($tipoReal, self::TIPOS_VALIDOS, true)) {
            return ['erro' => 'Formato inválido. Use JPG, PNG ou WebP.'];
        }

        // Define a extensão correta com base no tipo real
        $extensoes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        $ext = $extensoes[$tipoReal];

        // Gera nome único e seguro para o arquivo
        $nomeSeguro = uniqid('produto_', true) . '.' . $ext;

        // Cria a pasta se não existir
        if (!is_dir(self::PASTA_UPLOAD)) {
            mkdir(self::PASTA_UPLOAD, 0755, true);
        }

        $destino = self::PASTA_UPLOAD . $nomeSeguro;

        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            return ['erro' => 'Não foi possível salvar a imagem no servidor.'];
        }

        return ['caminho' => self::URL_UPLOAD . $nomeSeguro];
    }
}

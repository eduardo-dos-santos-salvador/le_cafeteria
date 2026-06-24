<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Produtos.php'; 

class AdminController {
    public static function index() { self::check(); require_once __DIR__ . '/../views/admin/admin.php'; }

    // ====== LISTAGEM GERAL DE USUÁRIOS COM ENDEREÇO UNIFICADO ======
    public static function listarUsuarios() { 
        self::check(); 
        
        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();

        // Traz todos os dados de usuários e seus respectivos endereços (se houver)
        $sql = "SELECT u.*, t.desc_tipo, e.logradouro, e.numero, e.complemento, e.bairro, e.cidade, e.uf, e.cep 
        FROM usuario u 
        JOIN tipo_usuario t ON t.id = u.tipo_user_id 
        LEFT JOIN endereco e ON e.usuario_id = u.id
        ORDER BY u.nome ASC";
        $stmt = $con->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/usuarios_lista.php'; 
    }
    
    // ====== AÇÃO: ADICIONAR NOVO USUÁRIO COMPLETO ======
    public static function adicionarUsuario() {
        self::check();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $tipo_user_id = (int)($_POST['tipo_user_id'] ?? 3); 
            
            $cpf = !empty(trim($_POST['cpf'] ?? '')) ? trim($_POST['cpf']) : null;
            $telefone = !empty(trim($_POST['telefone'] ?? '')) ? trim($_POST['telefone']) : null;

            // Dados de endereço
            $logradouro = trim($_POST['logradouro'] ?? '');
            $numero = trim($_POST['numero'] ?? '');
            $complemento = !empty(trim($_POST['complemento'] ?? '')) ? trim($_POST['complemento']) : null;
            $bairro = trim($_POST['bairro'] ?? '');
            $cidade = trim($_POST['cidade'] ?? '');
            $cep = trim($_POST['cep'] ?? '');

            if (!empty($nome) && !empty($email) && !empty($senha)) {
                require_once __DIR__ . '/../models/Conexao.php';
                $con = Conexao::getInstancia();

                try {
                    $con->beginTransaction();

                    // 1. Insere na tabela usuario
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $sqlUser = "INSERT INTO usuario (nome, email, senha, cpf, telefone, tipo_user_id) 
                                VALUES (:nome, :email, :senha, :cpf, :telefone, :tipo)";
                    $stmtUser = $con->prepare($sqlUser);
                    $stmtUser->bindParam(':nome', $nome, PDO::PARAM_STR);
                    $stmtUser->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmtUser->bindParam(':senha', $senhaHash, PDO::PARAM_STR);
                    $stmtUser->bindParam(':cpf', $cpf, PDO::PARAM_STR);
                    $stmtUser->bindParam(':telefone', $telefone, PDO::PARAM_STR);
                    $stmtUser->bindParam(':tipo', $tipo_user_id, PDO::PARAM_INT);
                    $stmtUser->execute();

                    $idNovoUsuario = $con->lastInsertId();

                    // 2. CORREÇÃO: Coluna mudada para usuario_id
                    $sqlEnd = "INSERT INTO endereco (usuario_id, logradouro, numero, complemento, bairro, cidade, uf, cep) 
                               VALUES (:user_id, :logradouro, :numero, :complemento, :bairro, :cidade, 'DF', :cep)";
                    $stmtEnd = $con->prepare($sqlEnd);
                    $stmtEnd->bindParam(':user_id', $idNovoUsuario, PDO::PARAM_INT);
                    $stmtEnd->bindParam(':logradouro', $logradouro, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':numero', $numero, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':complemento', $complemento, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':bairro', $bairro, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':cidade', $cidade, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':cep', $cep, PDO::PARAM_STR);
                    $stmtEnd->execute();

                    $con->commit();
                    header('Location: /le_cafeteria/index.php?controller=admin&action=usuarios&sucesso=adicionado');
                    exit;

                } catch (Exception $e) {
                    $con->rollBack();
                    die("Erro ao salvar usuário: " . $e->getMessage());
                }
            }
        }
        header('Location: /le_cafeteria/index.php?controller=admin&action=usuarios');
        exit;
    }

    // ====== AÇÃO: EDITAR USUÁRIO EXISTENTE ======
    public static function editarUsuario() {
        self::check();
        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();

        // ── CENÁRIO A: PROCESSAR O SALVAMENTO DO FORMULÁRIO (POST) ──
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $tipo_user_id = (int)($_POST['tipo_user_id'] ?? 3);
            $cpf = !empty(trim($_POST['cpf'] ?? '')) ? trim($_POST['cpf']) : null;
            $telefone = !empty(trim($_POST['telefone'] ?? '')) ? trim($_POST['telefone']) : null;

            // Endereço
            $logradouro = trim($_POST['logradouro'] ?? '');
            $numero = trim($_POST['numero'] ?? '');
            $complemento = !empty(trim($_POST['complemento'] ?? '')) ? trim($_POST['complemento']) : null;
            $bairro = trim($_POST['bairro'] ?? '');
            $cidade = trim($_POST['cidade'] ?? '');
            $cep = trim($_POST['cep'] ?? '');

            if ($id > 0 && !empty($nome) && !empty($email)) {
                try {
                    $con->beginTransaction();

                    // 1. Atualização dos Dados Gerais do Usuário
                    if (!empty($_POST['senha'])) {
                        $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                        $sql = "UPDATE usuario SET nome = :nome, email = :email, senha = :senha, cpf = :cpf, telefone = :telefone, tipo_user_id = :tipo WHERE id = :id";
                        $stmt = $con->prepare($sql);
                        $stmt->bindParam(':senha', $senhaHash, PDO::PARAM_STR);
                    } else {
                        $sql = "UPDATE usuario SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, tipo_user_id = :tipo WHERE id = :id";
                        $stmt = $con->prepare($sql);
                    }
                    
                    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR);
                    $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);
                    $stmt->bindParam(':tipo', $tipo_user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    // 2. CORREÇÃO: Verifica se o endereço existe usando 'usuario_id' na cláusula WHERE
                    $stmtCheck = $con->prepare("SELECT id FROM endereco WHERE usuario_id = :uid LIMIT 1");
                    $stmtCheck->execute([':uid' => $id]);

                    if ($stmtCheck->fetch()) {
                        // CORREÇÃO: Mudado WHERE de 'user_id' para 'usuario_id'
                        $sqlEnd = "UPDATE endereco SET logradouro = :logradouro, numero = :numero, complemento = :complemento, bairro = :bairro, city = :cidade, cep = :cep WHERE usuario_id = :user_id";
                        
                        // NOTA IMPORTANTE: Verifique se no banco a coluna é 'cidade' ou 'city'. 
                        // Se o erro mudar de nome para 'city', altere a linha acima de 'city = :cidade' para 'cidade = :cidade'
                        $sqlEnd = "UPDATE endereco SET logradouro = :logradouro, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, cep = :cep WHERE usuario_id = :user_id";
                    } else {
                        // CORREÇÃO: Mudado o campo de inserção para usuario_id
                        $sqlEnd = "INSERT INTO endereco (usuario_id, logradouro, numero, complemento, bairro, cidade, uf, cep) VALUES (:user_id, :logradouro, :numero, :complemento, :bairro, :cidade, 'DF', :cep)";
                    }

                    $stmtEnd = $con->prepare($sqlEnd);
                    $stmtEnd->bindParam(':logradouro', $logradouro, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':numero', $numero, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':complemento', $complemento, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':bairro', $bairro, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':cidade', $cidade, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':cep', $cep, PDO::PARAM_STR);
                    $stmtEnd->bindParam(':user_id', $id, PDO::PARAM_INT);
                    $stmtEnd->execute();

                    $con->commit();
                    header('Location: /le_cafeteria/index.php?controller=admin&action=usuarios&sucesso=editado');
                    exit;

                } catch (Exception $e) {
                    $con->rollBack();
                    die("Erro ao atualizar dados: " . $e->getMessage());
                }
            }
        }

        // ── CENÁRIO B: CLIQUE NO BOTÃO EDITAR (GET) ──
        $sql = "SELECT u.*, t.desc_tipo, e.logradouro, e.numero, e.complemento, e.bairro, e.cidade, e.uf, e.cep 
                FROM usuario u 
                JOIN tipo_usuario t ON t.id = u.tipo_user_id 
                LEFT JOIN endereco e ON e.usuario_id = u.id
                ORDER BY u.nome ASC";
        $stmt = $con->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/usuarios_lista.php';
    }

    // ====== AÇÃO: EXCLUIR USUÁRIO ======
    public static function excluirUsuario() {
        self::check();
        
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            require_once __DIR__ . '/../models/Conexao.php';
            $con = Conexao::getInstancia();
            
            $sql = "DELETE FROM usuario WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            header('Location: /le_cafeteria/index.php?controller=admin&action=usuarios&sucesso=excluido');
            exit;
        }
        header('Location: /le_cafeteria/index.php?controller=admin&action=usuarios');
        exit;
    }

public static function produtos() {
        self::check();
        // ALTERADO: Mudou de listarTodos() para listarTodosAdmin()
        $produtos = Produtos::listarTodosAdmin(); 
        require_once __DIR__ . '/../views/admin/produtos_lista.php';
    }

    public static function pedidos() {
        self::check();
        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();
        
        $sql = "SELECT p.*, u.nome AS cliente 
                FROM pedido p 
                JOIN usuario u ON u.id = p.usuario_id 
                ORDER BY p.criado_em DESC";
                
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/admin/pedidos_lista.php';
    }

    public static function criar() {
        self::check();
        $produto = ['id' => '', 'nome' => '', 'desc_produto' => '', 'preco' => '', 'foto' => '', 'ativo' => 1];
        $modo = 'criar';
        require_once __DIR__ . '/../views/admin/produto_form.php';
    }

    public static function editar() {
        self::check();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) { header('Location: /le_cafeteria/index.php?controller=admin&action=produtos'); exit; }

        $produto = Produtos::buscarPorId($id); 
        $modo = 'editar';
        require_once __DIR__ . '/../views/admin/produto_form.php';
    }

public static function salvar() {
        self::check();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nome = $_POST['nome'] ?? '';
            $desc = $_POST['desc_produto'] ?? '';
            
            // CAPTURA O STATUS ATIVO DO FORMULÁRIO (Padrão 1 se não vier nada)
            $ativo = isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1;

            $categoria = $_POST['categoria'] ?? 'bebida';
            $desc = trim(preg_replace('/\[TIPO:(comida|bebida)\]/', '', $desc));
            $desc = $desc . " [TIPO:" . $categoria . "]";

            $preco_raw = str_replace(',', '.', $_POST['preco'] ?? '0');
            $preco = (float) $preco_raw;
            $fotoPath = $_POST['foto_atual'] ?? '';
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $novoNome = uniqid('prod_') . '.' . $extensao;
                $diretorioDestino = __DIR__ . '/../assets/img/itens_menu/';
                
                if (!is_dir($diretorioDestino)) mkdir($diretorioDestino, 0755, true);
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorioDestino . $novoNome)) {
                    $fotoPath = 'assets/img/itens_menu/' . $novoNome;
                }
            }

            // ATUALIZADO: Passando a variável $ativo para a Model salvar no banco de dados
            if (empty($id)) {
                Produtos::criar($nome, $desc, $preco, $fotoPath, $ativo);
            } else {
                Produtos::atualizar((int)$id, $nome, $desc, $preco, $fotoPath, $ativo);
            }
            header('Location: /le_cafeteria/index.php?controller=admin&action=produtos');
            exit;
        }
    }
	
// ====== MÉTODO: VISUALIZAR FEEDBACKS DOS CLIENTES ======
    public static function feedbacks() {
        self::check();
        
        require_once __DIR__ . '/../models/Conexao.php';
        $con = Conexao::getInstancia();
        
        $sql = "SELECT f.*, u.nome AS nome_usuario 
                FROM feedback f 
                LEFT JOIN usuario u ON u.id = f.usuario_id 
                ORDER BY f.criado_em DESC";
                
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 🛠️ Alinhado perfeitamente com o nome no plural que você colocou na pasta!
        $caminhoView = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'feedbacks_lista.php';
        
        require_once $caminhoView;
    }

    public static function desativar() {
        self::check();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) { Produtos::desativar($id); }
        header('Location: /le_cafeteria/index.php?controller=admin&action=produtos');
        exit;
    }

    public static function reativar() {
        self::check();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) { Produtos::reativar($id); }
        header('Location: /le_cafeteria/index.php?controller=admin&action=produtos');
        exit;
    }
	
	public static function excluir() {
        self::check();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if ($id) { 
            require_once __DIR__ . '/../models/Conexao.php';
            $con = Conexao::getInstancia();
            
            // Deleta o produto fisicamente do banco de dados
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute(); 
        }
        
        // Redireciona de volta para a lista com action no padrão correto
        header('Location: /le_cafeteria/index.php?controller=admin&action=produtos');
        exit;
    }

    private static function check() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['tipo_user_id']) || $_SESSION['tipo_user_id'] != 1) {
            header('Location: /le_cafeteria/index.php?controller=auth&action=login');
            exit;
        }
    }
}
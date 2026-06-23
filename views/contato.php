<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'includes/cabecalho.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../models/Conexao.php';
    $con = Conexao::getInstancia();

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    
    $usuario_id = $_SESSION['user_id'] ?? null; 

    $comentarioSalvar = $nome . '[SPLIT]' . $email . '[SPLIT]' . $mensagem;

    try {
        $sql = "INSERT INTO feedback (usuario_id, pedido_id, nota, comentario, criado_em) 
                VALUES (:usuario_id, NULL, 5, :comentario, CURRENT_TIMESTAMP)";
        
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuario_id, $usuario_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':comentario', $comentarioSalvar, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            echo "<script>alert('Feedback enviado com sucesso! Obrigado.'); window.location.href='contato.php';</script>";
            exit;
        }
    } catch (Exception $e) {
        echo "<script>alert('Erro ao enviar feedback: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<div class="login-container">
    <div class="section-title">
        <span>CONTATO & FEEDBACK</span>
    </div>

    <form method="POST" action="">
        <div class="input-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>
        </div>
        
        <div class="input-group">
            <label for="email">E-Mail</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
        </div>
        
        <div class="input-group">
            <label for="mensagem">Seu feedback</label>
            <textarea id="mensagem" name="mensagem" placeholder="Digite seu feedback" required></textarea>
        </div>
        
        <button type="submit" class="btn-login">Enviar Feedback</button>
    </form>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
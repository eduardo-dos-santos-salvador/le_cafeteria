<?php
/**
 * usuarios_lista.php — Listagem de usuários para o admin
 * Permite ativar / desativar (campo `ativo` 0 ou 1) via GET
 */
require_once __DIR__ . '/cabecalho_admin.php';

// Ação de toggle ativo/inativo
if (isset($_GET['toggle_id'])) {
    $tid  = filter_var($_GET['toggle_id'], FILTER_VALIDATE_INT);
    if ($tid) {
        require_once __DIR__ . '/../../models/Conexao.php';
        $con  = Conexao::getConexao();
        $stmt = $con->prepare(
            "UPDATE usuario SET ativo = IF(ativo=1, 0, 1) WHERE id = :id"
        );
        $stmt->bindParam(':id', $tid, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['msg_sucesso'] = 'Status do usuário atualizado.';
        header('Location: /le_cafeteria/admin.php?acao=usuarios');
        exit;
    }
}
?>
<style>
    .tabela-wrapper { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow-x:auto; }
    table { width:100%; border-collapse:collapse; min-width:560px; }
    thead { background:var(--cafe-escuro); color:var(--cafe-claro); }
    thead th { padding:.85rem 1rem; text-align:left; font-size:.82rem; letter-spacing:1px; }
    tbody tr { border-bottom:1px solid var(--borda); }
    tbody tr:hover { background:#FAF7F4; }
    tbody td { padding:.72rem 1rem; vertical-align:middle; font-size:.9rem; }
    .badge { display:inline-block; padding:.22rem .6rem; border-radius:999px; font-size:.75rem; font-weight:700; }
    .badge-ativo   { background:#E8F5E9; color:var(--sucesso); }
    .badge-inativo { background:#FFEBEE; color:var(--erro); }
    .btn-sm { padding:.32rem .7rem; font-size:.8rem; border-radius:6px; text-decoration:none; font-weight:600; }
    .btn-toggle-off { background:#C62828; color:#fff; }
    .btn-toggle-on  { background:#2E7D32; color:#fff; }
</style>

<h1 style="font-size:1.5rem; color:var(--cafe-escuro); margin-bottom:1.5rem;">👥 Usuários</h1>

<div class="tabela-wrapper">
    <table>
        <thead>
            <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Ação</th></tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td>#<?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['tipo']) ?></td>
                <td>
                    <?php if ($u['ativo']): ?>
                        <span class="badge badge-ativo">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-inativo">Inativo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($u['tipo'] !== 'admin'): // não permite desativar admin próprio ?>
                        <a href="/le_cafeteria/admin.php?acao=usuarios&toggle_id=<?= $u['id'] ?>"
                           class="btn-sm <?= $u['ativo'] ? 'btn-toggle-off' : 'btn-toggle-on' ?>"
                           onclick="return confirm('Alterar status de <?= htmlspecialchars(addslashes($u['nome'])) ?>?')">
                            <?= $u['ativo'] ? '🔴 Desativar' : '🟢 Ativar' ?>
                        </a>
                    <?php else: ?>
                        <em style="font-size:.8rem; color:#999;">protegido</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div></body></html>

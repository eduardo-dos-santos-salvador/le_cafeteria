<?php
// 📊 CORREÇÃO DO CAMINHO: Volta 1 nível (sai de admin e vai para views) para achar a pasta includes geral
include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'cabecalho_admin.php'; ?>

<div class="fb-container">
    <div class="fb-header">
        <h2 class="fb-title">Gerenciamento de Feedbacks</h2>
    </div>

    <div class="tabela-wrapper">
        <?php if (empty($feedbacks)): ?>
            <p class="fb-vazio">Nenhum feedback recebido até o momento.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Mensagem / Feedback</th>
                        <th>Data / Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedbacks as $fb): 
                        $textoBanco = $fb['comentario'];

                        // 🔍 REPOSITÓRIO INTELIGENTE: Captura e separa strings no formato antigo e no novo
                        if (strpos($textoBanco, '[SPLIT]') !== false) {
                            $dados = explode('[SPLIT]', $textoBanco);
                            $nomeExibir = $dados[0] ?? 'Visitante';
                            $emailExibir = $dados[1] ?? 'Não informado';
                            $mensagemExibir = $dados[2] ?? '';
                        } elseif (strpos($textoBanco, 'Nome:') !== false) {
                            $partes = explode('|', $textoBanco);
                            $nomeExibir = isset($partes[0]) ? trim(str_replace('Nome:', '', $partes[0])) : 'Visitante';
                            
                            if (isset($partes[1])) {
                                $subPartes = explode('Feedback:', $partes[1]);
                                $emailExibir = isset($subPartes[0]) ? trim(str_replace('E-mail:', '', $subPartes[0])) : 'Não informado';
                                $mensagemExibir = isset($subPartes[1]) ? trim($subPartes[1]) : $partes[1];
                            } else {
                                $emailExibir = 'Não informado';
                                $mensagemExibir = $textoBanco;
                            }
                        } else {
                            $nomeExibir = $fb['nome_usuario'] ? $fb['nome_usuario'] : 'Visitante';
                            $emailExibir = $fb['nome_usuario'] ? 'Cadastrado no sistema' : 'Não informado';
                            $mensagemExibir = $textoBanco;
                        }

                        $perfilExibir = $fb['usuario_id'] ? 'cliente' : 'visitante';
                    ?>
                        <tr>
                            <td class="fb-td-nome"><?= htmlspecialchars($nomeExibir) ?></td>
                            <td class="fb-td-email"><?= htmlspecialchars($emailExibir) ?></td>
                            <td class="fb-td-perfil">
                                <span class="fb-badge-perfil">
                                    <?= $perfilExibir ?>
                                </span>
                            </td>
                            <td class="fb-td-mensagem"><?= htmlspecialchars($mensagemExibir) ?></td>
                            <td class="fb-td-data">
                                <?= date('d/m/Y H:i', strtotime($fb['criado_em'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
</body>
</html>
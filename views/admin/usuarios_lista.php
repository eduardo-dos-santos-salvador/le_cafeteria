<?php require_once __DIR__ . '/cabecalho_admin.php'; ?>

<main class="admin-dashboard">
    <h2>Gerenciamento de Usuários</h2>

    <?php
    $modo = $_GET['action'] ?? $_GET['acao'] ?? '';
    $idEditar = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    $usuarioParaEditar = null;
    if ($modo === 'editar_usuario' && $idEditar > 0) {
        foreach ($usuarios as $u) {
            if ((int)$u['id'] === $idEditar) {
                $usuarioParaEditar = $u;
                break;
            }
        }
    }
    ?>

    <?php if (isset($_GET['modo']) && $_GET['modo'] === 'novo'): ?>
        <div class="form-container">
            <h3>Adicionar Novo Usuário</h3>
            <form action="/le_cafeteria/index.php?controller=admin&action=adicionar_usuario" method="POST">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" required placeholder="Ex: João Silva">
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" required placeholder="exemplo@pausacafe.com">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>CPF</label>
                        <input type="text" name="cpf" required placeholder="***.***.***-**">
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" required placeholder="(__) _____-____">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-flex-3">
                        <label>Logradouro (Rua, Avenida, Quadra)</label>
                        <input type="text" name="logradouro" required placeholder="Ex: Av. Central, Comercial Norte">
                    </div>
                    <div class="form-group form-group-flex-1">
                        <label>Número</label>
                        <input type="text" name="numero" required placeholder="Ex: 12">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="complemento" placeholder="Ex: Apto 302, Bloco B (Opcional)">
                    </div>
                    <div class="form-group">
                        <label>Bairro / Setor</label>
                        <input type="text" name="bairro" required placeholder="Ex: Taguatinga Centro">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-flex-2">
                        <label>Cidade (Região Administrativa — DF)</label>
                        <select name="cidade" required>
                            <option value="" disabled selected>Selecione a RA</option>
                            <option value="Águas Claras">Águas Claras</option>
                            <option value="Arniqueira">Arniqueira</option>
                            <option value="Brasília (Plano Piloto)">Brasília (Plano Piloto)</option>
                            <option value="Brazlândia">Brazlândia</option>
                            <option value="Candangolândia">Candangolândia</option>
                            <option value="Ceilândia">Ceilândia</option>
                            <option value="Cruzeiro">Cruzeiro</option>
                            <option value="Fercal">Fercal</option>
                            <option value="Gama">Gama</option>
                            <option value="Guará">Guará</option>
                            <option value="Itapoã">Itapoã</option>
                            <option value="Jardim Botânico">Jardim Botânico</option>
                            <option value="Lago Norte">Lago Norte</option>
                            <option value="Lago Sul">Lago Sul</option>
                            <option value="Núcleo Bandeirante">Núcleo Bandeirante</option>
                            <option value="Paranoá">Paranoá</option>
                            <option value="Park Way">Park Way</option>
                            <option value="Planaltina">Planaltina</option>
                            <option value="Recanto das Emas">Recanto das Emas</option>
                            <option value="Riacho Fundo">Riacho Fundo</option>
                            <option value="Riacho Fundo II">Riacho Fundo II</option>
                            <option value="Samambaia">Samambaia</option>
                            <option value="Santa Maria">Santa Maria</option>
                            <option value="São Sebastião">São Sebastião</option>
                            <option value="SCIA (Cidade Estrutural)">SCIA (Cidade Estrutural)</option>
                            <option value="SIA">SIA</option>
                            <option value="Sobradinho">Sobradinho</option>
                            <option value="Sobradinho II">Sobradinho II</option>
                            <option value="Sudoeste/Octogonal">Sudoeste/Octogonal</option>
                            <option value="Taguatinga">Taguatinga</option>
                            <option value="Varjão">Varjão</option>
                            <option value="Vicente Pires">Vicente Pires</option>
                        </select>
                    </div>
                    <div class="form-group form-group-flex-05">
                        <label>UF</label>
                        <input type="text" name="uf" value="DF" readonly class="input-readonly-df">
                    </div>
                    <div class="form-group form-group-flex-15">
                        <label>CEP</label>
                        <input type="text" name="cep" required placeholder="70000-000">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Senha de Acesso</label>
                        <input type="password" name="senha" required placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="form-group">
                        <label>Cargo / Perfil</label>
                        <select name="tipo_user_id" required>
                            <option value="2">Barista</option>
                            <option value="1">Administrador</option>
                            <option value="3">Cliente</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save">Salvar Usuário</button>
                <a href="/le_cafeteria/index.php?controller=admin&action=usuarios" class="btn-cancel">Cancelar</a>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($usuarioParaEditar): ?>
        <div class="form-container form-container-editar">
            <h3>Editar Dados do Usuário: <?= htmlspecialchars($usuarioParaEditar['nome']) ?></h3>
            <form action="/le_cafeteria/index.php?controller=admin&action=editar_usuario" method="POST">
                <input type="hidden" name="id" value="<?= $usuarioParaEditar['id'] ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($usuarioParaEditar['nome']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($usuarioParaEditar['email']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>CPF</label>
                        <input type="text" name="cpf" value="<?= htmlspecialchars($usuarioParaEditar['cpf'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" value="<?= htmlspecialchars($usuarioParaEditar['telefone'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-flex-3">
                        <label>Logradouro (Rua, Avenida)</label>
                        <input type="text" name="logradouro" value="<?= htmlspecialchars($usuarioParaEditar['logradouro'] ?? '') ?>" required>
                    </div>
                    <div class="form-group form-group-flex-1">
                        <label>Número</label>
                        <input type="text" name="numero" value="<?= htmlspecialchars($usuarioParaEditar['numero'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="complemento" value="<?= htmlspecialchars($usuarioParaEditar['complemento'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Bairro / Setor</label>
                        <input type="text" name="bairro" value="<?= htmlspecialchars($usuarioParaEditar['bairro'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-flex-2">
                        <label>Cidade (Região Administrativa — DF)</label>
                        <select name="cidade" required>
                            <?php $cidSalva = $usuarioParaEditar['cidade'] ?? ''; ?>
                            <option value="Águas Claras" <?= $cidSalva === 'Águas Claras' ? 'selected' : '' ?>>Águas Claras</option>
                            <option value="Arniqueira" <?= $cidSalva === 'Arniqueira' ? 'selected' : '' ?>>Arniqueira</option>
                            <option value="Brasília (Plano Piloto)" <?= $cidSalva === 'Brasília (Plano Piloto)' ? 'selected' : '' ?>>Brasília (Plano Piloto)</option>
                            <option value="Brazlândia" <?= $cidSalva === 'Brazlândia' ? 'selected' : '' ?>>Brazlândia</option>
                            <option value="Candangolândia" <?= $cidSalva === 'Candangolândia' ? 'selected' : '' ?>>Candangolândia</option>
                            <option value="Ceilândia" <?= $cidSalva === 'Ceilândia' ? 'selected' : '' ?>>Ceilândia</option>
                            <option value="Cruzeiro" <?= $cidSalva === 'Cruzeiro' ? 'selected' : '' ?>>Cruzeiro</option>
                            <option value="Fercal" <?= $cidSalva === 'Fercal' ? 'selected' : '' ?>>Fercal</option>
                            <option value="Gama" <?= $cidSalva === 'Gama' ? 'selected' : '' ?>>Gama</option>
                            <option value="Guará" <?= $cidSalva === 'Guará' ? 'selected' : '' ?>>Guará</option>
                            <option value="Itapoã" <?= $cidSalva === 'Itapoã' ? 'selected' : '' ?>>Itapoã</option>
                            <option value="Jardim Botânico" <?= $cidSalva === 'Jardim Botânico' ? 'selected' : '' ?>>Jardim Botânico</option>
                            <option value="Lago Norte" <?= $cidSalva === 'Lago Norte' ? 'selected' : '' ?>>Lago Norte</option>
                            <option value="Lago Sul" <?= $cidSalva === 'Lago Sul' ? 'selected' : '' ?>>Lago Sul</option>
                            <option value="Núcleo Bandeirante" <?= $cidSalva === 'Núcleo Bandeirante' ? 'selected' : '' ?>>Núcleo Bandeirante</option>
                            <option value="Paranoá" <?= $cidSalva === 'Paranoá' ? 'selected' : '' ?>>Paranoá</option>
                            <option value="Park Way" <?= $cidSalva === 'Park Way' ? 'selected' : '' ?>>Park Way</option>
                            <option value="Planaltina" <?= $cidSalva === 'Planaltina' ? 'selected' : '' ?>>Planaltina</option>
                            <option value="Recanto das Emas" <?= $cidSalva === 'Recanto das Emas' ? 'selected' : '' ?>>Recanto das Emas</option>
                            <option value="Riacho Fundo" <?= $cidSalva === 'Riacho Fundo' ? 'selected' : '' ?>>Riacho Fundo</option>
                            <option value="Riacho Fundo II" <?= $cidSalva === 'Riacho Fundo II' ? 'selected' : '' ?>>Riacho Fundo II</option>
                            <option value="Samambaia" <?= $cidSalva === 'Samambaia' ? 'selected' : '' ?>>Samambaia</option>
                            <option value="Santa Maria" <?= $cidSalva === 'Santa Maria' ? 'selected' : '' ?>>Santa Maria</option>
                            <option value="São Sebastião" <?= $cidSalva === 'São Sebastião' ? 'selected' : '' ?>>São Sebastião</option>
                            <option value="SCIA (Cidade Estrutural)" <?= $cidSalva === 'SCIA (Cidade Estrutural)' ? 'selected' : '' ?>>SCIA (Cidade Estrutural)</option>
                            <option value="SIA" <?= $cidSalva === 'SIA' ? 'selected' : '' ?>>SIA</option>
                            <option value="Sobradinho" <?= $cidSalva === 'Sobradinho' ? 'selected' : '' ?>>Sobradinho</option>
                            <option value="Sobradinho II" <?= $cidSalva === 'Sobradinho II' ? 'selected' : '' ?>>Sobradinho II</option>
                            <option value="Sudoeste/Octogonal" <?= $cidSalva === 'Sudoeste/Octogonal' ? 'selected' : '' ?>>Sudoeste/Octogonal</option>
                            <option value="Taguatinga" <?= $cidSalva === 'Taguatinga' ? 'selected' : '' ?>>Taguatinga</option>
                            <option value="Varjão" <?= $cidSalva === 'Varjão' ? 'selected' : '' ?>>Varjão</option>
                            <option value="Vicente Pires" <?= $cidSalva === 'Vicente Pires' ? 'selected' : '' ?>>Vicente Pires</option>
                        </select>
                    </div>
                    <div class="form-group form-group-flex-05">
                        <label>UF</label>
                        <input type="text" name="uf" value="DF" readonly class="input-readonly-df">
                    </div>
                    <div class="form-group form-group-flex-15">
                        <label>CEP</label>
                        <input type="text" name="cep" value="<?= htmlspecialchars($usuarioParaEditar['cep'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nova Senha (Deixe em branco para manter a atual)</label>
                        <input type="password" name="senha" placeholder="Preencha apenas se quiser alterar">
                    </div>
                    <div class="form-group">
                        <label>Cargo / Perfil</label>
                        <select name="tipo_user_id" required>
                            <option value="2" <?= $usuarioParaEditar['tipo_user_id'] == 2 ? 'selected' : '' ?>>Barista</option>
                            <option value="1" <?= $usuarioParaEditar['tipo_user_id'] == 1 ? 'selected' : '' ?>>Administrador</option>
                            <option value="3" <?= $usuarioParaEditar['tipo_user_id'] == 3 ? 'selected' : '' ?>>Cliente</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save btn-save-editar">Ajustar Dados</button>
                <a href="/le_cafeteria/index.php?controller=admin&action=usuarios" class="btn-cancel">Cancelar</a>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!isset($_GET['modo']) && !$usuarioParaEditar): ?>
        
        <a href="/le_cafeteria/index.php?controller=admin&action=usuarios&modo=novo" class="btn-add">+ Novo Usuário</a>
        
        <div class="tabela-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nome']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['desc_tipo']) ?></td>
                        <td>
                            <a href="/le_cafeteria/index.php?controller=admin&action=editar_usuario&id=<?= $user['id'] ?>" class="btn-edit">Editar</a>
                            <a href="/le_cafeteria/index.php?controller=admin&action=excluir_usuario&id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    <?php endif; ?>
</main>
</body>
</html>
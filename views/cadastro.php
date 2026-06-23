<?php
include_once 'includes/cabecalho.php';
?>

<div class="login-container">

    <div class="section-title">
        <span>CADASTRO</span>
    </div>

    <?php if (isset($erro) && !empty($erro)): ?>
        <div class="alert-message">
            ⚠️ <strong>Aviso:</strong> <?= htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <form action="/le_cafeteria/index.php?controller=auth&action=cadastro" method="POST">
        
        <div class="form-row">
            <div class="input-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required placeholder="Seu nome completo">
            </div>

            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" required placeholder="***.***.***-**">
            </div>

            <div class="input-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" required placeholder="(__) _____-____">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="logradouro">Logradouro (Rua, Avenida, Quadra)</label>
                <input type="text" id="logradouro" name="logradouro" required placeholder="Ex: Av. Central, Comercial Norte">
            </div>

            <div class="input-group">
                <label for="numero">Número</label>
                <input type="text" id="numero" name="numero" required placeholder="Ex: 12">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="complemento" placeholder="Ex: Apto 302, Bloco B (Opcional)">
            </div>

            <div class="input-group">
                <label for="bairro">Bairro / Setor</label>
                <input type="text" id="bairro" name="bairro" required placeholder="Ex: Asa Norte, Águas Claras">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="cidade">Cidade (Região - DF)</label>
                <select id="cidade" name="cidade" required class="select-ra-custom">
                    <option value="" disabled selected>Selecione a Região</option>
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

            <div class="input-group">
                <label for="uf">UF</label>
                <input type="text" id="uf" name="uf" value="DF" readonly class="input-uf-readonly">
            </div>

            <div class="input-group">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" required placeholder="70000-000">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="********">
            </div>

            <div class="input-group">
                <label for="confirma_senha">Confirmar Senha</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required placeholder="********">
            </div>
        </div>

        <button type="submit" class="btn-login">Cadastrar</button>
    </form>

    <div class="login-footer-links">
        <a href="/le_cafeteria/index.php?controller=auth&action=login" class="forgot-link">Já tem conta? Entrar</a>
    </div>
</div>

<script src="/le_cafeteria/assets/js/script.js"></script>
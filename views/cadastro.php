<?php
include_once 'includes/cabecalho.php';
?>

        <div class="login-container">

            <div class="section-title">
                <span>CADASTRO</span>

            </div>

            <form action="processa-cadastro.php" method="POST">
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
                <a href="login.php" class="forgot-link">Já tem conta? Entrar</a>
            </div>
        </div>

<script src="/le_cafeteria/assets/js/script.js"></script>
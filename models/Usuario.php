<?php
class Usuario {
	//Dados estáticos simulando o que viria do banco de dados 
	private $usuario_correto = "admin";
	private $senha_correta = "12345";
	
	//Método que o Controller vai chamar para tentar logar
	public function autenticar($login_digitado, $senha_digitada) {
		if ($login_digitado === $this->usuario_correto && $senha_digitada === $this->senha_correta) {
			return true; ///Sucesso 
		}
		return false;//Falhou
	}
}
?>
<?php
require_once 'models/Usuario.php';

class LoginController {
	
	public function index() {
		$erro = "";
		
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$login = $_POST['login'];
			$senha = $_POST['senha'];
			
		     $usuarioModel = new Usuario();

			 if ($usuarioModel->autenticar($login, $senha)) {
				 //Sucesso: Cria a credencial na Sessão
				 $_SESSION['logado'] = true;
			     $_SESSION['usuario'] = $login;
			 
	             header ("Location: index.php?acao=dashboard");
				   exit();
		        }  else {
			     $erro = "Usuário ou senha inválidos!";
	       }
	    }
		require_once 'views/login.php';

	}
   public function dashboard() {
	  //Segurança: Se não existir a sessão 'logado', expulsa para o login
      if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
	       header ("Location: index.php");
	       exit();
        }   

	     require_once 'views/dashboard.php';
   }
	
	//Função para destruir a sessão e deslogar
	public function sair() {
		session_destroy();
		header ("location: index.php");
		exit();
	}
}

?>
<?php 
// 1. Inicia a sessão! Tem que ser a primeira linha do sistema.
session_start();
//comando para mostrar erros na tela
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'controllers/LoginController.php';

$controller = new LoginController();

///Um roteador muito simples: pega a 'acao' da URL. Se não tiver, o padrão é 'index'
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'index';

///Direciona o fluxa
if ($acao == 'dashboard') {
	$controller->dashboard();
} else {
	//Ação padrão carrega a tela de login[
	$controller->index();
}
?>
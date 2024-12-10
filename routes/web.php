<?php
// Incluindo configurações globais e dependências necessárias
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';
require_once '../config/constants.php';

// Capturando a URL solicitada
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Removendo parâmetros GET da URL, se existirem

// Verificando as rotas
switch ($request) {
    case '/': // Rota para a página inicial
    case '/dashboard': // Painel principal do sistema
        include '../views/dashboard.php';
        break;

    case '/login': // Rota para o login
        include '../controllers/LoginController.php';
        break;

    case '/logout': // Rota para sair do sistema
        include '../controllers/LogoutController.php';
        break;

    case '/faturas': // Rota para listar faturas
        include '../controllers/FaturaController.php';
        break;

    case '/faturas/cadastrar': // Rota para o formulário de lançamento de faturas
        include '../views/faturas/cadastrar.php';
        break;

    case '/transportadoras': // Rota para listar transportadoras
        include '../controllers/TransportadoraController.php';
        break;

    case '/transportadoras/cadastrar': // Rota para o formulário de cadastro de transportadoras
        include '../views/transportadoras/cadastrar.php';
        break;

    default: // Caso nenhuma rota seja encontrada
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}
?>

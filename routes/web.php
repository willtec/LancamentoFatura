<?php
// Incluindo configurações globais e dependências necessárias
require_once 'C:/tools/Apache24/htdocs/LancamentoFatura/config/db.php';
require_once 'C:/tools/Apache24/htdocs/LancamentoFatura/config/auth.php';
require_once 'C:/tools/Apache24/htdocs/LancamentoFatura/config/helpers.php';
require_once 'C:/tools/Apache24/htdocs/LancamentoFatura/config/constants.php'; // Inclui BASE_URL

// Capturando a URL solicitada
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Removendo parâmetros GET da URL, se existirem

// Roteamento baseado na URL
switch ($request) {
    case '/': // Página inicial
    case '/dashboard': // Painel principal do sistema
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/views/dashboard.php';
        break;

    case '/login': // Rota para o login
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/views/login.php';
        break;

    case '/logout': // Rota para sair do sistema
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/controllers/LogoutController.php';
        break;

    case '/faturas': // Listar faturas
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/controllers/FaturaController.php';
        break;

    case '/faturas/cadastrar': // Formulário de cadastro de faturas
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/views/faturas/cadastrar.php';
        break;

    case '/transportadoras': // Listar transportadoras
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/controllers/TransportadoraController.php';
        break;

    case '/transportadoras/cadastrar': // Formulário de cadastro de transportadoras
        include 'C:/tools/Apache24/htdocs/LancamentoFatura/views/transportadoras/cadastrar.php';
        break;

    default: // Página não encontrada
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}
?>

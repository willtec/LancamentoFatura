<?php
// Este arquivo gerencia as rotas do sistema

// Iniciar a sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir as rotas disponíveis no sistema
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET na URL

switch ($request) {
    case '/':
    case '/LancamentoFatura/':
    case '/LancamentoFatura/dashboard':
        // Verificar autenticação
        if (!isset($_SESSION['usuario'])) {
            header('Location: /LancamentoFatura/login');
            exit();
        }
        include __DIR__ . '/../views/dashboard.php';
        break;

    case '/LancamentoFatura/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include __DIR__ . '/../controllers/LoginController.php';
        } else {
            include __DIR__ . '/../views/login.php';
        }
        break;

    case '/LancamentoFatura/logout':
        include __DIR__ . '/../controllers/LogoutController.php';
        break;

    case '/LancamentoFatura/faturas':
        include __DIR__ . '/../controllers/FaturaController.php';
        break;

    case '/LancamentoFatura/faturas/cadastrar':
        // Redireciona para o método de cadastro do controlador
        include __DIR__ . '/../controllers/FaturaController.php';
        $controller = new FaturaController();
        $controller->cadastrar();
        break;

    case '/LancamentoFatura/transportadoras':
        include __DIR__ . '/../controllers/TransportadoraController.php';
        break;

    case '/LancamentoFatura/transportadoras/cadastrar':
        include __DIR__ . '/../views/transportadoras/cadastrar.php';
        break;

    default:
        // Página não encontrada
        http_response_code(404);
        echo "<h1>404 - Página $request não encontrada</h1>";
        break;
}

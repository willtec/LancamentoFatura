<?php
// Este arquivo gerencia as rotas do sistema

// Iniciar a sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Capturar a URI atual
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET na URL

// Verificar se o prefixo "/LancamentoFatura" está presente
$basePath = '/LancamentoFatura';
if (strpos($request, $basePath) === 0) {
    $request = substr($request, strlen($basePath));
}

// Adicionar uma barra inicial para rotas padrão
$request = '/' . ltrim($request, '/');

// Função para verificar autenticação
function verificarAutenticacao() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: /login');
        exit();
    }
}

// Definir as rotas disponíveis no sistema
switch (true) {
    // Rota inicial ou dashboard
    case $request === '/' || $request === '/dashboard':
        verificarAutenticacao();
        include __DIR__ . '/../views/dashboard.php';
        break;

    // Rota para login
    case $request === '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include __DIR__ . '/../controllers/LoginController.php';
        } else {
            include __DIR__ . '/../views/login.php';
        }
        break;

    // Rota para logout
    case $request === '/logout':
        verificarAutenticacao();
        include __DIR__ . '/../controllers/LogoutController.php';
        break;

    // Rota para listar faturas
    case $request === '/faturas':
        verificarAutenticacao();
        include __DIR__ . '/../controllers/FaturaController.php';
        break;

    // Rota para cadastrar faturas
    case $request === '/faturas/cadastrar':
        verificarAutenticacao();
        include __DIR__ . '/../views/faturas/cadastrar.php';
        break;

    // Rota para listar transportadoras
    case $request === '/transportadoras':
        verificarAutenticacao();
        include __DIR__ . '/../controllers/TransportadoraController.php';
        break;

    // Rota para cadastrar transportadoras
    case $request === '/transportadoras/cadastrar':
        verificarAutenticacao();
        include __DIR__ . '/../views/transportadoras/cadastrar.php';
        break;

    // Rota para editar transportadoras (GET com ID na URL)
    case preg_match('#^/transportadoras/editar/(\d+)$#', $request, $matches):
        verificarAutenticacao();
        $_GET['id'] = filter_var($matches[1], FILTER_SANITIZE_NUMBER_INT); // Capturar e sanitizar o ID da URL
        include __DIR__ . '/../controllers/TransportadoraController.php';
        break;

    // Processar edição (formulário enviado via POST)
    case $request === '/transportadoras/editar':
        verificarAutenticacao();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include __DIR__ . '/../controllers/TransportadoraController.php';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $_GET['id'] = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            include __DIR__ . '/../controllers/TransportadoraController.php';
        } else {
            http_response_code(405); // Método não permitido
            echo "Método não permitido";
        }
        break;

    // Página para listar transportadoras
    case $request === '/transportadoras/listar':
        verificarAutenticacao();
        include __DIR__ . '/../views/transportadoras/listar.php';
        break;

    // Rota para exclusão de transportadoras
    case preg_match('#^/transportadoras/excluir/(\d+)$#', $request, $matches):
        verificarAutenticacao();
        $_GET['id'] = filter_var($matches[1], FILTER_SANITIZE_NUMBER_INT); // Capturar e sanitizar o ID
        include __DIR__ . '/../controllers/TransportadoraController.php';
        break;

    // Rota não encontrada
    default:
        http_response_code(404);
        echo "404 - Página $request não encontrada";
        break;
}

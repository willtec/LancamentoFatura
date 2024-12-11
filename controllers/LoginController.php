<?php
require_once(__DIR__ . '/../models/Usuario.php');
require_once(__DIR__ . '/../config/helpers.php');

// Iniciar a sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar os dados do formulário
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Validar se os campos foram preenchidos
    if (empty($email) || empty($senha)) {
        $erro = 'Email e senha são obrigatórios.';
        include __DIR__ . '/../views/login.php';
        exit();
    }

    // Autenticação
    $usuario = Usuario::autenticar($email, $senha);

    if ($usuario) {
        // Usuário autenticado, salvar na sessão
        $_SESSION['usuario'] = $usuario;
        redirecionar('/LancamentoFatura/dashboard');
    } else {
        // Falha na autenticação
        $erro = 'Email ou senha incorretos.';
        include __DIR__ . '/../views/login.php';
    }
} else {
    // Se não for POST, exibir o formulário de login
    include __DIR__ . '/../views/login.php';
}
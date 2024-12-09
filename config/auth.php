<?php
session_start();

// Inclua as configurações globais
require_once __DIR__ . '/constants.php';

// Redirecionar para login se o usuário não estiver autenticado
if (!isset($_SESSION['usuario']) && !in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'logout.php'])) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// Verificar permissões de acesso
function verificarPermissao($nivelRequerido)
{
    if ($_SESSION['usuario']['nivel_acesso'] !== $nivelRequerido) {
        http_response_code(403);
        die('Acesso negado.');
    }
}

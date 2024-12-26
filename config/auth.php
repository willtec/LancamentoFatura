<?php
// Inclua as configurações globais
require_once __DIR__ . '/constants.php';

// Função de autenticação
function autenticarUsuario()
{
    if (!isset($_SESSION['usuario'])) {
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
}

// Verificar permissões de acesso
function verificarPermissao($nivelRequerido)
{
    if ($_SESSION['usuario']['nivel_acesso'] !== $nivelRequerido) {
        http_response_code(403);
        die('Acesso negado.');
    }
}

// Autenticar o usuário para todas as páginas menos login e logout
autenticarUsuario();

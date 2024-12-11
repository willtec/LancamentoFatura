<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");
session_start(); // Iniciar a sessão se ela ainda não estiver ativa

if (session_status() === PHP_SESSION_ACTIVE) {
    // Limpar todas as variáveis da sessão
    $_SESSION = [];

    // Destruir o cookie da sessão (melhoria de segurança)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destruir a sessão completamente
    session_destroy();
}

// Redirecionar para a página de login
header('Location: /LancamentoFatura/login');
exit();

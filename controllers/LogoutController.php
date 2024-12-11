<?php
session_start();

// Destruir a sessão do usuário e redirecionar para a página de login
session_destroy();
header('Location: /login');
exit();
?>

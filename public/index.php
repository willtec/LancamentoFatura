<?php
// Configurações iniciais
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';

// Capturar a URL e encaminhar para as rotas
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET

include '../routes/web.php';
?>

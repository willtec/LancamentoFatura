<?php
// Configurações iniciais
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/constants.php'; // Inclua a constante BASE_URL

// Capturar a URL e encaminhar para as rotas
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET

include '../routes/web.php';

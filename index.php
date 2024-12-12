<?php
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);

// Arquivo principal do sistema (index.php)

// Inicia o autoload para carregar classes automaticamente
spl_autoload_register(function ($class_name) {
    $file = BASE_PATH . '/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log("Arquivo da classe não encontrado: " . $file);
    }
});

// Define as configurações iniciais
define('BASE_PATH', __DIR__);

// Carrega o arquivo de rotas
require_once BASE_PATH . '/routes/web.php';

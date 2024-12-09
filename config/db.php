<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'will');
define('DB_PASS', 'Reboot3!');
define('DB_NAME', 'faturas');

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar com o banco de dados: ' . $e->getMessage());
}
?>

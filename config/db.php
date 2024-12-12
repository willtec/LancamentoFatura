<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'will');
define('DB_PASS', 'Reboot3!');
define('DB_NAME', 'faturas');

/**
 * Função para obter a conexão com o banco de dados.
 *
 * @return PDO Objeto de conexão PDO.
 */
function getDBConnection()
{
    static $pdo = null; // Uso de singleton para evitar múltiplas conexões.

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erro ao conectar com o banco de dados: ' . $e->getMessage());
        }
    }

    return $pdo;
}

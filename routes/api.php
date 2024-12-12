<?php
// Configurações para APIs
header('Content-Type: application/json');

require_once '../config/db.php';

// Capturar o endpoint solicitado
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET

switch ($request) {
    case '/api/faturas':
        $stmt = $pdo->query("SELECT * FROM faturas");
        $faturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($faturas);
        break;

    case '/api/transportadoras':
        $stmt = $pdo->query("SELECT * FROM transportadora");
        $transportadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($transportadoras);
        break;

    default:
        http_response_code(404);
        echo json_encode(['erro' => 'Endpoint não encontrado']);
        break;
}

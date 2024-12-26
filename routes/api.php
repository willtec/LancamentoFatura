<?php
// Configurações para APIs
header('Content-Type: application/json');

require_once '/../config/db.php';

// Capturar o endpoint solicitado
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET

switch ($request) {
    case '/api/transportadoras':
        if (isset($_GET['q'])) {
            $query = $_GET['q'];
            $stmt = $pdo->prepare("SELECT id, codigo, nome FROM transportadora WHERE codigo LIKE :termo OR nome LIKE :termo LIMIT 10");
            $stmt->bindValue(':termo', "%$query%", PDO::PARAM_STR);
            $stmt->execute();
            $transportadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($transportadoras);
        } else {
            $stmt = $pdo->query("SELECT id, codigo, nome FROM transportadora");
            $transportadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($transportadoras);
        }
        break;

    case '/api/faturas':
        $stmt = $pdo->query("SELECT * FROM faturas");
        $faturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($faturas);
        break;

    default:
        http_response_code(404);
        echo json_encode(['erro' => 'Endpoint não encontrado']);
        break;
}

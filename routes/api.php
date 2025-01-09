<?php
// Configurações para APIs
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

// Inicializar conexão com o banco
$pdo = getDBConnection();

// Capturar o endpoint solicitado
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = strtok($requestUri, '?'); // Ignorar parâmetros GET

// Função para responder com erro 404
function respondNotFound() {
    http_response_code(404);
    echo json_encode(['erro' => 'Endpoint não encontrado']);
}

// Roteamento básico
switch ($requestPath) {
    case '/api/transportadoras':
        if ($requestMethod === 'GET' && isset($_GET['q'])) {
            $query = $_GET['q'];

            // Busca por nome, código ou CNPJ
            $stmt = $pdo->prepare("
                SELECT id, codigo, cnpj, nome 
                FROM transportadora
                WHERE 
                    codigo LIKE :termo OR
                    cnpj LIKE :termo OR
                    nome LIKE :termo
                LIMIT 10
            ");
            $stmt->bindValue(':termo', "%$query%", PDO::PARAM_STR);
            $stmt->execute();

            $transportadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($transportadoras);
        } else {
            echo json_encode([]);
        }
        break;

    case '/api/faturas':
        if ($requestMethod === 'GET') {
            $stmt = $pdo->query("SELECT * FROM faturas");
            $faturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($faturas);
        } else {
            respondNotFound();
        }
        break;

    // Adicionar outros endpoints conforme necessário

    default:
        respondNotFound();
        break;
}

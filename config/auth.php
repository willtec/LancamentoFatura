<?php
// Inclua as configurações globais e a conexão com o banco de dados
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/db.php';

/**
 * Função para autenticar o usuário.
 * Redireciona para a página de login se o usuário não estiver autenticado.
 */
function autenticarUsuario()
{
    if (!isset($_SESSION['usuario'])) {
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

    // Certifique-se de que o nível de acesso está na sessão
    if (!isset($_SESSION['usuario']['nivel_acesso'])) {
        $usuario = buscarUsuarioPorId($_SESSION['usuario']['id']);
        $_SESSION['usuario']['nivel_acesso'] = $usuario['nivel_acesso'] ?? null;
    }
}

/**
 * Verifica se o usuário tem permissão de acesso.
 *
 * @param string $nivelRequerido Nível de acesso necessário.
 */
function verificarPermissao($nivelRequerido)
{
    if (!isset($_SESSION['usuario']['nivel_acesso']) || $_SESSION['usuario']['nivel_acesso'] !== $nivelRequerido) {
        http_response_code(403);
        die('Acesso negado.');
    }
}

/**
 * Busca o usuário no banco de dados pelo ID.
 *
 * @param int $id ID do usuário.
 * @return array|null Dados do usuário ou null se não encontrado.
 */
function buscarUsuarioPorId($id)
{
    try {
        $pdo = getDBConnection(); // Conexão utilizando db.php
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
        return null;
    }
}

// Autenticar o usuário para todas as páginas menos login e logout
autenticarUsuario();

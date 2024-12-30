<?php
// Importa as configurações necessárias
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';

// Verifica se o usuário está autenticado
verificarAutenticacao();

// O token CSRF é gerado no controlador e já deve estar na sessão
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Transportadora</title>
    <link rel="stylesheet" href="/public/styles/cadastrar_transportadora.css">
</head>
<body>
    <div class="container">
        <h1>Cadastrar Nova Transportadora</h1>
        <form id="form-manual" method="POST" action="/transportadoras/create">
            <input type="hidden" name="action" value="create_single">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="codigo">Código:</label>
            <input type="text" name="codigo" id="codigo" required>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            <label for="cnpj">CNPJ:</label>
            <input type="text" name="cnpj" id="cnpj" required>
            <button type="submit">Cadastrar</button>
        </form>

        <div class="import-section">
            <h1>Importar Transportadoras</h1>
            <form id="form-import" method="POST" enctype="multipart/form-data" action="/transportadoras/import">
                <input type="hidden" name="action" value="import_csv">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="file" name="csv_import" id="csv_import" accept=".csv" required>
                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>
</body>
</html>

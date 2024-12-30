<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Transportadora</title>
</head>
<body>
    <div class="container">
        <h1>Cadastrar Nova Transportadora</h1>
        <form method="POST" action="/transportadoras">
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
            <form method="POST" enctype="multipart/form-data" action="/transportadoras">
                <label for="csv_import">Arquivo CSV:</label>
                <input type="file" name="csv_import" id="csv_import" accept=".csv" required>

                <button type="submit">Importar</button>
            </form>
        </div>
    </div>
</body>
</html>

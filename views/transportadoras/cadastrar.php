<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Transportadora</title>
</head>
<body>
    <h1>Cadastrar Nova Transportadora</h1>
    <form method="POST" action="/transportadoras">
        <label for="codigo">Código:</label>
        <input type="text" name="codigo" id="codigo" required>
        <br>
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        <br>
        <label for="cnpj">CNPJ:</label>
        <input type="text" name="cnpj" id="cnpj" required>
        <br>
        <button type="submit">Cadastrar</button>
    </form>

    <h2>Importar Transportadoras</h2>
    <form method="POST" enctype="multipart/form-data" action="/transportadoras">
        <label for="csv_import">Arquivo CSV:</label>
        <input type="file" name="csv_import" id="csv_import" accept=".csv" required>
        <br>
        <button type="submit">Importar</button>
    </form>
</body>
</html>

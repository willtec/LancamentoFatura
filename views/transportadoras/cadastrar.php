<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Transportadora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #555;
        }
        input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .import-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
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

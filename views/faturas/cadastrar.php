<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lançar Fatura</title>
</head>
<body>
    <h1>Lançar Nova Fatura</h1>
    <form method="POST" enctype="multipart/form-data" action="/faturas">
        <label for="transportadora">Transportadora:</label>
        <select name="transportadora_id" id="transportadora">
            <!-- Opções preenchidas dinamicamente -->
        </select>
        <br>
        <label for="numero_fatura">Número da Fatura:</label>
        <input type="text" name="numero_fatura" id="numero_fatura" required>
        <br>
        <label for="vencimento">Data de Vencimento:</label>
        <input type="date" name="vencimento" id="vencimento" required>
        <br>
        <label for="valor">Valor:</label>
        <input type="number" step="0.01" name="valor" id="valor" required>
        <br>
        <label for="boleto">Boleto (PDF):</label>
        <input type="file" name="boleto" id="boleto" accept="application/pdf" required>
        <br>
        <label for="arquivos_cte">Arquivos XML (ZIP):</label>
        <input type="file" name="arquivos_cte" id="arquivos_cte" accept=".zip" required>
        <br>
        <button type="submit">Lançar</button>
    </form>
</body>
</html>

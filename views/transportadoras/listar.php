<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Transportadoras</title>
</head>
<body>
    <h1>Transportadoras</h1>
    <a href="/transportadoras/cadastrar">Cadastrar Nova Transportadora</a>
    <table border="1">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>CNPJ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transportadoras as $transportadora): ?>
                <tr>
                    <td><?= $transportadora['codigo']; ?></td>
                    <td><?= $transportadora['nome']; ?></td>
                    <td><?= $transportadora['cnpj']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

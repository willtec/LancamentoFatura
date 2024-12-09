<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bem-vindo, <?= $_SESSION['usuario']['nome']; ?></h1>
    <nav>
        <a href="/faturas">Gerenciar Faturas</a> |
        <a href="/transportadoras">Gerenciar Transportadoras</a> |
        <a href="/logout">Sair</a>
    </nav>
</body>
</html>

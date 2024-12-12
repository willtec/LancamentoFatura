<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/../public/styles/dashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Bem-vindo, <?=$_SESSION['usuario']['nome']?></h1>
            <nav>
                <a href="/LancamentoFatura/faturas">Faturas</a>
                <a href="/LancamentoFatura/transportadoras">Transportadoras</a>
                <a href="/LancamentoFatura/logout" class="logout">Sair</a>
            </nav>
        </header>

        <main>
            <section class="cards">
                <div class="card faturas">
                    <h2>Faturas</h2>
                    <span class="total"><?=$total_faturas ?? 0?></span>
                </div>
                <div class="card transportadoras">
                    <h2>Transportadoras</h2>
                    <span class="total"><?=$total_transportadoras ?? 0?></span>
                </div>
                <div class="card atualizacao">
                    <h2>Última Atualização</h2>
                    <span class="total"><?=$ultima_atualizacao ?? 'Não disponível'?></span>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
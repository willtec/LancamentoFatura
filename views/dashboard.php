<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Transportadora.php';
require_once __DIR__ . '/../models/Fatura.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: /login');
    exit;
}

// Obter o total de transportadoras
$total_transportadoras = Transportadora::contarTransportadoras();

// Obter o total de faturas
$total_faturas = Fatura::listarTodas() ? count(Fatura::listarTodas()) : 0;

// Obter as últimas atualizações das tabelas com o responsável
$ultima_atualizacao_transportadoras = Transportadora::obterUltimaAtualizacaoComUsuario();
$ultima_atualizacao_faturas = Fatura::obterUltimaAtualizacaoComUsuario();
$ultima_atualizacao_usuarios = Usuario::obterUltimaAtualizacaoComUsuario();

// Determinar a última atualização mais recente
$datas_atualizacoes = [
    'transportadoras' => $ultima_atualizacao_transportadoras,
    'faturas' => $ultima_atualizacao_faturas,
    'usuarios' => $ultima_atualizacao_usuarios,
];

// Filtrar datas não nulas
$datas_atualizacoes_filtradas = array_filter($datas_atualizacoes, fn($data) => !empty($data['data']));

// Ordenar pela data mais recente
usort($datas_atualizacoes_filtradas, fn($a, $b) => strtotime($b['data']) - strtotime($a['data']));

// Pegar a última atualização mais recente
$atualizacao_mais_recente = $datas_atualizacoes_filtradas[0] ?? null;

?>

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
            <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></h1>
            <nav>
                <a href="/LancamentoFatura/faturas">Faturas</a>
                <a href="/LancamentoFatura/transportadoras">Transportadoras</a>
                <a href="/LancamentoFatura/usuarios">Usuários</a>
                <a href="/LancamentoFatura/logout" class="logout">Sair</a>
            </nav>
        </header>

        <main>
            <section class="cards">
                <div class="card faturas">
                    <h2>Faturas</h2>
                    <span class="total"><?= htmlspecialchars($total_faturas) ?></span>
                </div>
                <div class="card transportadoras">
                    <h2>Transportadoras</h2>
                    <span class="total"><?= htmlspecialchars($total_transportadoras) ?></span>
                </div>
                <div class="card atualizacao">
                    <h2>Última Atualização</h2>
                    <span class="total">
                        <?php if ($atualizacao_mais_recente): ?>
                            <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($atualizacao_mais_recente['data']))) ?> 
                            - <?= htmlspecialchars($atualizacao_mais_recente['tabela']) ?> 
                            por <?= htmlspecialchars($atualizacao_mais_recente['usuario']) ?>
                        <?php else: ?>
                            Não disponível
                        <?php endif; ?>
                    </span>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

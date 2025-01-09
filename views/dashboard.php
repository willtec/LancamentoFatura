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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/../public/styles/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <header class="header">
            <div class="header-content">
                <div class="user-welcome">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h1>Bem-vindo, <?=htmlspecialchars($_SESSION['usuario']['nome'])?></h1>
                </div>
                <nav class="nav-menu">
                    <a href="/LancamentoFatura/faturas" class="nav-item">
                        <i class="fas fa-file-invoice"></i>
                        <span>Faturas</span>
                    </a>
                    <a href="/LancamentoFatura/transportadoras" class="nav-item">
                        <i class="fas fa-truck"></i>
                        <span>Transportadoras</span>
                    </a>
                    <a href="/LancamentoFatura/usuarios" class="nav-item">
                        <i class="fas fa-users"></i>
                        <span>Usuários</span>
                    </a>
                    <a href="/LancamentoFatura/logout" class="nav-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </a>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <div class="cards-grid">
                <div class="card faturas">
                    <div class="card-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="card-content">
                        <h2>Faturas</h2>
                        <span class="total"><?=htmlspecialchars($total_faturas)?></span>
                        <p class="subtitle">Total de faturas registradas</p>
                    </div>
                </div>

                <div class="card transportadoras">
                    <div class="card-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="card-content">
                        <h2>Transportadoras</h2>
                        <span class="total"><?=htmlspecialchars($total_transportadoras)?></span>
                        <p class="subtitle">Transportadoras cadastradas</p>
                    </div>
                </div>

                <div class="card atualizacao">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <h2>Última Atualização</h2>
                        <span class="total">
                            <?php if ($atualizacao_mais_recente): ?>
                                <div class="update-info">
                                    <div class="update-date">
                                        <?=htmlspecialchars(date('d/m/Y H:i:s', strtotime($atualizacao_mais_recente['data'])))?>
                                    </div>
                                    <div class="update-details">
                                        <?=htmlspecialchars($atualizacao_mais_recente['tabela'])?>
                                        por <?=htmlspecialchars($atualizacao_mais_recente['usuario'])?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="no-data">Não disponível</div>
                            <?php endif;?>
                        </span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

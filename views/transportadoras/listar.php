<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportadoras</title>
    <link rel="stylesheet" href="/public/styles/listar_transportadora.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="/public/scripts/transportadora.js" defer></script>
    <script src="/public/scripts/theme-toggle.js" defer></script>
</head>
<body>
    <div class="app-container">
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <div class="breadcrumb">
                    <a href="/dashboard">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Transportadoras</span>
                </div>
                <div class="top-nav-actions">
                    <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content-container">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="header-content">
                        <div class="header-title">
                            <i class="fas fa-truck header-icon"></i>
                            <div class="header-text">
                                <h1>Transportadoras</h1>
                                <p>Gerenciamento de transportadoras</p>
                            </div>
                        </div>
                        <a href="transportadoras/cadastrar" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Nova Transportadora
                        </a>
                    </div>
                    <div class="search-container">
                        <form id="search-form" method="GET" action="/transportadoras/listar">
                            <div class="search-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Buscar transportadora..."
                                    class="search-input"
                                    value="<?=htmlspecialchars($dadosPaginacao['searchTerm'] ?? '')?>"
                                >
                                <button type="submit" class="btn-search">Buscar</button>

                                <!-- Exibir o botão "Limpar" apenas se houver o parâmetro "search" na URL -->
                                <?php if (isset($_GET['search'])): ?>
                                    <a href="/LancamentoFatura/transportadoras" class="btn-clear">Limpar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="card table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="th-content">
                                        <i class="fas fa-hashtag"></i>
                                        <span>Código</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="th-content">
                                        <i class="fas fa-building"></i>
                                        <span>Nome</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="th-content">
                                        <i class="fas fa-file-alt"></i>
                                        <span>CNPJ</span>
                                    </div>
                                </th>
                                <th class="actions-column">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
// Função para formatar o CNPJ
function formatarCNPJ($cnpj)
{
    $cnpj = preg_replace('/\D/', '', $cnpj); // Remove caracteres não numéricos
    return preg_match('/^\d{14}$/', $cnpj)
    ? substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2)
    : $cnpj; // Retorna o CNPJ formatado ou o valor original se inválido
}
?>

                        <?php if (!empty($dadosPaginacao['transportadoras'])): ?>
                        <?php foreach ($dadosPaginacao['transportadoras'] as $transportadora): ?>
                            <tr class="<?=$transportadora['ativo'] ? '' : 'row-inactive'?>">
                                <td><?=htmlspecialchars($transportadora['codigo'])?></td>
                                <td><?=htmlspecialchars($transportadora['nome'])?></td>
                                <td><?=htmlspecialchars(formatarCNPJ($transportadora['cnpj']))?></td>
                                <td class="actions-column">
                                    <div class="action-buttons">
                                        <a href="/transportadoras/editar/<?=htmlspecialchars($transportadora['id'])?>"
                                        class="btn-action btn-edit"
                                        title="Editar">
                                            <i class="fas fa-edit"></i>
                                            <span>Editar</span>
                                        </a>
                                        <?php if ($transportadora['ativo']): ?>
                                            <a href="/transportadoras/excluir/<?=htmlspecialchars($transportadora['id'])?>"
                                            class="btn-action btn-delete"
                                            title="Excluir"
                                            onclick="return confirm('Tem certeza que deseja excluir esta transportadora?');">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>Excluir</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="/transportadoras/ativar/<?=htmlspecialchars($transportadora['id'])?>"
                                            class="btn-action btn-activate"
                                            title="Tornar visível"
                                            onclick="return confirm('Deseja tornar esta transportadora visível novamente?');">
                                                <i class="fas fa-eye"></i>
                                                <span>Tornar Visível</span>
                                            </a>
                                        <?php endif;?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                        <tr class="no-results">
                            <td colspan="4">
                                <div class="no-results-content">
                                    <i class="fas fa-inbox"></i>
                                    <p>Nenhuma transportadora encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif;?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    <div class="pagination">
                    <button
                        class="btn-pagination prev"
                        data-page="<?=($dadosPaginacao['currentPage'] ?? 1) - 1?>"
                        <?=($dadosPaginacao['currentPage'] ?? 1) > 1 ? '' : 'disabled'?>>
                        <i class="fas fa-chevron-left"></i>
                        Anterior
                    </button>

                        <span class="page-info">
                            Página <?=htmlspecialchars($dadosPaginacao['currentPage'] ?? 1)?>
                            de <?=htmlspecialchars($dadosPaginacao['totalPages'] ?? 1)?>
                        </span>

                        <button
                            class="btn-pagination next"
                            data-page="<?=($dadosPaginacao['currentPage'] ?? 1) + 1?>"
                            <?=($dadosPaginacao['currentPage'] ?? 1) < ($dadosPaginacao['totalPages'] ?? 1) ? '' : 'disabled'?>>
                            Próximo
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <div class="results-info">
                        <i class="fas fa-list"></i>
                        Total de registros: <?=htmlspecialchars($dadosPaginacao['totalItems'] ?? 0)?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($_SESSION['mensagem']) && isset($_SESSION['mensagem']['tipo'], $_SESSION['mensagem']['texto'])): ?>
        <div class="alert alert-<?=htmlspecialchars($_SESSION['mensagem']['tipo'])?>">
            <i class="fas fa-<?=$_SESSION['mensagem']['tipo'] === 'success' ? 'check-circle' : 'exclamation-circle'?>"></i>
            <p><?=htmlspecialchars($_SESSION['mensagem']['texto'])?></p>
            <button class="alert-close" aria-label="Fechar" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['mensagem']);?>
    <?php endif;?>
</body>
</html>

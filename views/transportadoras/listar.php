<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportadoras</title>
    <link rel="stylesheet" href="/public/styles/listar_transportadora.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="/../public/scripts/transportadora.js" defer></script>
</head>
<body>
    <div class="container">
        <header class="page-header">
            <div class="header-content">
                <h1>Transportadoras</h1>
                <a href="/transportadoras/cadastrar" class="btn-primary">
                    + Nova Transportadora
                </a>
            </div>
            <div class="search-container">
                <form id="search-form" method="GET" action="/transportadoras/listar">
                    <input
                        type="text"
                        name="search"
                        placeholder="Buscar transportadora..."
                        class="search-input"
                        value="<?=htmlspecialchars($dadosPaginacao['searchTerm'] ?? '')?>"
                    >
                    <button type="submit" class="btn-search">Buscar</button>
                </form>
            </div>
        </header>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th class="actions-column">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dadosPaginacao['transportadoras'])): ?>
                        <?php foreach ($dadosPaginacao['transportadoras'] as $transportadora): ?>
                            <tr>
                                <td><?=htmlspecialchars($transportadora['codigo'])?></td>
                                <td><?=htmlspecialchars($transportadora['nome'])?></td>
                                <td><?=htmlspecialchars($transportadora['cnpj'])?></td>
                                <td class="actions-column">
                                    <div class="action-buttons">
                                        <a href="/transportadoras/editar/<?=htmlspecialchars($transportadora['codigo'])?>" class="btn-edit">Editar</a>
                                        <a href="/transportadoras/excluir/<?=htmlspecialchars($transportadora['codigo'])?>" class="btn-delete">Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nenhuma transportadora encontrada.</td>
                        </tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button
                class="btn-pagination prev"
                <?=($dadosPaginacao['currentPage'] ?? 1) > 1 ? '' : 'disabled'?>
                data-page="<?=($dadosPaginacao['currentPage'] ?? 1) - 1?>"
            >
                Anterior
            </button>

            <span class="page-info">
                Página <?=htmlspecialchars($dadosPaginacao['currentPage'] ?? 1)?> de <?=htmlspecialchars($dadosPaginacao['totalPages'] ?? 1)?>
            </span>

            <button
                class="btn-pagination next"
                <?=($dadosPaginacao['currentPage'] ?? 1) < ($dadosPaginacao['totalPages'] ?? 1) ? '' : 'disabled'?>
                data-page="<?=($dadosPaginacao['currentPage'] ?? 1) + 1?>"
            >
                Próximo
            </button>
        </div>

        <div class="results-info">
            Total de registros: <?=htmlspecialchars($dadosPaginacao['totalItems'] ?? 0)?>
        </div>
    </div>
</body>
</html>

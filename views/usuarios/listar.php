<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/public/styles/listar_usuario.css">
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
                    <span>Gerenciamento de Usuários</span>
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
                            <i class="fas fa-user header-icon"></i>
                            <div class="header-text">
                                <h1>Gerenciamento de Usuários</h1>
                                <p>Gerencie todos os usuários do sistema</p>
                            </div>
                        </div>
                        <a href="transportadoras/cadastrar" class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Novo Usuário
                        </a>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?=htmlspecialchars(count($usuarios))?></div>
                            <div class="stat-label">Total de Usuários</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-user-shield fa-lg"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?=htmlspecialchars(array_reduce($usuarios, fn($carry, $u) => $carry + ($u['nivel_acesso'] === 'administrador' ? 1 : 0), 0))?></div>
                            <div class="stat-label">Administradores</div>
                        </div>
                    </div>
                </div>

                <!-- search -->
                    <div class="search-page-header">
                        <div class="search-container">
                        <form id="search-form" method="GET" action="/usuarios/listar">
                            <div class="search-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Buscar usuário..."
                                    class="search-input"
                                    value="<?=htmlspecialchars($dadosPaginacao['searchTerm'] ?? '')?>"
                                >
                                <button type="submit" class="btn-search">Buscar</button>
                            </div>
                        </form>
                        </div>

                <!-- Table Section -->
                <div class="table-wrapper">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Email</th>
                                    <th>Nível de Acesso</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar" style="background-color: <?=htmlspecialchars(sprintf('#%06X', crc32($usuario['nome'])))?>">
                                                <?=strtoupper(substr(htmlspecialchars($usuario['nome']), 0, 2))?>
                                            </div>
                                            <div class="user-details">
                                                <span class="user-name"><?=htmlspecialchars($usuario['nome'])?></span>
                                                <span class="user-id">ID: <?=htmlspecialchars($usuario['id'])?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?=htmlspecialchars($usuario['email'])?></td>
                                    <td>
                                        <span class="badge <?=strtolower(htmlspecialchars($usuario['nivel_acesso']))?>">
                                            <?=htmlspecialchars($usuario['nivel_acesso'])?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge">
                                            <i class="fas fa-circle"></i>
                                            Ativo
                                        </span>
                                    </td>
                                    <td>
                                        <div class="row-actions">
                                            <a href="/LancamentoFatura/usuarios/editar/<?=htmlspecialchars($usuario['id'])?>"
                                                class="action-btn edit"
                                                title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="/LancamentoFatura/usuarios/excluir/<?=htmlspecialchars($usuario['id'])?>"
                                                class="action-btn delete"
                                                title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="entries-info">
                            Mostrando <span id="currentEntries">10</span> de <span id="totalEntries"><?=htmlspecialchars(count($usuarios))?></span> registros
                        </div>
                        <div class="pagination">
                            <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                            <div class="page-numbers">
                                <button class="page-btn active">1</button>
                                <button class="page-btn">2</button>
                                <button class="page-btn">3</button>
                            </div>
                            <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
        </main>
    </div>
    <script src="/public/scripts/listar_usuario.js" defer></script>
</body>
</html>

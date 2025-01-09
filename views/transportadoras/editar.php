<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar<?=!empty($transportadora['nome']) ? ' - ' . htmlspecialchars($transportadora['nome']) : ''?></title>
    <link rel="stylesheet" href="/public/styles/editar_transportadora.css">
    <script src="/public/scripts/cnpj-validation.js"></script>
    <script src="/public/scripts/theme-toggle.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    <a href="/LancamentoFatura/transportadoras">Transportadoras</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Editar</span>
                </div>
                <div class="top-nav-actions">
                    <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content-container">
                <!-- Mensagens de Alerta -->
                <?php if (!empty($_SESSION['mensagem']) && isset($_SESSION['mensagem']['tipo'], $_SESSION['mensagem']['texto'])): ?>
                    <div class="alert alert-<?=htmlspecialchars($_SESSION['mensagem']['tipo'])?>">
                        <i class="fas fa-<?=$_SESSION['mensagem']['tipo'] === 'success' ? 'check-circle' : 'exclamation-circle'?>"></i>
                        <p><?=htmlspecialchars($_SESSION['mensagem']['texto'])?></p>
                        <button class="alert-close" aria-label="Fechar" onclick="this.parentElement.style.display='none';">&times;</button>
                    </div>
                    <?php unset($_SESSION['mensagem']);?>
                <?php endif;?>

                <!-- Formulário de Edição -->
                <?php if (!empty($transportadora)): ?>
                    <div class="page-header">
                        <div class="header-content">
                            <i class="fas fa-building header-icon"></i>
                            <div class="header-text">
                                <h1>Editar Transportadora</h1>
                                <p><?=htmlspecialchars($transportadora['nome'])?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <form class="edit-form" method="POST" action="/transportadoras/editar" onsubmit="return validarFormulario();">
                            <!-- CSRF e ID -->
                            <input type="hidden" name="id" value="<?=htmlspecialchars($transportadora['id'])?>">
                            <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['csrf_token'])?>">

                            <!-- Campos do Formulário -->
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="codigo">
                                        <i class="fas fa-hashtag"></i>
                                        Código
                                    </label>
                                    <div class="input-wrapper">
                                        <input
                                            type="text"
                                            id="codigo"
                                            name="codigo"
                                            value="<?=htmlspecialchars($transportadora['codigo'])?>"
                                            required
                                            maxlength="20"
                                            placeholder="Digite o código"
                                            autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label for="nome">
                                        <i class="fas fa-building"></i>
                                        Nome da Transportadora
                                    </label>
                                    <div class="input-wrapper">
                                        <input
                                            type="text"
                                            id="nome"
                                            name="nome"
                                            value="<?=htmlspecialchars($transportadora['nome'])?>"
                                            required
                                            maxlength="100"
                                            placeholder="Digite o nome"
                                            autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label for="cnpj">
                                        <i class="fas fa-file-alt"></i>
                                        CNPJ
                                    </label>
                                    <div class="input-wrapper">
                                        <input
                                            type="text"
                                            id="cnpj"
                                            name="cnpj"
                                            value="<?=htmlspecialchars($transportadora['cnpj'])?>"
                                            required
                                            maxlength="18"
                                            pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}"
                                            placeholder="00.000.000/0000-00"
                                            autocomplete="off">
                                        <div class="input-error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='/LancamentoFatura/transportadoras'">
                                    <i class="fas fa-arrow-left"></i>
                                    Voltar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Mensagem de Erro -->
                    <div class="error-card">
                        <div class="error-content">
                            <i class="fas fa-exclamation-circle"></i>
                            <h2>Transportadora não encontrada</h2>
                            <p>Não foi possível encontrar os dados da transportadora solicitada.</p>
                            <a href="/LancamentoFatura/transportadoras" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i>
                                Voltar para Lista
                            </a>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </main>
    </div>
</body>
</html>

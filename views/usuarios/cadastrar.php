<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/../public/styles/cadastrar_usuario.css">
    <script src="/../public/scripts/cadastrar_usuario.js"></script>
</head>
<body>
    <div class="container">
        <div class="form-panel">
            <h1>Cadastrar Usuário</h1>
            <!-- Exibe mensagem de erro, se houver -->
            <?php if (!empty($erro)): ?>
                <div class="error-alert">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>
            <form action="/LancamentoFatura/usuarios/cadastrar" method="POST" id="userForm">
                <div class="input-group">
                    <input type="text" name="nome" id="nome"
                           title="Nome deve conter apenas letras e espaços, mínimo 3 caracteres">
                    <label>Nome</label>
                    <div class="tooltip">Nome completo do usuário</div>
                    <span class="error-message" id="nomeError"></span>
                </div>

                <div class="input-group">
                    <input type="email" name="email" id="email" required>
                    <label>Email</label>
                    <div class="tooltip">Email corporativo</div>
                    <span class="error-message" id="emailError"></span>
                </div>

                <div class="input-group">
                    <input type="password" name="senha" id="senha" required>
                    <label>Senha</label>
                    <i class="password-toggle fas fa-eye" id="togglePassword"></i>
                    <div class="password-strength">
                        <div class="password-strength-meter"></div>
                    </div>
                    <div class="password-info">Força da senha: <span id="strengthText">Fraca</span></div>
                    <div class="requirements">
                        <div class="requirement" id="length"><i class="fas fa-circle"></i> Mínimo 8 caracteres</div>
                        <div class="requirement" id="uppercase"><i class="fas fa-circle"></i> Uma letra maiúscula</div>
                        <div class="requirement" id="lowercase"><i class="fas fa-circle"></i> Uma letra minúscula</div>
                        <div class="requirement" id="number"><i class="fas fa-circle"></i> Um número</div>
                        <div class="requirement" id="special"><i class="fas fa-circle"></i> Um caractere especial</div>
                    </div>
                </div>

                <div class="input-group select-wrapper">
                    <select name="nivel_acesso" required>
                        <option value="" disabled selected>Selecione o nível de acesso</option>
                        <option value="superadministrador">Super Administrador</option>
                        <option value="administrador">Administrador</option>
                        <option value="usuario_leitura">Usuário Leitura</option>
                        <option value="usuario_edicao">Usuário Edição</option>
                    </select>
                </div>

                <button type="submit" id="submitBtn" disabled>Criar Conta</button>
            </form>
        </div>
    </div>
</body>
</html>

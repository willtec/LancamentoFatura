<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../public/styles/login.css">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Login</h1>
        </div>
        <form method="POST" action="/LancamentoFatura/login">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Seu email">
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required placeholder="Sua senha">
            </div>
            <button type="submit" class="login-btn">Entrar</button>

            <!-- Exibe mensagem de erro, se houver -->
            <?php if (isset($erro)): ?>
                <p class="error-message"><?=htmlspecialchars($erro, ENT_QUOTES, 'UTF-8')?></p>
            <?php endif;?>
        </form>
    </div>
</body>
</html>
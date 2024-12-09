<?php
// Função para redirecionar para uma página
function redirecionar($url) {
    header('Location: ' . BASE_URL . $url);
    exit();
}

// Função para exibir mensagens de erro ou sucesso
function exibirMensagens() {
    if (isset($_SESSION['mensagens'])) {
        foreach ($_SESSION['mensagens'] as $tipo => $mensagem) {
            echo "<div class='mensagem {$tipo}'>{$mensagem}</div>";
        }
        unset($_SESSION['mensagens']);
    }
}

// Função para configurar mensagens de sessão
function setMensagem($tipo, $mensagem) {
    $_SESSION['mensagens'][$tipo] = $mensagem;
}
?>

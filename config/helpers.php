<?php
// Funções auxiliares para o sistema

/**
 * Redireciona para uma URL especificada
 *
 * @param string $url Caminho para redirecionar
 */
function redirecionar($url)
{
    header("Location: $url");
    exit();
}

/**
 * Define uma mensagem na sessão para exibição posterior
 *
 * @param string $tipo Tipo da mensagem ('sucesso', 'erro', etc.)
 * @param string $mensagem Conteúdo da mensagem
 */
function setMensagem($tipo, $mensagem)
{
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['mensagem'][$tipo] = $mensagem;
}

/**
 * Obtém uma mensagem da sessão e remove após exibição
 *
 * @param string $tipo Tipo da mensagem ('sucesso', 'erro', etc.)
 * @return string|null Retorna a mensagem ou null se não houver
 */
function getMensagem($tipo)
{
    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_SESSION['mensagem'][$tipo])) {
        $mensagem = $_SESSION['mensagem'][$tipo];
        unset($_SESSION['mensagem'][$tipo]);
        return $mensagem;
    }
    return null;
}

/**
 * Formata valores monetários para exibição
 *
 * @param float $valor Valor numérico
 * @return string Valor formatado em reais
 */
function formatarValor($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Valida um CNPJ
 *
 * @param string $cnpj CNPJ para validar
 * @return bool Retorna true se válido, false se inválido
 */
function validarCNPJ($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if (strlen($cnpj) !== 14) {
        return false;
    }

    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    $peso1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $peso2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    $digito1 = 0;
    for ($i = 0; $i < 12; $i++) {
        $digito1 += $cnpj[$i] * $peso1[$i];
    }
    $digito1 = $digito1 % 11 < 2 ? 0 : 11 - $digito1 % 11;

    $digito2 = 0;
    for ($i = 0; $i < 13; $i++) {
        $digito2 += $cnpj[$i] * $peso2[$i];
    }
    $digito2 = $digito2 % 11 < 2 ? 0 : 11 - $digito2 % 11;

    return $cnpj[12] == $digito1 && $cnpj[13] == $digito2;
}

/**
 * Sanitiza entradas para evitar XSS
 *
 * @param string $dado Dado a ser sanitizado
 * @return string Dado sanitizado
 */
function sanitizar($dado)
{
    return htmlspecialchars($dado, ENT_QUOTES, 'UTF-8');
}
?>

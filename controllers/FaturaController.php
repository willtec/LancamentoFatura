<?php
require_once(__DIR__ . '/../models/Fatura.php');
require_once(__DIR__ . '/../config/auth.php');
require_once(__DIR__ . '/../config/helpers.php');

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    redirecionar('/login');
}

// Processar o cadastro de uma nova fatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação de dados enviados pelo formulário
    $transportadora_id = filter_input(INPUT_POST, 'transportadora_id', FILTER_VALIDATE_INT);
    $numero_fatura = filter_input(INPUT_POST, 'numero_fatura', FILTER_SANITIZE_STRING);
    $vencimento = filter_input(INPUT_POST, 'vencimento', FILTER_SANITIZE_STRING);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
    
    if (!$transportadora_id || !$numero_fatura || !$vencimento || !$valor) {
        setMensagem('erro', 'Dados inválidos. Preencha o formulário corretamente.');
        include '../views/faturas/cadastrar.php';
        exit();
    }

    // Processar upload dos arquivos
    $boleto = null;
    $arquivos_cte = null;

    if (!empty($_FILES['boleto']['name'])) {
        $boleto = salvarArquivo($_FILES['boleto'], '../uploads/boletos/');
    }

    if (!empty($_FILES['arquivos_cte']['name'])) {
        $arquivos_cte = salvarArquivo($_FILES['arquivos_cte'], '../uploads/ctes/');
    }

    // Dados a serem enviados ao modelo
    $dadosFatura = [
        'transportadora_id' => $transportadora_id,
        'numero_fatura' => $numero_fatura,
        'vencimento' => $vencimento,
        'valor' => $valor,
        'boleto' => $boleto,
        'arquivos_cte' => $arquivos_cte,
    ];

    // Tentar cadastrar a fatura
    if (Fatura::cadastrar($dadosFatura)) {
        setMensagem('sucesso', 'Fatura cadastrada com sucesso.');
        redirecionar('/faturas');
    } else {
        setMensagem('erro', 'Erro ao cadastrar a fatura.');
        include '../views/faturas/cadastrar.php';
    }
    exit();
}

// Listar todas as faturas
$faturas = Fatura::listarTodas();
include __DIR__ . '/../views/faturas/listar.php';

// Função auxiliar para salvar arquivos
function salvarArquivo($arquivo, $destino)
{
    $nomeArquivo = basename($arquivo['name']);
    $caminhoCompleto = $destino . $nomeArquivo;

    // Validar tipo de arquivo e mover para o destino
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return $nomeArquivo;
    }

    return null; // Retorna null se o upload falhar
}
?>

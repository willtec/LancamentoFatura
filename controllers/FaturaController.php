<?php
require_once '../models/Fatura.php';
require_once '../config/helpers.php';
require_once '../config/auth.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    redirecionar('/login');
}

// Processar o cadastro de uma nova fatura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosFatura = [
        'transportadora_id' => $_POST['transportadora_id'],
        'numero_fatura' => $_POST['numero_fatura'],
        'vencimento' => $_POST['vencimento'],
        'valor' => $_POST['valor'],
        'boleto' => $_FILES['boleto']['name'],
        'arquivos_cte' => $_FILES['arquivos_cte']['name'],
    ];

    if (Fatura::cadastrar($dadosFatura)) {
        setMensagem('sucesso', 'Fatura cadastrada com sucesso.');
        redirecionar('/faturas');
    } else {
        setMensagem('erro', 'Erro ao cadastrar a fatura.');
        include '../views/faturas/cadastrar.php';
    }
}

// Listar todas as faturas
$faturas = Fatura::listarTodas();
include '../views/faturas/listar.php';
?>

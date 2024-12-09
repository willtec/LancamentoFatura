<?php
require_once '../models/Fatura.php';
require_once '../config/helpers.php';

// Roteamento interno para faturas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $faturas = Fatura::listar();
    include '../views/faturas/listar.php';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = $_POST;

    // Processar uploads
    $boleto = $_FILES['boleto'] ?? null;
    $arquivos_cte = $_FILES['arquivos_cte'] ?? null;

    if ($boleto && $arquivos_cte) {
        $resultado = Fatura::criar($dados, $boleto, $arquivos_cte);

        if ($resultado) {
            setMensagem('sucesso', 'Fatura criada com sucesso.');
            redirecionar('/faturas');
        } else {
            setMensagem('erro', 'Erro ao criar a fatura.');
        }
    }

    include '../views/faturas/cadastrar.php';
} else {
    http_response_code(405);
    echo "Método não permitido.";
}
?>

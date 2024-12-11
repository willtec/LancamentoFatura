<?php
require_once '../models/Transportadora.php';
require_once '../config/helpers.php';
require_once '../config/auth.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    redirecionar('/login');
}

// Processar o cadastro de uma nova transportadora
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosTransportadora = [
        'codigo' => $_POST['codigo'],
        'cnpj' => $_POST['cnpj'],
        'nome' => $_POST['nome']
    ];

    if (Transportadora::cadastrar($dadosTransportadora)) {
        setMensagem('sucesso', 'Transportadora cadastrada com sucesso.');
        redirecionar('/transportadoras');
    } else {
        setMensagem('erro', 'Erro ao cadastrar a transportadora.');
        include '../views/transportadoras/cadastrar.php';
    }
}

// Listar todas as transportadoras
$transportadoras = Transportadora::listarTodas();
include '../views/transportadoras/listar.php';
?>

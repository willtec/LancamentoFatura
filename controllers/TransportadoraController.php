<?php
require_once __DIR__ . '/../models/Transportadora.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    redirecionar('/login');
}

// Processar o cadastro de uma nova transportadora
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosTransportadora = [
        'codigo' => $_POST['codigo'],
        'cnpj' => $_POST['cnpj'],
        'nome' => $_POST['nome'],
    ];

    if (Transportadora::cadastrar($dadosTransportadora)) {
        setMensagem('sucesso', 'Transportadora cadastrada com sucesso.');
        redirecionar('/transportadoras');
    } else {
        setMensagem('erro', 'Erro ao cadastrar a transportadora.');
        include '../views/transportadoras/cadastrar.php';
    }
}

// Configurações de paginação
$itensPorPagina = 10;
$paginaAtual = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$termoBusca = isset($_GET['search']) ? $_GET['search'] : '';

// Calcular offset
$offset = ($paginaAtual - 1) * $itensPorPagina;

// Obter transportadoras com paginação e busca
$totalTransportadoras = Transportadora::contarTransportadoras($termoBusca);
$totalPaginas = ceil($totalTransportadoras / $itensPorPagina);

// Listar transportadoras com filtro e paginação
$transportadoras = Transportadora::listarPaginado($itensPorPagina, $offset, $termoBusca);

// Preparar dados para a view
$dadosPaginacao = [
    'transportadoras' => $transportadoras,
    'currentPage' => $paginaAtual,
    'totalPages' => $totalPaginas,
    'totalItems' => $totalTransportadoras,
    'searchTerm' => $termoBusca,
];

include __DIR__ . '/../views/transportadoras/listar.php';

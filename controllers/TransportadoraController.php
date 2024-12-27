<?php
require_once __DIR__ . '/../models/Transportadora.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

// Verificar se o usuário está autenticado
verificarAutenticacao();

// Verificar CSRF token para todas as solicitações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
    setMensagem('erro', 'Requisição inválida. Token CSRF inválido.');
    redirecionar('/LancamentoFatura/transportadoras');
    exit;
}

// Processar o cadastro de uma nova transportadora
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['id'])) {
    $dadosTransportadora = [
        'codigo' => htmlspecialchars(trim($_POST['codigo'])),
        'cnpj' => htmlspecialchars(trim($_POST['cnpj'])),
        'nome' => htmlspecialchars(trim($_POST['nome'])),
    ];

    if (validarDadosTransportadora($dadosTransportadora) && Transportadora::cadastrar($dadosTransportadora)) {
        setMensagem('sucesso', 'Transportadora cadastrada com sucesso.');
        redirecionar('/LancamentoFatura/transportadoras');
    } else {
        setMensagem('erro', 'Erro ao cadastrar a transportadora. Verifique os dados e tente novamente.');
        include __DIR__ . '/../views/transportadoras/cadastrar.php';
    }
    exit;
}

// Processar a edição de uma transportadora (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = (int) $_POST['id'];
    $dadosAtualizados = [
        'codigo' => htmlspecialchars(trim($_POST['codigo'])),
        'nome' => htmlspecialchars(trim($_POST['nome'])),
        'cnpj' => htmlspecialchars(trim($_POST['cnpj'])),
    ];

    // Verificar se o CNPJ já está sendo usado
    $cnpjExistente = Transportadora::buscarPorCnpj($dadosAtualizados['cnpj']);
    if ($cnpjExistente && $cnpjExistente['id'] !== $id) {
        setMensagem('erro', 'O CNPJ já está em uso por outra transportadora.');
        redirecionar("/LancamentoFatura/transportadoras/editar?id=$id");
        exit;
    }

    if (Transportadora::atualizar($id, $dadosAtualizados)) {
        setMensagem('sucesso', 'Transportadora atualizada com sucesso.');
        redirecionar('/LancamentoFatura/transportadoras');
    } else {
        setMensagem('erro', 'Erro ao atualizar a transportadora.');
        redirecionar("/LancamentoFatura/transportadoras/editar?id=$id");
    }
    exit;
}

// Exibir a tela de edição de uma transportadora (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/editar') !== false) {
    $id = (int) $_GET['id'];
    $transportadora = Transportadora::buscarPorId($id);

    if (!$transportadora) {
        setMensagem('erro', 'Transportadora não encontrada.');
        redirecionar('/LancamentoFatura/transportadoras');
        exit;
    }

    // Gerar CSRF token para segurança
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    include __DIR__ . '/../views/transportadoras/editar.php';
    exit;
}

// Ocultar (excluir logicamente) uma transportadora
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/excluir') !== false) {
    $id = (int) $_GET['id'];

    // Chama o método de ocultação
    if (Transportadora::ocultar($id)) {
        setMensagem('sucesso', 'Transportadora excluída com sucesso.');
    } else {
        setMensagem('erro', 'Erro ao excluir a transportadora.');
    }

    // Sempre redirecionar para a listagem após tentar excluir
    redirecionar('/LancamentoFatura/transportadoras');
    exit;
}

// Configurações de paginação
$itensPorPagina = 10;
$paginaAtual = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$termoBusca = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';

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

/**
 * Valida os dados da transportadora.
 *
 * @param array $dados
 * @return bool
 */
function validarDadosTransportadora(array $dados): bool
{
    if (empty($dados['codigo']) || empty($dados['nome']) || empty($dados['cnpj'])) {
        setMensagem('erro', 'Todos os campos são obrigatórios.');
        return false;
    }

    if (!preg_match('/^\d{14}$/', preg_replace('/\D/', '', $dados['cnpj']))) {
        setMensagem('erro', 'CNPJ inválido. Utilize apenas números.');
        return false;
    }

    return true;
}

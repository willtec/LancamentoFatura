<?php
require_once __DIR__ . '/../models/Transportadora.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

// Verificar se o usuário está autenticado
verificarAutenticacao();

// Registrar logs para depuração
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

// Verificar CSRF token para todas as solicitações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
    setMensagem('erro', 'Requisição inválida. Token CSRF inválido.');
    redirecionar('/LancamentoFatura/transportadoras');
    exit;
}

// Processar requisição baseado na "action"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_single':
            error_log("Dados recebidos para cadastro: " . print_r($_POST, true));

            $dadosTransportadora = [
                'codigo' => isset($_POST['codigo']) ? htmlspecialchars(trim($_POST['codigo'])) : null,
                'cnpj' => isset($_POST['cnpj']) ? htmlspecialchars(trim($_POST['cnpj'])) : null,
                'nome' => isset($_POST['nome']) ? htmlspecialchars(trim($_POST['nome'])) : null,
            ];

            if (validarDadosTransportadora($dadosTransportadora) && Transportadora::criar($dadosTransportadora)) {
                setMensagem('sucesso', 'Transportadora cadastrada com sucesso.');
                redirecionar('/LancamentoFatura/transportadoras');
            } else {
                setMensagem('erro', 'Erro ao cadastrar a transportadora. Verifique os dados e tente novamente.');
                include __DIR__ . '/../views/transportadoras/cadastrar.php';
            }
            break;

        case 'import_csv':
            if (isset($_FILES['csv_import'])) {
                $arquivoCSV = $_FILES['csv_import']['tmp_name'];
                if (Transportadora::importarCSV($arquivoCSV)) {
                    setMensagem('sucesso', 'Arquivo CSV importado com sucesso.');
                } else {
                    setMensagem('erro', 'Erro ao importar o arquivo CSV.');
                }
            } else {
                setMensagem('erro', 'Nenhum arquivo foi enviado para importação.');
            }
            redirecionar('/LancamentoFatura/transportadoras');
            break;

        default:
            setMensagem('erro', 'Ação desconhecida.');
            redirecionar('/LancamentoFatura/transportadoras');
            break;
    }
    exit;
}

// Processar a edição de uma transportadora (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = (int) $_POST['id'];
    error_log("Dados recebidos para edição (ID: $id): " . print_r($_POST, true));

    $dadosAtualizados = [
        'codigo' => isset($_POST['codigo']) ? htmlspecialchars(trim($_POST['codigo'])) : null,
        'nome' => isset($_POST['nome']) ? htmlspecialchars(trim($_POST['nome'])) : null,
        'cnpj' => isset($_POST['cnpj']) ? htmlspecialchars(trim($_POST['cnpj'])) : null,
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
    error_log("Carregando dados para edição da transportadora (ID: $id)");

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
    error_log("Ocultando transportadora (ID: $id)");

    if (Transportadora::ocultar($id)) {
        setMensagem('sucesso', 'Transportadora excluída com sucesso.');
    } else {
        setMensagem('erro', 'Erro ao excluir a transportadora.');
    }

    redirecionar('/LancamentoFatura/transportadoras');
    exit;
}

// Tornar visível uma transportadora
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/ativar') !== false) {
    $id = (int) $_GET['id'];
    error_log("Ativando transportadora (ID: $id)");

    if (Transportadora::ativar($id)) {
        setMensagem('sucesso', 'Transportadora ativada com sucesso.');
    } else {
        setMensagem('erro', 'Erro ao ativar a transportadora.');
    }

    redirecionar('/LancamentoFatura/transportadoras');
    exit;
}

// Configurações de paginação
$itensPorPagina = 10;
$paginaAtual = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$termoBusca = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
error_log("Listando transportadoras: página $paginaAtual, termo de busca: $termoBusca");

// Calcular offset
$offset = ($paginaAtual - 1) * $itensPorPagina;

error_log("Classe Transportadora carregada: " . (class_exists('Transportadora') ? 'Sim' : 'Não'));
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
        error_log("Validação falhou: campos obrigatórios ausentes.");
        setMensagem('erro', 'Todos os campos são obrigatórios.');
        return false;
    }

    if (!preg_match('/^\d{14}$/', preg_replace('/\D/', '', $dados['cnpj']))) {
        error_log("Validação falhou: CNPJ inválido.");
        setMensagem('erro', 'CNPJ inválido. Utilize apenas números.');
        return false;
    }

    return true;
}

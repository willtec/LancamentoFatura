<?php
require_once __DIR__ . '/../models/Fatura.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

// Identificação única para a requisição
$requestId = uniqid('req_', true);

// Função para log estruturado
function registrarLog($nivel, $mensagem, $dados = [])
{
    global $requestId;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = [
        'timestamp' => $timestamp,
        'requestId' => $requestId,
        'nivel' => $nivel,
        'mensagem' => $mensagem,
        'dados' => $dados,
    ];
    error_log(json_encode($logEntry));
}

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    registrarLog('WARNING', 'Usuário não autenticado tentou acessar o controlador.', ['ip' => $_SERVER['REMOTE_ADDR']]);
    redirecionar('/login');
}

// Verificar o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? null;
registrarLog('INFO', "Método HTTP recebido: $method", ['acao' => $acao]);

if ($method === 'POST') {
    registrarLog('INFO', 'Iniciando processamento de formulário de fatura.');
    processarFormularioFatura();
} elseif ($method === 'GET' && $acao) {
    if ($acao === 'deletar' && isset($_GET['id'])) {
        registrarLog('INFO', 'Solicitação de exclusão de fatura.', ['id' => $_GET['id']]);
        deletarFatura($_GET['id']);
    } elseif ($acao === 'editar' && isset($_GET['id'])) {
        registrarLog('INFO', 'Solicitação de edição de fatura.', ['id' => $_GET['id']]);
        editarFatura($_GET['id']);
    } else {
        registrarLog('WARNING', 'Ação inválida ou não especificada.', ['acao' => $acao]);
        listarFaturas();
    }
} else {
    registrarLog('INFO', 'Exibindo lista de faturas.');
    listarFaturas();
}

/**
 * Processa o formulário para criar ou atualizar uma fatura.
 */
function processarFormularioFatura()
{
    registrarLog('DEBUG', 'Dados do formulário recebidos.', ['POST' => $_POST, 'FILES' => $_FILES]);

    $transportadora_id = filter_input(INPUT_POST, 'transportadora_id', FILTER_VALIDATE_INT);
    $numero_fatura = filter_input(INPUT_POST, 'numero_fatura', FILTER_SANITIZE_STRING);
    $vencimento = filter_input(INPUT_POST, 'vencimento', FILTER_SANITIZE_STRING);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);

    if (!$transportadora_id || !$numero_fatura || !$vencimento || !$valor) {
        registrarLog('ERROR', 'Dados inválidos no formulário.', compact('transportadora_id', 'numero_fatura', 'vencimento', 'valor'));
        setMensagem('erro', 'Dados inválidos. Preencha o formulário corretamente.');
        include __DIR__ . '/../views/faturas/cadastrar.php';
        exit();
    }

    $dadosFatura = [
        'transportadora_id' => $transportadora_id,
        'numero_fatura' => $numero_fatura,
        'vencimento' => $vencimento,
        'valor' => $valor,
    ];

    if (!empty($_FILES['boleto']['name'])) {
        $dadosFatura['boleto'] = salvarArquivo($_FILES['boleto'], '../uploads/boletos/');
    }
    if (!empty($_FILES['arquivos_cte']['name'])) {
        $dadosFatura['arquivos_cte'] = salvarArquivo($_FILES['arquivos_cte'], '../uploads/ctes/');
    }

    registrarLog('DEBUG', 'Dados preparados para persistência.', $dadosFatura);

    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        if (Fatura::atualizar($id, $dadosFatura)) {
            registrarLog('INFO', 'Fatura atualizada com sucesso.', ['id' => $id]);
            setMensagem('sucesso', 'Fatura atualizada com sucesso.');
        } else {
            registrarLog('ERROR', 'Erro ao atualizar fatura.', ['id' => $id]);
            setMensagem('erro', 'Erro ao atualizar a fatura.');
        }
    } else {
        if (Fatura::cadastrar($dadosFatura)) {
            registrarLog('INFO', 'Fatura cadastrada com sucesso.');
            setMensagem('sucesso', 'Fatura cadastrada com sucesso.');
        } else {
            registrarLog('ERROR', 'Erro ao cadastrar a fatura.');
            setMensagem('erro', 'Erro ao cadastrar a fatura.');
        }
    }

    redirecionar('/faturas');
}

/**
 * Exibe a lista de faturas.
 */
function listarFaturas()
{
    $faturas = Fatura::listarTodas();
    include __DIR__ . '/../views/faturas/listar.php';
}

/**
 * Processa a exclusão de uma fatura.
 *
 * @param int $id ID da fatura a ser excluída.
 */
function deletarFatura($id)
{
    if (Fatura::deletar($id)) {
        registrarLog('INFO', 'Fatura excluída com sucesso.', ['id' => $id]);
        setMensagem('sucesso', 'Fatura excluída com sucesso.');
    } else {
        registrarLog('ERROR', 'Erro ao excluir fatura.', ['id' => $id]);
        setMensagem('erro', 'Erro ao excluir a fatura.');
    }
    redirecionar('/faturas');
}

/**
 * Exibe o formulário de edição para uma fatura existente.
 *
 * @param int $id ID da fatura a ser editada.
 */
function editarFatura($id)
{
    $fatura = Fatura::buscarPorId($id);
    if ($fatura) {
        registrarLog('INFO', 'Fatura encontrada para edição.', ['id' => $id]);
        include __DIR__ . '/../views/faturas/cadastrar.php';
    } else {
        registrarLog('ERROR', 'Fatura não encontrada.', ['id' => $id]);
        setMensagem('erro', 'Fatura não encontrada.');
        redirecionar('/faturas');
    }
}

/**
 * Salva um arquivo no destino especificado.
 *
 * @param array $arquivo Array contendo as informações do arquivo enviado.
 * @param string $destino Caminho do diretório de destino.
 * @return string|null Retorna o nome do arquivo salvo ou null em caso de falha.
 */
function salvarArquivo($arquivo, $destino)
{
    registrarLog('DEBUG', 'Processando upload de arquivo.', $arquivo);

    $nomeArquivo = basename($arquivo['name']);
    $caminhoCompleto = $destino . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        registrarLog('INFO', 'Arquivo salvo com sucesso.', ['caminho' => $caminhoCompleto]);
        return $nomeArquivo;
    }

    registrarLog('ERROR', 'Erro ao salvar arquivo.', ['nome' => $nomeArquivo]);
    return null;
}

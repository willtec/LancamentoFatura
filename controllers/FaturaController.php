<?php
require_once __DIR__ . '/../models/Fatura.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    redirecionar('/login');
}

// Verificar o método HTTP da requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lidar com a criação de uma nova fatura
    processarFormularioFatura();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao'])) {
    // Ações específicas baseadas no parâmetro 'acao'
    $acao = $_GET['acao'];
    if ($acao === 'deletar' && isset($_GET['id'])) {
        deletarFatura($_GET['id']);
    } elseif ($acao === 'editar' && isset($_GET['id'])) {
        editarFatura($_GET['id']);
    } else {
        listarFaturas();
    }
} else {
    // Exibir a lista de faturas
    listarFaturas();
}

/**
 * Processa o formulário para criar ou atualizar uma fatura.
 */
function processarFormularioFatura()
{
    // Validação de dados enviados pelo formulário
    $transportadora_id = filter_input(INPUT_POST, 'transportadora_id', FILTER_VALIDATE_INT);
    $numero_fatura = filter_input(INPUT_POST, 'numero_fatura', FILTER_SANITIZE_STRING);
    $vencimento = filter_input(INPUT_POST, 'vencimento', FILTER_SANITIZE_STRING);
    $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);

    if (!$transportadora_id || !$numero_fatura || !$vencimento || !$valor) {
        setMensagem('erro', 'Dados inválidos. Preencha o formulário corretamente.');
        include __DIR__ . '/../views/faturas/cadastrar.php';
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

    // Dados para o modelo
    $dadosFatura = [
        'transportadora_id' => $transportadora_id,
        'numero_fatura' => $numero_fatura,
        'vencimento' => $vencimento,
        'valor' => $valor,
        'boleto' => $boleto,
        'arquivos_cte' => $arquivos_cte,
    ];

    // Inserir ou atualizar a fatura
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        if (Fatura::atualizar($id, $dadosFatura)) {
            setMensagem('sucesso', 'Fatura atualizada com sucesso.');
        } else {
            setMensagem('erro', 'Erro ao atualizar a fatura.');
        }
    } else {
        if (Fatura::cadastrar($dadosFatura)) {
            setMensagem('sucesso', 'Fatura cadastrada com sucesso.');
        } else {
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
        setMensagem('sucesso', 'Fatura excluída com sucesso.');
    } else {
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
        include __DIR__ . '/../views/faturas/cadastrar.php';
    } else {
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
    $nomeArquivo = basename($arquivo['name']);
    $caminhoCompleto = $destino . $nomeArquivo;

    // Validar tipo de arquivo e mover para o destino
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return $nomeArquivo;
    }

    return null; // Retorna null se o upload falhar
}

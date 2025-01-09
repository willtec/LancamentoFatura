<?php

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController
{
    /**
     * Exibe a lista de usuários, com suporte a busca.
     */
    public static function index()
    {
        $searchTerm = $_GET['search'] ?? '';
        if ($searchTerm) {
            $usuarios = Usuario::buscarPorTermo($searchTerm);
        } else {
            $usuarios = Usuario::listarTodos();
        }
        include __DIR__ . '/../views/usuarios/listar.php';
    }

    /**
     * Processa a criação de um novo usuário.
     */
    public static function criar()
    {
        $erro = ''; // Variável para armazenar mensagem de erro

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtenha os dados do formulário
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $nivelAcesso = $_POST['nivel_acesso'] ?? 'usuario_leitura'; // Define o padrão, se não enviado

            // Validação básica
            if (empty($nome) || empty($email) || empty($senha)) {
                $erro = "Todos os campos são obrigatórios.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = "O e-mail informado é inválido.";
            } else {
                // Tentar criar o usuário
                if (Usuario::criar($nome, $email, $senha, $nivelAcesso)) {
                    header('Location: /LancamentoFatura/usuarios');
                    exit();
                } else {
                    $erro = "Erro ao criar usuário. O e-mail ou nome já podem estar cadastrados.";
                }
            }
        }

        // Inclua a view de cadastro, passando a mensagem de erro, se houver
        include __DIR__ . '/../views/usuarios/cadastrar.php';
    }

    /**
     * Processa a edição de um usuário existente.
     */
    public static function editar($id)
    {
        $erro = ''; // Variável para armazenar mensagem de erro
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? null; // Senha pode ser nula
            $nivelAcesso = $_POST['nivel_acesso'] ?? 'usuario_leitura';

            // Validação básica
            if (empty($nome) || empty($email)) {
                $erro = "Nome e e-mail são obrigatórios.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = "O e-mail informado é inválido.";
            } else {
                // Se a senha estiver vazia, não a atualize
                if (empty($senha)) {
                    $senha = null;
                }

                // Atualizar usuário
                if (Usuario::atualizar($id, ['nome' => $nome, 'email' => $email, 'senha' => $senha, 'nivel_acesso' => $nivelAcesso], $_SESSION['usuario']['id'])) {
                    header('Location: /LancamentoFatura/usuarios');
                    exit();
                } else {
                    $erro = "Erro ao atualizar o usuário. Verifique os dados e tente novamente.";
                }
            }
        }

        // Buscar usuário para edição
        $usuario = Usuario::buscarPorId($id);
        include __DIR__ . '/../views/usuarios/editar.php';
    }

    /**
     * Processa a exclusão de um usuário.
     */
    public static function excluir($id)
    {
        if (Usuario::excluir($id)) {
            header('Location: /LancamentoFatura/usuarios');
        } else {
            echo "Erro ao excluir o usuário.";
        }
        exit();
    }

    /**
     * Verifica se o nome ou e-mail já estão cadastrados.
     */
    public static function verificar()
    {
        header('Content-Type: application/json');

        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';

        $resultado = [
            'nome_existe' => Usuario::verificarNome($nome),
            'email_existe' => Usuario::verificarEmail($email),
        ];

        echo json_encode($resultado);
        exit();
    }
}

<?php
require_once __DIR__ . '/../config/db.php';

class Usuario
{
    /**
     * Autentica um usuário com base no email e senha.
     *
     * @param string $email
     * @param string $senha
     * @return array|false Retorna os dados do usuário autenticado ou false em caso de falha.
     */
    public static function autenticar($email, $senha)
    {
        try {
            $pdo = getDBConnection();
            $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                error_log("Usuário não encontrado para o email: $email");
                return false;
            }

            if (!password_verify($senha, $usuario['senha'])) {
                error_log("Senha incorreta. Dados recebidos: Email - $email | Senha - $senha");
                error_log("Hash armazenado no banco: {$usuario['senha']}");
                return false;
            }

            // Atualizar o hash, se necessário
            if (password_needs_rehash($usuario['senha'], PASSWORD_BCRYPT)) {
                $novoHash = password_hash($senha, PASSWORD_BCRYPT);
                $updateQuery = "UPDATE usuarios SET senha = :senha WHERE id = :id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([':senha' => $novoHash, ':id' => $usuario['id']]);
                error_log("Hash de senha atualizado para o usuário com email: $email");
            }

            unset($usuario['senha']);
            return $usuario;
        } catch (PDOException $e) {
            error_log("Erro ao autenticar usuário: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Cria um novo usuário no sistema.
     *
     * @param string $nome
     * @param string $email
     * @param string $senha
     * @return bool Retorna true se o usuário for criado com sucesso, false caso contrário.
     */
    public static function criar($nome, $email, $senha)
    {
        try {
            $pdo = getDBConnection();
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

            $query = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param int $id
     * @param array $dados
     * @param int $usuarioId ID do usuário que está realizando a alteração.
     * @return bool Retorna true se o usuário foi atualizado com sucesso, false caso contrário.
     */
    public static function atualizar($id, $dados, $usuarioId)
    {
        try {
            $pdo = getDBConnection();

            $query = "
                UPDATE usuarios 
                SET nome = :nome, email = :email, senha = :senha, modificado_por = :modificado_por 
                WHERE id = :id
            ";
            $stmt = $pdo->prepare($query);

            $senhaHash = password_hash($dados['senha'], PASSWORD_BCRYPT);

            $stmt->execute([
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => $senhaHash,
                'modificado_por' => $usuarioId,
                'id' => $id,
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Busca a última atualização no sistema com o responsável.
     *
     * @return array|false Retorna um array com data, nome da tabela e o nome do usuário ou false em caso de erro.
     */
    public static function obterUltimaAtualizacaoComUsuario()
    {
        try {
            $pdo = getDBConnection();
            $query = "
                SELECT 
                    MAX(u.atualizado_em) AS data, 
                    'usuarios' AS tabela, 
                    COALESCE(
                        (SELECT nome FROM usuarios WHERE id = u.modificado_por), 
                        'Alterado diretamente no banco'
                    ) AS usuario
                FROM usuarios u
            ";
            $stmt = $pdo->query($query);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar última atualização com responsável: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Busca um usuário pelo ID.
     *
     * @param int $id
     * @return array|false Retorna os dados do usuário ou false se não encontrado.
     */
    public static function buscarPorId($id)
    {
        try {
            $pdo = getDBConnection();
            $query = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
        }

        return false;
    }
}

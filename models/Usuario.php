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

            // Adicionar logs para depuração
            if (!$usuario) {
                error_log("Usuário não encontrado para o email: $email");
                return false;
            }

            if (password_verify($senha, $hash)) {
                echo "Senha válida!";
            } else {
                echo "Senha inválida!";
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
            $pdo = getDBConnection(); // Obter conexão com o banco de dados

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

        return false; // Retorna false em caso de falha
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

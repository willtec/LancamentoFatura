<?php
require_once __DIR__ . '/../config/db.php';

class Transportadora
{
    // Método para listar todas as transportadoras
    public static function listar()
    {
        $pdo = getDBConnection();

        try {
            $stmt = $pdo->query("SELECT * FROM transportadora");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar transportadoras: " . $e->getMessage());
            return [];
        }
    }

    // Método para criar uma nova transportadora
    public static function criar($dados)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO transportadora (codigo, nome, cnpj) 
                VALUES (:codigo, :nome, :cnpj)
            ");

            return $stmt->execute([
                'codigo' => htmlspecialchars($dados['codigo']),
                'nome' => htmlspecialchars($dados['nome']),
                'cnpj' => htmlspecialchars($dados['cnpj']),
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar transportadora: " . $e->getMessage());
            return false;
        }
    }

    // Atualizar uma transportadora
    public static function atualizar($id, $dados)
    {
        try {
            $pdo = getDBConnection();

            $usuarioId = $_SESSION['usuario']['id'] ?? null;
            if (!$usuarioId) {
                error_log("Usuário não autenticado ao tentar atualizar transportadora.");
                return false;
            }

            $query = "
                UPDATE transportadora 
                SET codigo = :codigo, nome = :nome, cnpj = :cnpj, updated_at = NOW(), modificado_por = :modificado_por 
                WHERE id = :id
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'codigo' => htmlspecialchars($dados['codigo']),
                'nome' => htmlspecialchars($dados['nome']),
                'cnpj' => htmlspecialchars($dados['cnpj']),
                'modificado_por' => $usuarioId,
                'id' => $id,
            ]);

            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // Código SQL para violação de restrição UNIQUE
                error_log("Erro de duplicidade ao atualizar transportadora: " . $e->getMessage());
                setMensagem('erro', 'CNPJ já está em uso por outra transportadora.');
            } else {
                error_log("Erro ao atualizar transportadora: " . $e->getMessage());
                setMensagem('erro', 'Erro ao atualizar a transportadora. Tente novamente.');
            }
            return false;
        }
    }

    // exclui (oculta) uma transportadora
    public static function ocultar($id)
    {
        try {
            $pdo = getDBConnection(); // Conexão com o banco de dados
            $stmt = $pdo->prepare("UPDATE transportadora SET ativo = 0 WHERE id = :id");
            return $stmt->execute(['id' => $id]); // Atualiza o campo 'ativo' para 0
        } catch (PDOException $e) {
            error_log("Erro ao ocultar transportadora: " . $e->getMessage());
            return false; // Retorna false se houver erro
        }
    }

    // método ativar
    public static function ativar($id)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE transportadora SET ativo = 1 WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Erro ao ativar transportadora: " . $e->getMessage());
            return false;
        }
    }

    // Buscar uma transportadora pelo ID
    public static function buscarPorId($id)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM transportadora WHERE id = :id");
            $stmt->execute(['id' => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar transportadora por ID: " . $e->getMessage());
            return false;
        }
    }

    // Obter última atualização com usuário
    public static function obterUltimaAtualizacaoComUsuario()
    {
        try {
            $pdo = getDBConnection();
            $query = "
                SELECT
                    MAX(f.data_lancamento) AS data,
                    'faturas' AS tabela,
                    COALESCE(
                        (SELECT nome FROM usuarios WHERE id = f.modificado_por),
                        'Alterado diretamente no banco'
                    ) AS usuario
                FROM faturas f
            ";
            $stmt = $pdo->query($query);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar última atualização com responsável: " . $e->getMessage());
            return false;
        }
    }

    // Importar dados de CSV
    public static function importarCSV($arquivo)
    {
        $pdo = getDBConnection();

        try {
            $pdo->beginTransaction();
            $handle = fopen($arquivo, 'r');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $stmt = $pdo->prepare("
                    INSERT INTO transportadora (codigo, nome, cnpj) 
                    VALUES (:codigo, :nome, :cnpj)
                    ON DUPLICATE KEY UPDATE nome = VALUES(nome), cnpj = VALUES(cnpj)
                ");

                $stmt->execute([
                    'codigo' => htmlspecialchars($data[0]),
                    'nome' => htmlspecialchars($data[1]),
                    'cnpj' => htmlspecialchars($data[2]),
                ]);
            }

            fclose($handle);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Erro ao importar CSV: " . $e->getMessage());
            return false;
        }
    }

    // Contar transportadoras com ou sem termo de busca
    public static function contarTransportadoras($termo = '')
    {
        try {
            $pdo = getDBConnection();

            if (!empty($termo)) {
                $sql = "SELECT COUNT(*) as total FROM transportadora WHERE nome LIKE :termo OR cnpj LIKE :termo";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
            } else {
                $sql = "SELECT COUNT(*) as total FROM transportadora";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro ao contar transportadoras: " . $e->getMessage());
            return 0;
        }
    }

    // Listar transportadoras com paginação (exibe ativos e ocultos)
    public static function listarPaginado($limite, $offset, $termo = '')
    {
        try {
            $pdo = getDBConnection();

            if (!empty($termo)) {
                $sql = "SELECT * FROM transportadora WHERE (nome LIKE :termo OR cnpj LIKE :termo) LIMIT :limite OFFSET :offset";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
            } else {
                $sql = "SELECT * FROM transportadora LIMIT :limite OFFSET :offset";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar transportadoras com paginação: " . $e->getMessage());
            return [];
        }
    }

    // Buscar transportadoras por termo
    public static function buscarPorTermo($termo)
    {
        try {
            $pdo = getDBConnection();
            $sql = "SELECT id, codigo, nome FROM transportadora WHERE codigo LIKE :termo OR nome LIKE :termo LIMIT 10";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar transportadoras por termo: " . $e->getMessage());
            return [];
        }
    }

    public static function buscarPorCnpj($cnpj)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM transportadora WHERE cnpj = :cnpj LIMIT 1");
            $stmt->execute(['cnpj' => $cnpj]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar transportadora por CNPJ: " . $e->getMessage());
            return false;
        }
    }
}

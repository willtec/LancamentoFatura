<?php
require_once __DIR__ . '/../config/db.php';

class Transportadora
{
    // Método para listar todas as transportadoras
    public static function listar()
    {
        $pdo = getDBConnection();

        $stmt = $pdo->query("SELECT * FROM transportadora");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para criar uma nova transportadora
    public static function criar($dados)
    {
        $pdo = getDBConnection();

        $stmt = $pdo->prepare(
            "INSERT INTO transportadora (codigo, nome, cnpj) VALUES (:codigo, :nome, :cnpj)"
        );

        return $stmt->execute([
            'codigo' => htmlspecialchars($dados['codigo']),
            'nome' => htmlspecialchars($dados['nome']),
            'cnpj' => htmlspecialchars($dados['cnpj']),
        ]);
    }

    /**
     * Atualiza os dados de uma transportadora existente.
     *
     * @param int $id
     * @param array $dados
     * @param int $usuarioId ID do usuário que está realizando a alteração.
     * @return bool Retorna true se a transportadora foi atualizada com sucesso, false caso contrário.
     */
    public static function atualizar($id, $dados, $usuarioId)
    {
        try {
            $pdo = getDBConnection();

            $query = "
                UPDATE transportadora 
                SET nome = :nome, codigo = :codigo, cnpj = :cnpj, modificado_por = :modificado_por 
                WHERE id = :id
            ";
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                'nome' => $dados['nome'],
                'codigo' => $dados['codigo'],
                'cnpj' => $dados['cnpj'],
                'modificado_por' => $usuarioId,
                'id' => $id,
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar transportadora: " . $e->getMessage());
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
        }

        return false;
    }

    // Outros métodos já existentes permanecem sem alterações...
    public static function importarCSV($arquivo)
    {
        $pdo = getDBConnection();

        try {
            $pdo->beginTransaction();
            $handle = fopen($arquivo, 'r');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $stmt = $pdo->prepare(
                    "INSERT INTO transportadora (codigo, nome, cnpj) VALUES (:codigo, :nome, :cnpj)
                    ON DUPLICATE KEY UPDATE nome = VALUES(nome), cnpj = VALUES(cnpj)"
                );

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

    public static function contarTransportadoras($termo = '')
    {
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
    }

    public static function listarPaginado($limite, $offset, $termo = '')
    {
        $pdo = getDBConnection();

        if (!empty($termo)) {
            $sql = "SELECT * FROM transportadora WHERE nome LIKE :termo OR cnpj LIKE :termo LIMIT :limite OFFSET :offset";
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
    }

    public static function buscarPorTermo($termo)
    {
        $pdo = getDBConnection();

        $sql = "SELECT id, codigo, nome FROM transportadora
                WHERE codigo LIKE :termo OR nome LIKE :termo
                LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Endpoint para buscar transportadoras (autocomplete)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['termo'])) {
    $termo = $_GET['termo'];
    $resultado = Transportadora::buscarPorTermo($termo);
    echo json_encode($resultado);
    exit;
}

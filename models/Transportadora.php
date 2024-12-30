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
            $stmt = $pdo->prepare("INSERT INTO transportadora (codigo, nome, cnpj, modificado_por, ativo)
            VALUES (:codigo, :nome, :cnpj, :modificado_por, 1)");

            $resultado = $stmt->execute([
                'codigo' => $dados['codigo'],
                'nome' => $dados['nome'],
                'cnpj' => $dados['cnpj'],
                'modificado_por' => $_SESSION['usuario']['id'] ?? null,
            ]);

            error_log("Transportadora criada: " . json_encode($dados));
            return $resultado;
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

            $stmt = $pdo->prepare("
                UPDATE transportadora 
                SET codigo = :codigo, nome = :nome, cnpj = :cnpj, updated_at = NOW(), modificado_por = :modificado_por 
                WHERE id = :id
            ");
            $stmt->execute([
                'codigo' => $dados['codigo'],
                'nome' => $dados['nome'],
                'cnpj' => $dados['cnpj'],
                'modificado_por' => $usuarioId,
                'id' => $id,
            ]);

            error_log("Transportadora atualizada (ID: $id): " . json_encode($dados));
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // Código SQL para violação de restrição UNIQUE
                error_log("Erro de duplicidade ao atualizar transportadora: " . $e->getMessage());
                setMensagem('erro', 'CNPJ ou código já está em uso por outra transportadora.');
            } else {
                error_log("Erro ao atualizar transportadora: " . $e->getMessage());
                setMensagem('erro', 'Erro ao atualizar a transportadora. Tente novamente.');
            }
            return false;
        }
    }

    // Excluir (ocultar logicamente) uma transportadora
    public static function ocultar($id)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE transportadora SET ativo = 0 WHERE id = :id");
            $resultado = $stmt->execute(['id' => $id]);

            error_log("Transportadora ocultada (ID: $id)");
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao ocultar transportadora: " . $e->getMessage());
            return false;
        }
    }

    // Tornar visível uma transportadora
    public static function ativar($id)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE transportadora SET ativo = 1 WHERE id = :id");
            $resultado = $stmt->execute(['id' => $id]);

            error_log("Transportadora ativada (ID: $id)");
            return $resultado;
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

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
            error_log("Transportadora buscada (ID: $id): " . json_encode($resultado));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar transportadora por ID: " . $e->getMessage());
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

            if (!$handle) {
                throw new Exception('Não foi possível abrir o arquivo CSV.');
            }

            $headerLine = fgets($handle);
            $header = str_getcsv($headerLine, ',');
            error_log("Cabeçalho do CSV: " . json_encode($header));

            if (!$header || $header !== ['codigo', 'nome', 'cnpj']) {
                throw new Exception('Formato do arquivo CSV inválido. O cabeçalho deve ser: codigo, nome, cnpj.');
            }

            while (($line = fgets($handle)) !== false) {
                $data = str_getcsv($line, ',');
                if (count($data) !== 3) {
                    throw new Exception('Dados inválidos no arquivo CSV.');
                }

                $stmt = $pdo->prepare("
                    INSERT INTO transportadora (codigo, nome, cnpj, modificado_por, ativo) 
                    VALUES (:codigo, :nome, :cnpj, :modificado_por, 1)
                    ON DUPLICATE KEY UPDATE 
                        nome = VALUES(nome),
                        cnpj = VALUES(cnpj),
                        updated_at = NOW(),
                        modificado_por = :modificado_por
                ");
                $stmt->execute([
                    'codigo' => $data[0],
                    'nome' => $data[1],
                    'cnpj' => $data[2],
                    'modificado_por' => $_SESSION['usuario']['id'] ?? null,
                ]);
            }

            fclose($handle);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            if (isset($handle)) {
                fclose($handle);
            }
            $pdo->rollBack();
            error_log("Erro ao importar CSV: " . $e->getMessage());
            setMensagem('erro', $e->getMessage());
            return false;
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
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Transportadoras listadas: " . json_encode($resultado));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao listar transportadoras com paginação: " . $e->getMessage());
            return [];
        }
    }

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

            error_log("Total de transportadoras encontradas: " . $resultado['total']);
            return $resultado['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro ao contar transportadoras: " . $e->getMessage());
            return 0;
        }
    }

    public static function buscarPorCnpj($cnpj)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM transportadora WHERE cnpj = :cnpj LIMIT 1");
            $stmt->execute(['cnpj' => $cnpj]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
            error_log("Transportadora buscada por CNPJ ($cnpj): " . json_encode($resultado));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar transportadora por CNPJ: " . $e->getMessage());
            return false;
        }
    }
}

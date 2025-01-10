<?php
require_once __DIR__ . '/../config/db.php';

class Fatura
{
    // Método para listar todas as faturas
    public static function listarTodas()
    {
        error_log("Iniciando a listagem de todas as faturas.");

        try {
            $pdo = getDBConnection();
            $query = "
                SELECT f.*, t.nome AS transportadora
                FROM faturas f
                LEFT JOIN transportadora t ON f.transportadora_id = t.id
            ";
            $stmt = $pdo->query($query);
            $faturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Listagem concluída com sucesso. Total de faturas encontradas: " . count($faturas));
            return $faturas;
        } catch (PDOException $e) {
            error_log("Erro ao listar faturas: " . $e->getMessage());
            return false;
        }
    }

    // Método para criar uma nova fatura
    public static function cadastrar($dados)
    {
        error_log("Iniciando o cadastro de uma nova fatura.");
        error_log("Dados recebidos no modelo: " . print_r($dados, true));

        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            $boleto_path = $dados['boleto'] ?? null;
            $cte_path = $dados['arquivos_cte'] ?? null;

            $stmt = $pdo->prepare(
                "INSERT INTO faturas (transportadora_id, numero_fatura, vencimento, valor, boleto, arquivos_cte)
                VALUES (:transportadora_id, :numero_fatura, :vencimento, :valor, :boleto, :arquivos_cte)"
            );
            $stmt->execute([
                'transportadora_id' => $dados['transportadora_id'],
                'numero_fatura' => $dados['numero_fatura'],
                'vencimento' => $dados['vencimento'],
                'valor' => $dados['valor'],
                'boleto' => $boleto_path,
                'arquivos_cte' => $cte_path,
            ]);

            $pdo->commit();
            error_log("Fatura cadastrada com sucesso no banco de dados.");
            return true;
        } catch (Exception $e) {
            error_log("Erro ao cadastrar fatura: " . $e->getMessage());
            $pdo->rollBack();
            return false;
        }
    }

    // Método para buscar uma fatura pelo ID
    public static function buscarPorId($id)
    {
        error_log("Iniciando a busca por uma fatura. ID: $id");

        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare(
                "SELECT f.*, t.nome AS transportadora
                FROM faturas f
                LEFT JOIN transportadora t ON f.transportadora_id = t.id
                WHERE f.id = :id"
            );
            $stmt->execute(['id' => $id]);
            $fatura = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fatura) {
                error_log("Fatura encontrada: " . print_r($fatura, true));
            } else {
                error_log("Nenhuma fatura encontrada com o ID fornecido.");
            }

            return $fatura;
        } catch (PDOException $e) {
            error_log("Erro ao buscar fatura: " . $e->getMessage());
            return false;
        }
    }

    // Método para atualizar os dados de uma fatura existente
    public static function atualizar($id, $dados, $usuarioId)
    {
        error_log("Iniciando atualização da fatura. ID: $id");
        error_log("Dados recebidos para atualização: " . print_r($dados, true));

        try {
            $pdo = getDBConnection();

            $query = "
                UPDATE faturas
                SET
                    transportadora_id = :transportadora_id,
                    numero_fatura = :numero_fatura,
                    vencimento = :vencimento,
                    valor = :valor,
                    boleto = :boleto,
                    arquivos_cte = :arquivos_cte,
                    modificado_por = :modificado_por
                WHERE id = :id
            ";
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                'transportadora_id' => $dados['transportadora_id'],
                'numero_fatura' => $dados['numero_fatura'],
                'vencimento' => $dados['vencimento'],
                'valor' => $dados['valor'],
                'boleto' => $dados['boleto'] ?? null,
                'arquivos_cte' => $dados['arquivos_cte'] ?? null,
                'modificado_por' => $usuarioId,
                'id' => $id,
            ]);

            error_log("Fatura atualizada com sucesso. ID: $id");
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar fatura: " . $e->getMessage());
            return false;
        }
    }

    // Método para remover uma fatura do banco de dados
    public static function deletar($id)
    {
        error_log("Iniciando exclusão da fatura. ID: $id");

        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM faturas WHERE id = :id");
            $stmt->execute(['id' => $id]);

            error_log("Fatura excluída com sucesso. ID: $id");
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao deletar fatura: " . $e->getMessage());
            return false;
        }
    }

    // Método para obter a última atualização das faturas com dados do usuário
    public static function obterUltimaAtualizacaoComUsuario()
    {
        error_log("Buscando a última atualização de faturas com informações do usuário.");

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

            if ($resultado) {
                error_log("Última atualização encontrada: " . print_r($resultado, true));
            } else {
                error_log("Nenhuma atualização encontrada.");
            }

            return $resultado ?: false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar última atualização de faturas com responsável: " . $e->getMessage());
        }

        return false;
    }
}

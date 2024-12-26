<?php
require_once __DIR__ . '/../config/db.php';

class Fatura
{
    // Método para listar todas as faturas
    public static function listarTodas()
    {
        try {
            $pdo = getDBConnection();
            $query = "
                SELECT f.*, t.nome AS transportadora
                FROM faturas f
                LEFT JOIN transportadora t ON f.transportadora_id = t.id
            ";
            $stmt = $pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar faturas: " . $e->getMessage());
            return false;
        }
    }

    // Método para criar uma nova fatura
    public static function cadastrar($dados)
    {
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
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar fatura: " . $e->getMessage());
            $pdo->rollBack();
            return false;
        }
    }

    // Método para buscar uma fatura pelo ID
    public static function buscarPorId($id)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare(
                "SELECT f.*, t.nome AS transportadora
                FROM faturas f
                LEFT JOIN transportadora t ON f.transportadora_id = t.id
                WHERE f.id = :id"
            );
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar fatura: " . $e->getMessage());
            return false;
        }
    }

    // Método para atualizar os dados de uma fatura existente
    public static function atualizar($id, $dados, $usuarioId)
    {
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

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar fatura: " . $e->getMessage());
        }

        return false;
    }

    // Método para remover uma fatura do banco de dados
    public static function deletar($id)
    {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM faturas WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao deletar fatura: " . $e->getMessage());
            return false;
        }
    }

    // Método para obter a última atualização das faturas com dados do usuário
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
            error_log("Erro ao buscar última atualização de faturas com responsável: " . $e->getMessage());
        }

        return false;
    }
}

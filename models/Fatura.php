<?php
require_once(__DIR__ . '/../config/db.php');

class Fatura {
    /**
     * Lista todas as faturas.
     *
     * @return array|false Retorna um array com todas as faturas ou false em caso de erro.
     */
    public static function listarTodas() {
        try {
            $pdo = getDBConnection(); // Obter conexão com o banco de dados
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

    /**
     * Cria uma nova fatura.
     *
     * @param array $dados Dados da fatura.
     * @param array $boleto Arquivo do boleto.
     * @param array $arquivos_cte Arquivo(s) do CTe.
     * @return bool Retorna true se a fatura foi criada com sucesso, false em caso de erro.
     */
    public static function cadastrar($dados) {
        try {
            $pdo = getDBConnection(); // Obter conexão com o banco de dados
            $pdo->beginTransaction();

            // Processar upload dos arquivos
            $numero_fatura = $dados['numero_fatura'];
            $boleto_path = UPLOAD_PDFS . $numero_fatura . '.pdf';
            $cte_path = UPLOAD_XMLS . $numero_fatura . '.zip';

            if (!move_uploaded_file($dados['boleto']['tmp_name'], $boleto_path)) {
                throw new Exception("Erro ao mover o arquivo do boleto.");
            }
            if (!move_uploaded_file($dados['arquivos_cte']['tmp_name'], $cte_path)) {
                throw new Exception("Erro ao mover os arquivos de CTe.");
            }

            // Inserir a fatura no banco de dados
            $stmt = $pdo->prepare("
                INSERT INTO faturas (transportadora_id, numero_fatura, vencimento, valor, boleto, arquivos_cte)
                VALUES (:transportadora_id, :numero_fatura, :vencimento, :valor, :boleto, :arquivos_cte)
            ");
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
}
?>

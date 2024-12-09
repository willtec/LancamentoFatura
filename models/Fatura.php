<?php
require_once '../config/db.php';

class Fatura {
    // Método para listar todas as faturas
    public static function listar() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT f.*, t.nome AS transportadora 
            FROM faturas f
            LEFT JOIN transportadora t ON f.transportadora_id = t.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para criar uma nova fatura
    public static function criar($dados, $boleto, $arquivos_cte) {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // Processar upload dos arquivos
            $numero_fatura = $dados['numero_fatura'];
            $boleto_path = UPLOAD_PDFS . $numero_fatura . '.pdf';
            $cte_path = UPLOAD_XMLS . $numero_fatura . '.zip';

            move_uploaded_file($boleto['tmp_name'], $boleto_path);
            move_uploaded_file($arquivos_cte['tmp_name'], $cte_path);

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
            $pdo->rollBack();
            return false;
        }
    }
}
?>

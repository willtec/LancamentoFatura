<?php
require_once '../config/db.php';

class Transportadora {
    // Método para listar todas as transportadoras
    public static function listar() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM transportadora");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para criar uma nova transportadora
    public static function criar($dados) {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO transportadora (codigo, nome, cnpj)
            VALUES (:codigo, :nome, :cnpj)
        ");
        return $stmt->execute([
            'codigo' => $dados['codigo'],
            'nome' => $dados['nome'],
            'cnpj' => $dados['cnpj'],
        ]);
    }

    // Método para importar transportadoras via arquivo CSV
    public static function importarCSV($arquivo) {
        global $pdo;

        try {
            $pdo->beginTransaction();
            $handle = fopen($arquivo, 'r');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $stmt = $pdo->prepare("
                    INSERT INTO transportadora (codigo, nome, cnpj)
                    VALUES (:codigo, :nome, :cnpj)
                    ON DUPLICATE KEY UPDATE nome = :nome, cnpj = :cnpj
                ");
                $stmt->execute([
                    'codigo' => $data[0],
                    'nome' => $data[1],
                    'cnpj' => $data[2],
                ]);
            }

            fclose($handle);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
?>

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

    // Método para importar transportadoras via arquivo CSV
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

    // Método para contar transportadoras com ou sem filtro
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

        return $resultado['total'];
    }

    // Método para listar transportadoras com paginação
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

    // Método para cadastrar uma transportadora (compatível com o controller)
    public static function cadastrar($dados)
    {
        return self::criar($dados);
    }
}

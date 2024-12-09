<?php
require_once '../config/db.php';

class Usuario {
    // Método para autenticar o usuário com email e senha
    public static function autenticar($email, $senha) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return null;
    }
}
?>

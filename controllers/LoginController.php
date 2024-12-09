<?php
require_once '../models/Usuario.php';
require_once '../config/helpers.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Autenticação
    $usuario = Usuario::autenticar($email, $senha);

    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
        redirecionar('../views/dashboard');
    } else {
        setMensagem('erro', 'Email ou senha incorretos.');
        include '../views/login.php';
    }
} else {
    include '../views/login.php';
}
?>

<?php
require_once '../models/Transportadora.php';
require_once '../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $transportadoras = Transportadora::listar();
    include '../views/transportadoras/listar.php';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = $_POST;

    if (isset($_FILES['csv_import'])) {
        $arquivo = $_FILES['csv_import'];

        if ($arquivo['type'] === 'text/csv') {
            $resultado = Transportadora::importarCSV($arquivo['tmp_name']);

            if ($resultado) {
                setMensagem('sucesso', 'Transportadoras importadas com sucesso.');
                redirecionar('/transportadoras');
            } else {
                setMensagem('erro', 'Erro ao importar CSV.');
            }
        } else {
            setMensagem('erro', 'O arquivo enviado não é um CSV válido.');
        }
    } else {
        $resultado = Transportadora::criar($dados);

        if ($resultado) {
            setMensagem('sucesso', 'Transportadora criada com sucesso.');
            redirecionar('/transportadoras');
        } else {
            setMensagem('erro', 'Erro ao criar transportadora.');
        }
    }

    include '../views/transportadoras/cadastrar.php';
} else {
    http_response_code(405);
    echo "Método não permitido.";
}
?>

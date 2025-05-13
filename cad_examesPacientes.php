<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codPaciente = $_POST['paciente'];
    $codExame = $_POST['exame'];
    $dataPedido = $_POST['dataPedido'];
    $dataResultado = $_POST['dataResultado'];
    $dataEntrega = $_POST['dataEntrega'];
    $situacao = $_POST['situacao'];


    $sql = "INSERT INTO PiCadExamesRealizados (codPaciente, codExame, dataPedido, dataResultado, dataEntrega, situacao) VALUES ('$codPaciente', '$codExame','$dataPedido','$dataResultado','$dataEntrega','$situacao')";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Cadastro Realizado com Sucesso!"); window.location.href = "status_exames.php";</script>';
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }

    $conn->close();
}
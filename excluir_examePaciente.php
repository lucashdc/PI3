<?php
require_once 'db_connection.php';

if (isset($_GET['codigo'])) {
    $statusId = $_GET['codigo'];


      $sql = "DELETE FROM PiCadExamesRealizados WHERE codigo = $statusId";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Exame Excluido com Sucesso');</script>";
        echo "<script>window.location.href = 'status_exames.php';</script>";
        exit;
    } else {
        echo "Erro ao excluir o Paciente: " . $conn->error;
    }
} else {
    echo "Código do Paciente não especificado.";
}

$conn->close();

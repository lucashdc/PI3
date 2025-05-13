<?php
require_once 'db_connection.php';

if (isset($_GET['codigo'])) {
    $pacienteCod = $_GET['codigo'];

    // Verificar se o exame está sendo referenciado por outras tabelas
    $sqlCheck = "SELECT COUNT(*) AS count FROM PiCadExamesRealizados WHERE codPaciente = $pacienteCod";
    $resultCheck = $conn->query($sqlCheck);
    $row = $resultCheck->fetch_assoc();
    $count = $row['count'];

    if ($count > 0) {
        echo "<script>alert('O Paciente não pode ser excluído pois está associado a exames.');</script>";
        echo "<script>window.location.href = 'lista_pacientes.php';</script>";
        exit;
    }

    // Se não houver referências, exclua o exame
    $sql = "DELETE FROM PICadPacientes WHERE codigo = $pacienteCod";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Paciente Excluído com Sucesso');</script>";
        echo "<script>window.location.href = 'lista_pacientes.php';</script>";
        exit;
    } else {
        echo "Erro ao excluir o Paciente: " . $conn->error;
    }
} else {
    echo "Código do Paciente não especificado.";
}

$conn->close();

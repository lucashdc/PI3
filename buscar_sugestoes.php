<?php
require_once 'db_connection.php';

if (isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $sql = "SELECT codigo, nome, prontuario FROM PICadPacientes WHERE nome LIKE ? OR prontuario LIKE ?";
    $stmt = $conn->prepare($sql);
    $buscar = "%" . $nome . "%";
    $stmt->bind_param("ss", $buscar, $buscar);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($row = $resultado->fetch_assoc()) {
        echo "<li data-id='" . $row['codigo'] . "'>" . $row['codigo'] . " - " . $row['nome'] . "<b>" . " - " . $row['prontuario'] . "</b>" . "</li>";
    }
    $stmt->close();
    $conn->close();
}
?>

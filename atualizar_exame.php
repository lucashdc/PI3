<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'];
    $situacao = $_POST['situacao'];
    $dataEntrega = $_POST['dataEntrega'];

    $sql = "UPDATE PiCadExamesRealizados SET situacao='$situacao', dataEntrega='$dataEntrega' WHERE codigo = $codigo";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Exame atualizado com sucesso!"); window.location.href = "status_exames.php";</script>';
    } else {
        echo "Erro ao atualizar exame: " . $conn->error;
    }

    $conn->close();
}
?>
<?php

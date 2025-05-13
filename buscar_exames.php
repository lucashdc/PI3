<?php
require_once 'db_connection.php';

if(isset($_POST['exame'])){
    $exame = $_POST['exame'];
    $sql = "SELECT codigo, exame FROM PICadExames WHERE exame LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $exame . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while($row = $resultado->fetch_assoc()){
        echo "<li data-id='".$row['codigo']."'>". $row['exame'] ."</li>";
    }
    $stmt->close();
    $conn->close();
}
?>

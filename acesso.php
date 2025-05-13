<?php

require_once 'db_connection.php';

session_start();

$error = '';

if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];


    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }


    $sql = "SELECT codigo FROM PICadUsuarios WHERE usuario = '$usuario' AND senha = '$senha'";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];


        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario'] = $usuario;


        header("Location: inicio.php");
        exit();
    } else {
        $error = "Usuário ou senha incorretos.";
    }

    $conn->close();
} else {
    $error = "Por favor, preencha todos os campos.";
}

if ($error) {
    echo "<script>
            alert('$error');
            window.location.href = 'index.html';
          </script>";
}

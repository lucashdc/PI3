<?php
require_once 'db_connection.php';

if (isset($_GET['codigo'])) {
    $codExame = $_GET['codigo'];

    $sql = "SELECT * FROM PICadExames WHERE codigo = $codExame";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $exame = $result->fetch_assoc();
    } else {
        echo "Exame não encontrado.";
        exit;
    }
} else {
    echo "ID do exame não especificado.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novoNome = $_POST['exame'];

    $sql = "UPDATE PICadExames SET exame = '$novoNome' WHERE codigo = $codExame";

    if ($conn->query($sql) === TRUE) {
        header("Location: listar_exames.php");
        exit;
    } else {
        echo "Erro ao atualizar o exame: " . $conn->error;
    }
}

$conn->close();
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Exames</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos de layout */
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }

        /* Estilo para centralizar o formulário */
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Estilo do card */
        .box {
            background-color: #fff;
            padding: 30px;
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo para o título */
        .box .title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }

        /* Ajuste de margem e preenchimento do formulário */
        .box form .form-label {
            font-weight: 500;
            color: #495057;
        }

        /* Estilos para os botões */
        .btn-primary {
            width: 100%;
        }

        /* Nav Bar */
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="d-flex justify-content-between w-100">
            <div class="navbar-brand"> Sistema</div>
            <div class="collapse navbar-collapse justify-content-center" id="navbarCenteredExample">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            Pacientes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="cad_pacientes.html">Cadastrar Pacientes</a></li>
                            <li><a class="dropdown-item" href="lista_pacientes.php">Listar Pacientes</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            Exames
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="cad_exame.html">Cadastro de Exames</a></li>
                            <li><a class="dropdown-item" href="listar_exames.php">Lista de Exames</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            Regulação
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="exames_pacientes.php">Vincular Exames a Pacientes</a></li>
                            <li><a class="dropdown-item" href="status_exames.php">Status de Exames Relizados</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div>
                <button class="btn btn-outline-light" type="button">Sair</button>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="box">
        <h1 class="title">Alteração de Exames</h1>
        <form class="row g-3" action="" method="POST">
            <div class="col-md-12">
                <label for="exame" class="form-label">Nome do Exame</label>
                <input type="text" class="form-control" id="exame" name="exame" value="<?php echo $exame['exame']; ?>" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

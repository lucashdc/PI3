<?php
require_once 'db_connection.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exame = $_POST["exame"];
    $sql = "INSERT INTO PICadExames (exame) VALUES ('$exame')";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Exame cadastrado com sucesso!"); window.location.href = "listar_exames.php";</script>';
    } else {
        echo "Erro ao cadastrar o exame: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Exames - Sistema de Gestão Hospitalar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --bg-gradient: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 2rem 0;
        }

        .header-section {
            background: var(--bg-gradient);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            color: white;
            text-align: center;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header-section h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .header-section p {
            font-size: 1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }

        .form-container {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin-bottom: 2rem;
        }

        .form-header {
            background-color: #198754;
            color: white;
            padding: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .form-header i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .form-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            border-color: #0b5ed7;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5c636a;
            border-color: #5c636a;
            transform: translateY(-2px);
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

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
    <div class="container">
        <a class="navbar-brand" href="inicio.php">
            <i class="fas fa-hospital-alt me-2"></i>
            Sistema de Gestão Hospitalar
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="inicio.php">
                        <i class="fas fa-home me-1"></i> Início
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-1"></i> Pacientes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cad_pacientes.php">Cadastrar Pacientes</a></li>
                        <li><a class="dropdown-item" href="lista_pacientes.php">Listar Pacientes</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-vials me-1"></i> Exames
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item active" href="cad_exame2.php">Cadastro de Exames</a></li>
                        <li><a class="dropdown-item" href="listar_exames.php">Lista de Exames</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-clipboard-check me-1"></i> Regulação
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="exames_pacientes.php">Vincular Exames a Pacientes</a></li>
                        <li><a class="dropdown-item" href="status_exames.php">Status de Exames Realizados</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php">Relatórios</a></li>
                        <li><a class="dropdown-item" href="analise_dados.php">Análise de Dados</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.html">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="header-section">
    <div class="container">
        <h1>Cadastro de Exames</h1>
        <p>Adicione novos tipos de exames ao sistema</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <i class="fas fa-vials"></i> Informações do Exame
            </div>
            <div class="form-body">
                <form class="row g-3" action="" method="POST">
                    <div class="col-12">
                        <label for="exame" class="form-label">Nome do Exame</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-flask"></i></span>
                            <input type="text" class="form-control" id="exame" name="exame" placeholder="Digite o nome do exame" required>
                        </div>
                        <div class="form-text">Insira o nome completo do exame conforme ele deve aparecer no sistema.</div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i> Salvar
                            </button>
                            <button type="reset" class="btn btn-secondary btn-lg px-5">
                                <i class="fas fa-undo me-2"></i> Limpar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mb-4">
            <a href="listar_exames.php" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i> Ver Lista de Exames
            </a>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; <?php echo date('Y'); ?> Sistema de Gestão Hospitalar | Todos os direitos reservados</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Exames - Sistema de Gestão Hospitalar</title>
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

        .content-container {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin-bottom: 2rem;
        }

        .content-header {
            background-color: #198754;
            color: white;
            padding: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .content-header i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .content-body {
            padding: 1.5rem;
        }

        .search-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-top: none;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(25, 135, 84, 0.05);
        }

        .btn-action {
            margin-right: 5px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-warning {
            color: white;
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

        .badge {
            padding: 0.5rem 0.8rem;
            border-radius: 50rem;
            font-weight: 500;
            font-size: 0.8rem;
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
                        <li><a class="dropdown-item" href="cad_exame2.php">Cadastro de Exames</a></li>
                        <li><a class="dropdown-item active" href="listar_exames.php">Lista de Exames</a></li>
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
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="header-section">
    <div class="container">
        <h1>Lista de Exames</h1>
        <p>Visualize, pesquise e gerencie todos os exames cadastrados no sistema</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="content-container">
            <div class="content-header">
                <i class="fas fa-vials"></i> Gerenciamento de Exames
            </div>
            <div class="content-body">
                <div class="search-box">
                    <form class="row g-2 align-items-center" method="POST" action="">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="inp_busca" name="busca" placeholder="Digite o nome do exame para pesquisar">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="listar_exames.php" class="btn btn-secondary">
                                <i class="fas fa-redo-alt me-1"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Exame</th>
                            <th class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        require_once 'db_connection.php';

                        $sql = "SELECT * FROM PICadExames";

                        if (!empty($_POST['busca'])) {
                            $busca = $_POST['busca'];
                            $sql .= " WHERE exame LIKE '%$busca%'";
                        }

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['codigo'] . "</td>";
                                echo "<td>" . $row['exame'] . "</td>";
                                echo "<td class='text-center'>
                                    <a href='alterar_exame.php?codigo=" . $row['codigo'] . "' class='btn btn-warning btn-sm btn-action'>
                                        <i class='fas fa-edit me-1'></i> Editar
                                    </a>
                                    <a href='excluir_exame.php?codigo=" . $row['codigo'] . "' class='btn btn-danger btn-sm btn-action'
                                    onclick=\"return confirm('Tem certeza que deseja excluir este exame?');\">
                                        <i class='fas fa-trash-alt me-1'></i> Excluir
                                    </a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center py-4'>
                                <div class='alert alert-info mb-0'>
                                    <i class='fas fa-info-circle me-2'></i> Nenhum exame encontrado.
                                </div>
                            </td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <?php if (isset($result) && $result->num_rows > 0): ?>
                            <span class="badge bg-success">
                            <i class="fas fa-vials me-1"></i> Total: <?= $result->num_rows ?> exames
                        </span>
                        <?php endif; ?>
                    </div>
                    <a href="cad_exame.html" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Novo Exame
                    </a>
                </div>
            </div>
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

<?php $conn->close(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

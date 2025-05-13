<?php
require_once 'db_connection.php';

$totalPacientes = $conn->query("SELECT COUNT(*) as total FROM PICadPacientes")->fetch_assoc()['total'];
$totalExames = $conn->query("SELECT COUNT(*) as total FROM PICadExames")->fetch_assoc()['total'];
$totalExamesRealizados = $conn->query("SELECT COUNT(*) as total FROM PiCadExamesRealizados")->fetch_assoc()['total'];

$examesPendentes = $conn->query("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE situacao = 'Aguardando' OR situacao IS NULL")->fetch_assoc()['total'];

$examesProntos = $conn->query("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE situacao = 'Pronto'")->fetch_assoc()['total'];

$examesEntregues = $conn->query("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE situacao = 'Entregue'")->fetch_assoc()['total'];

$ultimosExames = [];
$resultUltimosExames = $conn->query("
    SELECT 
        p.nome AS nome_paciente,
        e.exame,
        er.dataPedido,
        er.situacao
    FROM PiCadExamesRealizados er
    JOIN PICadPacientes p ON er.codPaciente = p.codigo
    JOIN PICadExames e ON er.codExame = e.codigo
    ORDER BY er.dataPedido DESC
    LIMIT 5
");

if ($resultUltimosExames) {
    while ($row = $resultUltimosExames->fetch_assoc()) {
        $ultimosExames[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão Hospitalar</title>
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
            padding: 3rem 0;
        }

        .header-section {
            background: var(--bg-gradient);
            padding: 2.5rem 0;
            margin-bottom: 3rem;
            color: white;
            text-align: center;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header-section h1 {
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .header-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }

        .menu-card {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .menu-card-header {
            padding: 1.5rem;
            text-align: center;
            color: white;
        }

        .menu-card-body {
            padding: 1.5rem;
            flex: 1;
        }

        .menu-card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: #495057;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .menu-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
            padding-left: 1.5rem;
        }

        .menu-link i {
            margin-right: 1rem;
            width: 24px;
            font-size: 1.1rem;
            text-align: center;
        }

        .card-pacientes .menu-card-header { background-color: #0d6efd; }
        .card-exames .menu-card-header { background-color: #198754; }
        .card-regulacao .menu-card-header { background-color: #dc3545; }
        .card-dashboard .menu-card-header { background-color: #6f42c1; }

        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .stat-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .stat-content p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .stat-1 .stat-icon { background: linear-gradient(45deg, #0d6efd, #0a58ca); }
        .stat-2 .stat-icon { background: linear-gradient(45deg, #198754, #146c43); }
        .stat-3 .stat-icon { background: linear-gradient(45deg, #dc3545, #b02a37); }
        .stat-4 .stat-icon { background: linear-gradient(45deg, #fd7e14, #ca6510); }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

        .recent-activity {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-top: 3rem;
        }

        .recent-activity h3 {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            color: #343a40;
            font-weight: 600;
        }

        .activity-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f1f1;
        }

        .activity-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .activity-icon i {
            color: white;
            font-size: 1rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 0.2rem;
            color: #343a40;
        }

        .activity-subtitle {
            color: #6c757d;
            font-size: 0.85rem;
            margin: 0;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #adb5bd;
            margin-left: 1rem;
            white-space: nowrap;
        }

        .status-aguardando { background-color: #ffc107; }
        .status-pronto { background-color: #28a745; }
        .status-entregue { background-color: #0d6efd; }

        @media (max-width: 992px) {
            .menu-section {
                margin-bottom: 2rem;
            }
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
                    <a class="nav-link active" href="inicio.php">
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
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-vials me-1"></i> Exames
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cad_exame2.php">Cadastro de Exames</a></li>
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
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-chart-line me-1"></i> Dashboard
                    </a>
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
        <h1>Sistema de Gestão Hospitalar</h1>
        <p>Gerencie pacientes, exames e processos hospitalares com eficiência e agilidade</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="stat-box stat-1">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalPacientes; ?></h3>
                        <p>PACIENTES</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="stat-box stat-2">
                    <div class="stat-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalExames; ?></h3>
                        <p>TIPOS DE EXAMES</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="stat-box stat-3">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $examesPendentes; ?></h3>
                        <p>EXAMES PENDENTES</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-4">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalExamesRealizados; ?></h3>
                        <p>EXAMES REALIZADOS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 menu-section mb-4">
                <div class="menu-card card-pacientes">
                    <div class="menu-card-header">
                        <i class="fas fa-user-plus menu-card-icon"></i>
                        <h3>Pacientes</h3>
                    </div>
                    <div class="menu-card-body">
                        <a href="cad_pacientes.html" class="menu-link">
                            <i class="fas fa-user-plus"></i>
                            Cadastrar Paciente
                        </a>
                        <a href="lista_pacientes.php" class="menu-link">
                            <i class="fas fa-users"></i>
                            Listar Pacientes
                        </a>
                        <p class="mt-3 text-muted">Gerencie o cadastro de pacientes e consulte informações detalhadas sobre sua localização.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 menu-section mb-4">
                <div class="menu-card card-exames">
                    <div class="menu-card-header">
                        <i class="fas fa-vials menu-card-icon"></i>
                        <h3>Exames</h3>
                    </div>
                    <div class="menu-card-body">
                        <a href="cad_exame2.php" class="menu-link">
                            <i class="fas fa-plus-circle"></i>
                            Cadastrar Exames
                        </a>
                        <a href="listar_exames.php" class="menu-link">
                            <i class="fas fa-list-alt"></i>
                            Listar Exames
                        </a>
                        <p class="mt-3 text-muted">Adicione novos tipos de exames ao sistema e consulte os já cadastrados para manter seu catálogo atualizado.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 menu-section mb-4">
                <div class="menu-card card-regulacao">
                    <div class="menu-card-header">
                        <i class="fas fa-clipboard-check menu-card-icon"></i>
                        <h3>Regulação</h3>
                    </div>
                    <div class="menu-card-body">
                        <a href="exames_pacientes.php" class="menu-link">
                            <i class="fas fa-link"></i>
                            Vincular Exames
                        </a>
                        <a href="status_exames.php" class="menu-link">
                            <i class="fas fa-tasks"></i>
                            Status de Exames
                        </a>
                        <p class="mt-3 text-muted">Controle o ciclo de vida dos exames, desde a solicitação até a entrega do resultado aos pacientes.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 menu-section mb-4">
                <div class="menu-card card-dashboard">
                    <div class="menu-card-header">
                        <i class="fas fa-chart-line menu-card-icon"></i>
                        <h3>Dashboard</h3>
                    </div>
                    <div class="menu-card-body">
                        <a href="dashboard.php" class="menu-link">
                            <i class="fas fa-chart-bar"></i>
                            Análise de Dados
                        </a>
                        <a href="dashboard.php" class="menu-link">
                            <i class="fas fa-chart-pie"></i>
                            Visualizar Relatórios
                        </a>
                        <p class="mt-3 text-muted">Acompanhe estatísticas e indicadores de desempenho da sua unidade de saúde de forma visual e intuitiva.</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($ultimosExames)): ?>
            <div class="recent-activity">
                <h3><i class="fas fa-history me-2"></i> Atividade Recente</h3>

                <?php foreach ($ultimosExames as $exame): ?>
                    <?php
                    $statusClass = '';
                    $statusIcon = '';

                    if ($exame['situacao'] == 'Aguardando' || empty($exame['situacao'])) {
                        $statusClass = 'status-aguardando';
                        $statusIcon = 'clock';
                    } elseif ($exame['situacao'] == 'Pronto') {
                        $statusClass = 'status-pronto';
                        $statusIcon = 'check-circle';
                    } elseif ($exame['situacao'] == 'Entregue') {
                        $statusClass = 'status-entregue';
                        $statusIcon = 'paper-plane';
                    }

                    // Formatação da data
                    $dataFormatada = !empty($exame['dataPedido']) ? date('d/m/Y', strtotime($exame['dataPedido'])) : 'Data não definida';
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon <?php echo $statusClass; ?>">
                            <i class="fas fa-<?php echo $statusIcon; ?>"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($exame['exame']); ?></div>
                            <div class="activity-subtitle">
                                Paciente: <?php echo htmlspecialchars($exame['nome_paciente']); ?> |
                                Status: <?php echo htmlspecialchars($exame['situacao'] ?: 'Aguardando'); ?>
                            </div>
                        </div>
                        <div class="activity-time"><?php echo $dataFormatada; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
<?php
require_once 'db_connection.php';

$dataAtual = date('Y-m-d');
$anoAtual = date('Y');
$mesAtual = date('m');

$periodoSelecionado = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes';

$dataInicio = '';
switch ($periodoSelecionado) {
    case 'dia':
        $dataInicio = $dataAtual;
        break;
    case 'semana':
        $dataInicio = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'mes':
        $dataInicio = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'trimestre':
        $dataInicio = date('Y-m-d', strtotime('-90 days'));
        break;
    case 'ano':
        $dataInicio = date('Y-m-d', strtotime('-365 days'));
        break;
    case 'todos':
    default:
        $dataInicio = '1900-01-01'; // Data muito antiga para incluir todos os registros
        break;
}

function obterExamesMaisRealizados($conn, $dataInicio, $limite = 10) {
    $sql = "SELECT e.exame, COUNT(er.codigo) AS total 
            FROM PiCadExamesRealizados er
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido >= ?
            GROUP BY er.codExame
            ORDER BY total DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $dataInicio, $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $exames = [];
    $totais = [];

    while ($linha = $resultado->fetch_assoc()) {
        $exames[] = $linha['exame'];
        $totais[] = $linha['total'];
    }

    return [
        'exames' => $exames,
        'totais' => $totais
    ];
}

function obterMediaIdade($conn) {
    $sql = "SELECT 
                AVG(TIMESTAMPDIFF(YEAR, dataNasc, CURDATE())) AS media_idade,
                CASE 
                    WHEN genero = 'M' THEN 'Masculino'
                    WHEN genero = 'F' THEN 'Feminino'
                    ELSE 'Outro'
                END AS genero,
                COUNT(*) as total
            FROM PICadPacientes
            GROUP BY genero";

    $resultado = $conn->query($sql);

    $generos = [];
    $medias = [];
    $totais = [];

    while ($linha = $resultado->fetch_assoc()) {
        $generos[] = $linha['genero'] . ' (' . $linha['total'] . ')';
        $medias[] = round($linha['media_idade'], 1);
        $totais[] = $linha['total'];
    }

    return [
        'generos' => $generos,
        'medias' => $medias,
        'totais' => $totais
    ];
}

function obterFluxoPorMes($conn, $dataInicio) {
    $sql = "SELECT 
                MONTH(dataPedido) AS mes,
                COUNT(*) AS total
            FROM PiCadExamesRealizados
            WHERE dataPedido IS NOT NULL AND dataPedido >= ?
            GROUP BY MONTH(dataPedido)
            ORDER BY MONTH(dataPedido)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dataInicio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $dados = array_fill(0, 12, 0); // Inicializa array com zeros para 12 meses

    while ($linha = $resultado->fetch_assoc()) {
        $indice_mes = $linha['mes'] - 1; // Mês em SQL começa em 1, array em 0
        $dados[$indice_mes] = $linha['total'];
    }

    return [
        'meses' => $meses,
        'dados' => $dados
    ];
}

function obterSituacaoExames($conn, $dataInicio) {
    $sql = "SELECT 
                situacao,
                COUNT(*) AS total
            FROM PiCadExamesRealizados
            WHERE dataPedido >= ?
            GROUP BY situacao";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dataInicio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $situacoes = [];
    $totais = [];
    $cores = ['#28a745', '#ffc107', '#dc3545'];

    while ($linha = $resultado->fetch_assoc()) {
        $situacoes[] = $linha['situacao'] ?: 'Não definido';
        $totais[] = $linha['total'];
    }

    return [
        'situacoes' => $situacoes,
        'totais' => $totais,
        'cores' => $cores
    ];
}

function obterDistribuicaoEtaria($conn) {
    $sql = "SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, dataNasc, CURDATE()) < 18 THEN 'Menor de 18'
                    WHEN TIMESTAMPDIFF(YEAR, dataNasc, CURDATE()) BETWEEN 18 AND 30 THEN '18-30'
                    WHEN TIMESTAMPDIFF(YEAR, dataNasc, CURDATE()) BETWEEN 31 AND 45 THEN '31-45'
                    WHEN TIMESTAMPDIFF(YEAR, dataNasc, CURDATE()) BETWEEN 46 AND 60 THEN '46-60'
                    ELSE 'Mais de 60'
                END AS faixa_etaria,
                COUNT(*) AS total
            FROM PICadPacientes
            GROUP BY faixa_etaria
            ORDER BY 
                CASE faixa_etaria
                    WHEN 'Menor de 18' THEN 1
                    WHEN '18-30' THEN 2
                    WHEN '31-45' THEN 3
                    WHEN '46-60' THEN 4
                    WHEN 'Mais de 60' THEN 5
                END";

    $resultado = $conn->query($sql);

    $faixas = [];
    $totais = [];

    while ($linha = $resultado->fetch_assoc()) {
        $faixas[] = $linha['faixa_etaria'];
        $totais[] = $linha['total'];
    }

    return [
        'faixas' => $faixas,
        'totais' => $totais
    ];
}

function obterExamesPorUnidade($conn, $dataInicio) {
    $sql = "SELECT 
                p.unidadeSaude,
                COUNT(*) AS total
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            WHERE er.dataPedido >= ?
            GROUP BY p.unidadeSaude
            ORDER BY total DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dataInicio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $unidades = [];
    $totais = [];

    while ($linha = $resultado->fetch_assoc()) {
        $unidades[] = $linha['unidadeSaude'] ?: 'Não definido';
        $totais[] = $linha['total'];
    }

    return [
        'unidades' => $unidades,
        'totais' => $totais
    ];
}

function obterTendenciaExames($conn, $dataInicio) {
    $sql = "SELECT 
                DATE_FORMAT(dataPedido, '%Y-%m') AS mes_ano,
                COUNT(*) AS total
            FROM PiCadExamesRealizados
            WHERE dataPedido IS NOT NULL AND dataPedido >= ?
            GROUP BY mes_ano
            ORDER BY mes_ano ASC
            LIMIT 12";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dataInicio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $periodos = [];
    $totais = [];

    while ($linha = $resultado->fetch_assoc()) {
        $periodos[] = $linha['mes_ano'];
        $totais[] = $linha['total'];
    }

    return [
        'periodos' => $periodos,
        'totais' => $totais
    ];
}

function obterUltimosExames($conn, $dataInicio, $limite = 10) {
    $sql = "SELECT 
                p.nome AS nome_paciente,
                e.exame,
                er.dataPedido,
                er.situacao
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido >= ?
            ORDER BY er.dataPedido DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $dataInicio, $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $exames = [];

    while ($linha = $resultado->fetch_assoc()) {
        $exames[] = $linha;
    }

    return $exames;
}

$examesMaisRealizados = obterExamesMaisRealizados($conn, $dataInicio);
$mediaIdade = obterMediaIdade($conn);
$fluxoPorMes = obterFluxoPorMes($conn, $dataInicio);
$situacaoExames = obterSituacaoExames($conn, $dataInicio);
$distribuicaoEtaria = obterDistribuicaoEtaria($conn);
$examesPorUnidade = obterExamesPorUnidade($conn, $dataInicio);
$tendenciaExames = obterTendenciaExames($conn, $dataInicio);
$ultimosExames = obterUltimosExames($conn, $dataInicio);

$totalPacientes = $conn->query("SELECT COUNT(*) as total FROM PICadPacientes")->fetch_assoc()['total'];
$totalExames = $conn->query("SELECT COUNT(*) as total FROM PICadExames")->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE dataPedido >= ?");
$stmt->bind_param("s", $dataInicio);
$stmt->execute();
$totalExamesRealizados = $stmt->get_result()->fetch_assoc()['total'];

$mediaExamesPorPaciente = $totalPacientes > 0 ? round($totalExamesRealizados / $totalPacientes, 1) : 0;

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE situacao = 'Pronto' AND dataPedido >= ?");
$stmt->bind_param("s", $dataInicio);
$stmt->execute();
$examesProntos = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE situacao = 'Entregue' AND dataPedido >= ?");
$stmt->bind_param("s", $dataInicio);
$stmt->execute();
$examesEntregues = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM PiCadExamesRealizados WHERE (situacao = 'Aguardando' OR situacao IS NULL) AND dataPedido >= ?");
$stmt->bind_param("s", $dataInicio);
$stmt->execute();
$examesAguardando = $stmt->get_result()->fetch_assoc()['total'];

$periodoTexto = "";
switch ($periodoSelecionado) {
    case 'dia':
        $periodoTexto = "Hoje";
        break;
    case 'semana':
        $periodoTexto = "Última semana";
        break;
    case 'mes':
        $periodoTexto = "Último mês";
        break;
    case 'trimestre':
        $periodoTexto = "Último trimestre";
        break;
    case 'ano':
        $periodoTexto = "Último ano";
        break;
    case 'todos':
        $periodoTexto = "Todos os tempos";
        break;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestão Hospitalar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
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

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #eeeeee;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 12px 12px 0 0 !important;
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .card-body {
            padding: 20px;
        }

        .stat-card {
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            height: 100%;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 5px 0;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .small-chart {
            height: 220px;
        }

        .table th {
            font-weight: 600;
            color: #495057;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-aguardando {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-pronto {
            background-color: #d4edda;
            color: #155724;
        }

        .status-entregue {
            background-color: #cce5ff;
            color: #004085;
        }

        /* Cores para os cards */
        .blue-card { background-color: #e7f1ff; }
        .blue-card .stat-icon, .blue-card .stat-value { color: #0d6efd; }

        .green-card { background-color: #e7f9e7; }
        .green-card .stat-icon, .green-card .stat-value { color: #198754; }

        .orange-card { background-color: #fff3e6; }
        .orange-card .stat-icon, .orange-card .stat-value { color: #fd7e14; }

        .purple-card { background-color: #f1e8ff; }
        .purple-card .stat-icon, .purple-card .stat-value { color: #6f42c1; }

        .red-card { background-color: #ffe7e7; }
        .red-card .stat-icon, .red-card .stat-value { color: #dc3545; }

        .yellow-card { background-color: #fffce7; }
        .yellow-card .stat-icon, .yellow-card .stat-value { color: #ffc107; }

        .teal-card { background-color: #e7f8f9; }
        .teal-card .stat-icon, .teal-card .stat-value { color: #20c997; }

        .period-filter {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .period-filter select {
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .period-filter select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
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

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .card-header {
                padding: 10px 15px;
                font-size: 1rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .chart-container {
                height: 250px;
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
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-vials me-1"></i> Exames
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cad_exame.html">Cadastro de Exames</a></li>
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
        <h1>Dashboard de Análise de Dados</h1>
        <p>Visão geral do sistema de gestão hospitalar - <?php echo $periodoTexto; ?></p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <!-- Filtro de período -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="periodoForm" method="get" action="">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5><i class="fas fa-filter me-2"></i> Filtrar dados por período</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <select class="form-select" name="periodo" id="filtroPerido" onchange="document.getElementById('periodoForm').submit()">
                                    <option value="dia" <?php echo $periodoSelecionado == 'dia' ? 'selected' : ''; ?>>Hoje</option>
                                    <option value="semana" <?php echo $periodoSelecionado == 'semana' ? 'selected' : ''; ?>>Última semana</option>
                                    <option value="mes" <?php echo $periodoSelecionado == 'mes' ? 'selected' : ''; ?>>Último mês</option>
                                    <option value="trimestre" <?php echo $periodoSelecionado == 'trimestre' ? 'selected' : ''; ?>>Último trimestre</option>
                                    <option value="ano" <?php echo $periodoSelecionado == 'ano' ? 'selected' : ''; ?>>Último ano</option>
                                    <option value="todos" <?php echo $periodoSelecionado == 'todos' ? 'selected' : ''; ?>>Todos os tempos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards de Estatísticas Rápidas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card blue-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalPacientes; ?></div>
                            <div class="stat-label">Total de Pacientes</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card green-card">
                            <div class="stat-icon">
                                <i class="fas fa-vial"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalExames; ?></div>
                            <div class="stat-label">Tipos de Exames</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card orange-card">
                            <div class="stat-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalExamesRealizados; ?></div>
                            <div class="stat-label">Exames Realizados</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card purple-card">
                            <div class="stat-icon">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="stat-value"><?php echo $mediaExamesPorPaciente; ?></div>
                            <div class="stat-label">Média de Exames por Paciente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status dos Exames -->
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card yellow-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value"><?php echo $examesAguardando; ?></div>
                            <div class="stat-label">Exames Aguardando</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card teal-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-value"><?php echo $examesProntos; ?></div>
                            <div class="stat-label">Exames Prontos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card red-card">
                            <div class="stat-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="stat-value"><?php echo $examesEntregues; ?></div>
                            <div class="stat-label">Exames Entregues</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Principais -->
        <div class="row">
            <!-- Exames Mais Realizados -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Exames Mais Realizados
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoExames"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Média de Idade por Gênero -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users"></i> Média de Idade por Gênero
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoIdade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Fluxo por Período (Mês) -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock"></i> Fluxo por Período (Mês)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoFluxo"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Situação dos Exames -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-tasks"></i> Situação dos Exames
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoSituacao"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Distribuição Etária -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-birthday-cake"></i> Distribuição Etária dos Pacientes
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoEtaria"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exames por Unidade de Saúde -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-hospital"></i> Exames por Unidade de Saúde
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoUnidade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Tendência de Exames ao Longo do Tempo -->
            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line"></i> Tendência de Exames ao Longo do Tempo
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="graficoTendencia"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Exames Realizados -->
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list-alt"></i> Últimos Exames Realizados
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Exame</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($ultimosExames as $exame): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($exame['nome_paciente']); ?></td>
                                        <td><?php echo htmlspecialchars($exame['exame']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($exame['dataPedido']))); ?></td>
                                        <td>
                                            <span class="status-badge <?php
                                            echo $exame['situacao'] == 'Entregue' ? 'status-entregue' :
                                                ($exame['situacao'] == 'Pronto' ? 'status-pronto' : 'status-aguardando');
                                            ?>">
                                                <?php echo htmlspecialchars($exame['situacao'] ?: 'Aguardando'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($ultimosExames)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3">Nenhum exame encontrado no período selecionado</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Configuração dos gráficos
    document.addEventListener('DOMContentLoaded', function() {
        // Cores padrão para gráficos
        const corPrimaria = '#0d6efd';
        const corSecundaria = '#6c757d';
        const corSucesso = '#28a745';
        const corPerigo = '#dc3545';
        const corAlerta = '#ffc107';
        const corInfo = '#17a2b8';

        const coresGrafico = [
            'rgba(13, 110, 253, 0.7)',
            'rgba(220, 53, 69, 0.7)',
            'rgba(255, 193, 7, 0.7)',
            'rgba(40, 167, 69, 0.7)',
            'rgba(23, 162, 184, 0.7)',
            'rgba(108, 117, 125, 0.7)',
            'rgba(111, 66, 193, 0.7)',
            'rgba(253, 126, 20, 0.7)'
        ];

        const coresBordaGrafico = [
            'rgba(13, 110, 253, 1)',
            'rgba(220, 53, 69, 1)',
            'rgba(255, 193, 7, 1)',
            'rgba(40, 167, 69, 1)',
            'rgba(23, 162, 184, 1)',
            'rgba(108, 117, 125, 1)',
            'rgba(111, 66, 193, 1)',
            'rgba(253, 126, 20, 1)'
        ];

        // Gráfico de Exames Mais Realizados
        const ctxExames = document.getElementById('graficoExames').getContext('2d');
        const graficoExames = new Chart(ctxExames, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($examesMaisRealizados['exames']); ?>,
                datasets: [{
                    label: 'Quantidade de Exames',
                    data: <?php echo json_encode($examesMaisRealizados['totais']); ?>,
                    backgroundColor: coresGrafico[0],
                    borderColor: coresBordaGrafico[0],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Quantidade: ${context.parsed.y}`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Média de Idade por Gênero
        const ctxIdade = document.getElementById('graficoIdade').getContext('2d');
        const graficoIdade = new Chart(ctxIdade, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($mediaIdade['generos']); ?>,
                datasets: [{
                    label: 'Média de Idade (anos)',
                    data: <?php echo json_encode($mediaIdade['medias']); ?>,
                    backgroundColor: [
                        coresGrafico[0],
                        coresGrafico[1],
                        coresGrafico[2]
                    ],
                    borderColor: [
                        coresBordaGrafico[0],
                        coresBordaGrafico[1],
                        coresBordaGrafico[2]
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Gráfico de Fluxo por Período
        const ctxFluxo = document.getElementById('graficoFluxo').getContext('2d');
        const graficoFluxo = new Chart(ctxFluxo, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($fluxoPorMes['meses']); ?>,
                datasets: [{
                    label: 'Número de Exames',
                    data: <?php echo json_encode($fluxoPorMes['dados']); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Gráfico de Situação dos Exames
        const ctxSituacao = document.getElementById('graficoSituacao').getContext('2d');
        const graficoSituacao = new Chart(ctxSituacao, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($situacaoExames['situacoes']); ?>,
                datasets: [{
                    label: 'Quantidade',
                    data: <?php echo json_encode($situacaoExames['totais']); ?>,
                    backgroundColor: [
                        coresGrafico[3], // Success
                        coresGrafico[2], // Warning
                        coresGrafico[1]  // Danger
                    ],
                    borderColor: [
                        coresBordaGrafico[3],
                        coresBordaGrafico[2],
                        coresBordaGrafico[1]
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Distribuição Etária
        const ctxEtaria = document.getElementById('graficoEtaria').getContext('2d');
        const graficoEtaria = new Chart(ctxEtaria, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($distribuicaoEtaria['faixas']); ?>,
                datasets: [{
                    label: 'Quantidade de Pacientes',
                    data: <?php echo json_encode($distribuicaoEtaria['totais']); ?>,
                    backgroundColor: coresGrafico[6],
                    borderColor: coresBordaGrafico[6],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Gráfico de Exames por Unidade de Saúde
        const ctxUnidade = document.getElementById('graficoUnidade').getContext('2d');
        const graficoUnidade = new Chart(ctxUnidade, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($examesPorUnidade['unidades']); ?>,
                datasets: [{
                    label: 'Quantidade de Exames',
                    data: <?php echo json_encode($examesPorUnidade['totais']); ?>,
                    backgroundColor: coresGrafico,
                    borderColor: coresBordaGrafico,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Tendência de Exames
        const ctxTendencia = document.getElementById('graficoTendencia').getContext('2d');
        const graficoTendencia = new Chart(ctxTendencia, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($tendenciaExames['periodos']); ?>,
                datasets: [{
                    label: 'Exames Realizados',
                    data: <?php echo json_encode($tendenciaExames['totais']); ?>,
                    backgroundColor: 'rgba(111, 66, 193, 0.2)',
                    borderColor: 'rgba(111, 66, 193, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(111, 66, 193, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return 'Período: ' + context[0].label;
                            },
                            label: function(context) {
                                return `Total de exames: ${context.parsed.y}`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

</body>
</html>
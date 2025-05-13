<?php
require_once 'db_connection.php';

$dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d', strtotime('-30 days'));
$dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');

$tipoExame = isset($_GET['tipo_exame']) ? $_GET['tipo_exame'] : '';
$situacao = isset($_GET['situacao']) ? $_GET['situacao'] : '';
$unidadeSaude = isset($_GET['unidade_saude']) ? $_GET['unidade_saude'] : '';
$faixaEtaria = isset($_GET['faixa_etaria']) ? $_GET['faixa_etaria'] : '';
$genero = isset($_GET['genero']) ? $_GET['genero'] : '';

$itensPorPagina = isset($_GET['itens_por_pagina']) ? (int)$_GET['itens_por_pagina'] : 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

function obterExamesFiltrados($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero, $limit, $offset) {
    $sql = "SELECT 
                er.codigo, 
                p.nome AS nome_paciente,
                p.dataNasc,
                p.genero,
                p.unidadeSaude,
                e.exame,
                er.dataPedido,
                er.dataResultado,
                er.dataEntrega,
                er.situacao
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido BETWEEN ? AND ?";

    $params = [$dataInicio, $dataFim];
    $types = "ss";

    if ($tipoExame) {
        $sql .= " AND er.codExame = ?";
        $params[] = $tipoExame;
        $types .= "i";
    }

    if ($situacao) {
        $sql .= " AND er.situacao = ?";
        $params[] = $situacao;
        $types .= "s";
    }

    if ($unidadeSaude) {
        $sql .= " AND p.unidadeSaude = ?";
        $params[] = $unidadeSaude;
        $types .= "s";
    }

    if ($genero) {
        $sql .= " AND p.genero = ?";
        $params[] = $genero;
        $types .= "s";
    }

    if ($faixaEtaria) {
        switch ($faixaEtaria) {
            case 'menor18':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) < 18";
                break;
            case '18a30':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 18 AND 30";
                break;
            case '31a45':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 31 AND 45";
                break;
            case '46a60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 46 AND 60";
                break;
            case 'maior60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) > 60";
                break;
        }
    }

    $sql .= " ORDER BY er.dataPedido DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $exames = [];
    while ($row = $result->fetch_assoc()) {
        $row['idade'] = date_diff(date_create($row['dataNasc']), date_create('now'))->y;
        $exames[] = $row;
    }

    return $exames;
}

function obterTotalExamesFiltrados($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero) {
    $sql = "SELECT 
                COUNT(*) as total
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido BETWEEN ? AND ?";

    $params = [$dataInicio, $dataFim];
    $types = "ss";

    if ($tipoExame) {
        $sql .= " AND er.codExame = ?";
        $params[] = $tipoExame;
        $types .= "i";
    }

    if ($situacao) {
        $sql .= " AND er.situacao = ?";
        $params[] = $situacao;
        $types .= "s";
    }

    if ($unidadeSaude) {
        $sql .= " AND p.unidadeSaude = ?";
        $params[] = $unidadeSaude;
        $types .= "s";
    }

    if ($genero) {
        $sql .= " AND p.genero = ?";
        $params[] = $genero;
        $types .= "s";
    }

    if ($faixaEtaria) {
        switch ($faixaEtaria) {
            case 'menor18':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) < 18";
                break;
            case '18a30':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 18 AND 30";
                break;
            case '31a45':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 31 AND 45";
                break;
            case '46a60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 46 AND 60";
                break;
            case 'maior60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) > 60";
                break;
        }
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'];
}

function obterTiposExames($conn) {
    $sql = "SELECT codigo, exame FROM PICadExames ORDER BY exame";
    $result = $conn->query($sql);

    $exames = [];
    while ($row = $result->fetch_assoc()) {
        $exames[] = $row;
    }

    return $exames;
}

function obterUnidadesSaude($conn) {
    $sql = "SELECT DISTINCT unidadeSaude FROM PICadPacientes WHERE unidadeSaude IS NOT NULL AND unidadeSaude != '' ORDER BY unidadeSaude";
    $result = $conn->query($sql);

    $unidades = [];
    while ($row = $result->fetch_assoc()) {
        $unidades[] = $row['unidadeSaude'];
    }

    return $unidades;
}

function obterMetricasResumo($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero) {
    $baseSelect = "FROM PiCadExamesRealizados er
                  JOIN PICadPacientes p ON er.codPaciente = p.codigo
                  JOIN PICadExames e ON er.codExame = e.codigo
                  WHERE er.dataPedido BETWEEN ? AND ?";

    $params = [$dataInicio, $dataFim];
    $types = "ss";

    if ($tipoExame) {
        $baseSelect .= " AND er.codExame = ?";
        $params[] = $tipoExame;
        $types .= "i";
    }

    if ($situacao) {
        $baseSelect .= " AND er.situacao = ?";
        $params[] = $situacao;
        $types .= "s";
    }

    if ($unidadeSaude) {
        $baseSelect .= " AND p.unidadeSaude = ?";
        $params[] = $unidadeSaude;
        $types .= "s";
    }

    if ($genero) {
        $baseSelect .= " AND p.genero = ?";
        $params[] = $genero;
        $types .= "s";
    }

    if ($faixaEtaria) {
        switch ($faixaEtaria) {
            case 'menor18':
                $baseSelect .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) < 18";
                break;
            case '18a30':
                $baseSelect .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 18 AND 30";
                break;
            case '31a45':
                $baseSelect .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 31 AND 45";
                break;
            case '46a60':
                $baseSelect .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 46 AND 60";
                break;
            case 'maior60':
                $baseSelect .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) > 60";
                break;
        }
    }

    $sql = "SELECT COUNT(*) as total " . $baseSelect;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $totalExames = $stmt->get_result()->fetch_assoc()['total'];

    // Exams by status
    $sql = "SELECT er.situacao, COUNT(*) as total " . $baseSelect . " GROUP BY er.situacao";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $statusCounts = [
        'Aguardando' => 0,
        'Pronto' => 0,
        'Entregue' => 0
    ];

    while ($row = $result->fetch_assoc()) {
        $status = $row['situacao'] ?: 'Aguardando';
        $statusCounts[$status] = $row['total'];
    }
    $sql = "SELECT AVG(DATEDIFF(er.dataResultado, er.dataPedido)) as avg_processing_time 
            " . $baseSelect . " 
            AND er.dataResultado IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $avgProcessingTime = $stmt->get_result()->fetch_assoc()['avg_processing_time'];

    $sql = "SELECT AVG(DATEDIFF(er.dataEntrega, er.dataResultado)) as avg_waiting_time 
            " . $baseSelect . " 
            AND er.dataResultado IS NOT NULL 
            AND er.dataEntrega IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $avgWaitingTime = $stmt->get_result()->fetch_assoc()['avg_waiting_time'];

    $sql = "SELECT COUNT(DISTINCT er.codPaciente) as unique_patients " . $baseSelect;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $uniquePatients = $stmt->get_result()->fetch_assoc()['unique_patients'];

    return [
        'totalExames' => $totalExames,
        'statusCounts' => $statusCounts,
        'avgProcessingTime' => $avgProcessingTime,
        'avgWaitingTime' => $avgWaitingTime,
        'uniquePatients' => $uniquePatients
    ];
}

function obterExamesPorMes($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero) {
    $sql = "SELECT 
                DATE_FORMAT(er.dataPedido, '%Y-%m') as mes,
                COUNT(*) as total
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido BETWEEN ? AND ?";

    $params = [$dataInicio, $dataFim];
    $types = "ss";

    if ($tipoExame) {
        $sql .= " AND er.codExame = ?";
        $params[] = $tipoExame;
        $types .= "i";
    }

    if ($situacao) {
        $sql .= " AND er.situacao = ?";
        $params[] = $situacao;
        $types .= "s";
    }

    if ($unidadeSaude) {
        $sql .= " AND p.unidadeSaude = ?";
        $params[] = $unidadeSaude;
        $types .= "s";
    }

    if ($genero) {
        $sql .= " AND p.genero = ?";
        $params[] = $genero;
        $types .= "s";
    }

    if ($faixaEtaria) {
        switch ($faixaEtaria) {
            case 'menor18':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) < 18";
                break;
            case '18a30':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 18 AND 30";
                break;
            case '31a45':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 31 AND 45";
                break;
            case '46a60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 46 AND 60";
                break;
            case 'maior60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) > 60";
                break;
        }
    }

    $sql .= " GROUP BY DATE_FORMAT(er.dataPedido, '%Y-%m') ORDER BY mes";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $examesPorMes = [];
    while ($row = $result->fetch_assoc()) {
        $examesPorMes[] = $row;
    }

    return $examesPorMes;
}

function obterExamesPorTipo($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero) {
    $sql = "SELECT 
                e.exame,
                COUNT(*) as total
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido BETWEEN ? AND ?";

    $params = [$dataInicio, $dataFim];
    $types = "ss";

    if ($tipoExame) {
        $sql .= " AND er.codExame = ?";
        $params[] = $tipoExame;
        $types .= "i";
    }

    if ($situacao) {
        $sql .= " AND er.situacao = ?";
        $params[] = $situacao;
        $types .= "s";
    }

    if ($unidadeSaude) {
        $sql .= " AND p.unidadeSaude = ?";
        $params[] = $unidadeSaude;
        $types .= "s";
    }

    if ($genero) {
        $sql .= " AND p.genero = ?";
        $params[] = $genero;
        $types .= "s";
    }

    if ($faixaEtaria) {
        switch ($faixaEtaria) {
            case 'menor18':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) < 18";
                break;
            case '18a30':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 18 AND 30";
                break;
            case '31a45':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 31 AND 45";
                break;
            case '46a60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 46 AND 60";
                break;
            case 'maior60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) > 60";
                break;
        }
    }

    $sql .= " GROUP BY e.exame ORDER BY total DESC LIMIT 10";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $examesPorTipo = [];
    while ($row = $result->fetch_assoc()) {
        $examesPorTipo[] = $row;
    }

    return $examesPorTipo;
}

function exportarParaCSV($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero) {
    if (!isset($_GET['export_csv'])) {
        return;
    }

    $sql = "SELECT 
                er.codigo as 'ID', 
                p.nome as 'Nome_Paciente',
                p.dataNasc as 'Data_Nascimento',
                TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) as 'Idade',
                CASE 
                    WHEN p.genero = 'M' THEN 'Masculino'
                    WHEN p.genero = 'F' THEN 'Feminino'
                    ELSE 'Outro'
                END as 'Genero',
                p.unidadeSaude as 'Unidade_Saude',
                e.exame as 'Tipo_Exame',
                er.dataPedido as 'Data_Pedido',
                er.dataResultado as 'Data_Resultado',
                er.dataEntrega as 'Data_Entrega',
                er.situacao as 'Situacao',
                DATEDIFF(er.dataResultado, er.dataPedido) as 'Tempo_Processamento',
                DATEDIFF(er.dataEntrega, er.dataResultado) as 'Tempo_Entrega'
            FROM PiCadExamesRealizados er
            JOIN PICadPacientes p ON er.codPaciente = p.codigo
            JOIN PICadExames e ON er.codExame = e.codigo
            WHERE er.dataPedido BETWEEN ? AND ?";

    $params = [$dataInicio, $dataFim];
    $types = "ss";

    if ($tipoExame) {
        $sql .= " AND er.codExame = ?";
        $params[] = $tipoExame;
        $types .= "i";
    }

    if ($situacao) {
        $sql .= " AND er.situacao = ?";
        $params[] = $situacao;
        $types .= "s";
    }

    if ($unidadeSaude) {
        $sql .= " AND p.unidadeSaude = ?";
        $params[] = $unidadeSaude;
        $types .= "s";
    }

    if ($genero) {
        $sql .= " AND p.genero = ?";
        $params[] = $genero;
        $types .= "s";
    }

    if ($faixaEtaria) {
        switch ($faixaEtaria) {
            case 'menor18':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) < 18";
                break;
            case '18a30':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 18 AND 30";
                break;
            case '31a45':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 31 AND 45";
                break;
            case '46a60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) BETWEEN 46 AND 60";
                break;
            case 'maior60':
                $sql .= " AND TIMESTAMPDIFF(YEAR, p.dataNasc, CURDATE()) > 60";
                break;
        }
    }

    $sql .= " ORDER BY er.dataPedido DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=exames_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');

    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    $firstRow = true;

    while ($row = $result->fetch_assoc()) {
        if ($firstRow) {
            fputcsv($output, array_keys($row));
            $firstRow = false;
        }
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

$tiposExames = obterTiposExames($conn);
$unidadesSaude = obterUnidadesSaude($conn);
$totalExames = obterTotalExamesFiltrados($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero);
$exames = obterExamesFiltrados($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero, $itensPorPagina, $offset);
$metricas = obterMetricasResumo($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero);
$examesPorMes = obterExamesPorMes($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero);
$examesPorTipo = obterExamesPorTipo($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero);

$totalPaginas = ceil($totalExames / $itensPorPagina);

exportarParaCSV($conn, $dataInicio, $dataFim, $tipoExame, $situacao, $unidadeSaude, $faixaEtaria, $genero);

$conn->close();
?>

    <!DOCTYPE html>
    <html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Dados - Sistema de Gestão Hospitalar</title>
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
            overflow: hidden;
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

        .filter-card {
            background-color: #f8f9fa;
            border: none;
        }

        .filter-card .card-body {
            padding: 15px;
        }

        .btn-export {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-export:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-filter {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .pagination {
            margin-top: 1rem;
            justify-content: center;
        }

        .page-link {
            color: #0d6efd;
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .page-link:hover {
            z-index: 2;
            color: #0a58ca;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
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

        .footer {
            background-color: #343a40;
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
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
                        <li><a class="dropdown-item" href="cad_pacientes.html">Cadastrar Pacientes</a></li>
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
        <h1>Análise de Dados e Exportação</h1>
        <p>Filtre, analise e exporte dados de exames do sistema de gestão hospitalar</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="card mb-4 filter-card">
            <div class="card-body">
                <form method="get" action="" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="data_inicio" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo $dataInicio; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="data_fim" class="form-label">Data Fim</label>
                            <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?php echo $dataFim; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="tipo_exame" class="form-label">Tipo de Exame</label>
                            <select class="form-select" id="tipo_exame" name="tipo_exame">
                                <option value="">Todos</option>
                                <?php foreach ($tiposExames as $tipo): ?>
                                    <option value="<?php echo $tipo['codigo']; ?>" <?php echo ($tipoExame == $tipo['codigo']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo['exame']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="situacao" class="form-label">Situação</label>
                            <select class="form-select" id="situacao" name="situacao">
                                <option value="">Todas</option>
                                <option value="Aguardando" <?php echo ($situacao == 'Aguardando') ? 'selected' : ''; ?>>Aguardando</option>
                                <option value="Pronto" <?php echo ($situacao == 'Pronto') ? 'selected' : ''; ?>>Pronto</option>
                                <option value="Entregue" <?php echo ($situacao == 'Entregue') ? 'selected' : ''; ?>>Entregue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="unidade_saude" class="form-label">Unidade de Saúde</label>
                            <select class="form-select" id="unidade_saude" name="unidade_saude">
                                <option value="">Todas</option>
                                <?php foreach ($unidadesSaude as $unidade): ?>
                                    <option value="<?php echo $unidade; ?>" <?php echo ($unidadeSaude == $unidade) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($unidade); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="faixa_etaria" class="form-label">Faixa Etária</label>
                            <select class="form-select" id="faixa_etaria" name="faixa_etaria">
                                <option value="">Todas</option>
                                <option value="menor18" <?php echo ($faixaEtaria == 'menor18') ? 'selected' : ''; ?>>Menor de 18</option>
                                <option value="18a30" <?php echo ($faixaEtaria == '18a30') ? 'selected' : ''; ?>>18 a 30 anos</option>
                                <option value="31a45" <?php echo ($faixaEtaria == '31a45') ? 'selected' : ''; ?>>31 a 45 anos</option>
                                <option value="46a60" <?php echo ($faixaEtaria == '46a60') ? 'selected' : ''; ?>>46 a 60 anos</option>
                                <option value="maior60" <?php echo ($faixaEtaria == 'maior60') ? 'selected' : ''; ?>>Maior de 60</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="genero" class="form-label">Gênero</label>
                            <select class="form-select" id="genero" name="genero">
                                <option value="">Todos</option>
                                <option value="M" <?php echo ($genero == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo ($genero == 'F') ? 'selected' : ''; ?>>Feminino</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="itens_por_pagina" class="form-label">Itens por Página</label>
                            <select class="form-select" id="itens_por_pagina" name="itens_por_pagina">
                                <option value="10" <?php echo ($itensPorPagina == 10) ? 'selected' : ''; ?>>10</option>
                                <option value="20" <?php echo ($itensPorPagina == 20) ? 'selected' : ''; ?>>20</option>
                                <option value="50" <?php echo ($itensPorPagina == 50) ? 'selected' : ''; ?>>50</option>
                                <option value="100" <?php echo ($itensPorPagina == 100) ? 'selected' : ''; ?>>100</option>
                            </select>
                        </div>
                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn btn-filter">
                                <i class="fas fa-filter me-2"></i> Filtrar
                            </button>
                            <button type="submit" class="btn btn-export ms-2" name="export_csv" value="1">
                                <i class="fas fa-file-csv me-2"></i> Exportar CSV
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="limparFiltros()">
                                <i class="fas fa-eraser me-2"></i> Limpar Filtros
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card blue-card">
                            <div class="stat-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="stat-value"><?php echo $metricas['totalExames']; ?></div>
                            <div class="stat-label">Total de Exames</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card purple-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value"><?php echo $metricas['uniquePatients']; ?></div>
                            <div class="stat-label">Pacientes Atendidos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card orange-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value"><?php echo round($metricas['avgProcessingTime']); ?></div>
                            <div class="stat-label">Média de Dias p/ Resultado</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card green-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value"><?php echo round($metricas['avgWaitingTime']); ?></div>
                            <div class="stat-label">Média de Dias p/ Entrega</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card yellow-card">
                            <div class="stat-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="stat-value"><?php echo $metricas['statusCounts']['Aguardando']; ?></div>
                            <div class="stat-label">Exames Aguardando</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card teal-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-value"><?php echo $metricas['statusCounts']['Pronto']; ?></div>
                            <div class="stat-label">Exames Prontos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="stat-card red-card">
                            <div class="stat-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="stat-value"><?php echo $metricas['statusCounts']['Entregue']; ?></div>
                            <div class="stat-label">Exames Entregues</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-line"></i> Evolução Mensal de Exames
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartExamesMes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie"></i> Exames por Tipo
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartExamesTipo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table"></i> Dados Detalhados
                    </div>
                    <div>
                        <span class="badge bg-primary"><?php echo $totalExames; ?> registros encontrados</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Idade</th>
                            <th>Gênero</th>
                            <th>Unidade</th>
                            <th>Exame</th>
                            <th>Data Pedido</th>
                            <th>Data Resultado</th>
                            <th>Data Entrega</th>
                            <th>Situação</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($exames) > 0): ?>
                            <?php foreach ($exames as $exame): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exame['nome_paciente']); ?></td>
                                    <td><?php echo htmlspecialchars($exame['idade']); ?></td>
                                    <td><?php echo $exame['genero'] == 'M' ? 'Masculino' : ($exame['genero'] == 'F' ? 'Feminino' : 'Outro'); ?></td>
                                    <td><?php echo htmlspecialchars($exame['unidadeSaude']); ?></td>
                                    <td><?php echo htmlspecialchars($exame['exame']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($exame['dataPedido'])); ?></td>
                                    <td><?php echo $exame['dataResultado'] ? date('d/m/Y', strtotime($exame['dataResultado'])) : '-'; ?></td>
                                    <td><?php echo $exame['dataEntrega'] ? date('d/m/Y', strtotime($exame['dataEntrega'])) : '-'; ?></td>
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
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Nenhum registro encontrado</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Navegação de página">
                        <ul class="pagination">
                            <li class="page-item <?php echo $paginaAtual <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $paginaAtual - 1])); ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPaginas || ($i >= $paginaAtual - 2 && $i <= $paginaAtual + 2)): ?>
                                    <li class="page-item <?php echo $i == $paginaAtual ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $i])); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php elseif ($i == $paginaAtual - 3 || $i == $paginaAtual + 3): ?>
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#">...</a>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <li class="page-item <?php echo $paginaAtual >= $totalPaginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['pagina' => $paginaAtual + 1])); ?>" aria-label="Próximo">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
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
    function limparFiltros() {
        document.getElementById('data_inicio').value = '<?php echo date('Y-m-d', strtotime('-30 days')); ?>';
        document.getElementById('data_fim').value = '<?php echo date('Y-m-d'); ?>';

        document.getElementById('tipo_exame').value = '';
        document.getElementById('situacao').value = '';
        document.getElementById('unidade_saude').value = '';
        document.getElementById('faixa_etaria').value = '';
        document.getElementById('genero').value = '';

        document.getElementById('filterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctxMonthly = document.getElementById('chartExamesMes').getContext('2d');
        const examesMesData = <?php
            $labels = [];
            $data = [];

            foreach ($examesPorMes as $item) {
                $date = DateTime::createFromFormat('Y-m', $item['mes']);
                $labels[] = $date->format('M/Y');
                $data[] = $item['total'];
            }

            echo json_encode([
                'labels' => $labels,
                'data' => $data
            ]);
            ?>;

        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: examesMesData.labels,
                datasets: [{
                    label: 'Quantidade de Exames',
                    data: examesMesData.data,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.3,
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

        const ctxType = document.getElementById('chartExamesTipo').getContext('2d');
        const examesTipoData = <?php
            $labels = [];
            $data = [];

            foreach ($examesPorTipo as $item) {
                $labels[] = $item['exame'];
                $data[] = $item['total'];
            }

            echo json_encode([
                'labels' => $labels,
                'data' => $data
            ]);
            ?>;

        new Chart(ctxType, {
            type: 'pie',
            data: {
                labels: examesTipoData.labels,
                datasets: [{
                    data: examesTipoData.data,
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(23, 162, 184, 0.7)',
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(111, 66, 193, 0.7)',
                        'rgba(253, 126, 20, 0.7)',
                        'rgba(32, 201, 151, 0.7)',
                        'rgba(102, 16, 242, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
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
    });
</script>

</body>
    </html>

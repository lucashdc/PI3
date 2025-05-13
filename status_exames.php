<?php
require_once 'db_connection.php';

$sql = "SELECT PiCadExamesRealizados.codigo, PICadPacientes.nome, PICadExames.exame, PiCadExamesRealizados.dataPedido, 
    PiCadExamesRealizados.dataResultado, PiCadExamesRealizados.dataEntrega, PiCadExamesRealizados.situacao
    FROM PiCadExamesRealizados
    INNER JOIN PICadPacientes ON PiCadExamesRealizados.codPaciente = PICadPacientes.codigo
    INNER JOIN PICadExames ON PiCadExamesRealizados.codExame = PICadExames.codigo";


if (isset($_POST['busca'])) {
    $pesq = '%' . $_POST['busca'] . '%';
    $stmt = $conn->prepare($sql . " WHERE PICadPacientes.nome LIKE ? OR PICadExames.exame LIKE ?");
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }
    $stmt->bind_param("ss", $pesq, $pesq);
} else {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }
}

$stmt->execute();
$result = $stmt->get_result();
$exames = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $exames[] = $row;
    }
}

// Estatísticas para o cabeçalho
$totalExames = count($exames);
$examesProntos = 0;
$examesEntregues = 0;
$examesAguardando = 0;

foreach($exames as $exame) {
    if($exame['situacao'] == 'Pronto') {
        $examesProntos++;
    } elseif($exame['situacao'] == 'Entregue') {
        $examesEntregues++;
    } else {
        $examesAguardando++;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status de Exames Realizados - Sistema de Gestão Hospitalar</title>
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
            background-color: #0d6efd;
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
            background-color: rgba(13, 110, 253, 0.05);
        }

        .btn-action {
            margin-right: 5px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .status-badge {
            padding: 0.4rem 0.6rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
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

        .modal-content {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: var(--bg-gradient);
            color: white;
            border-bottom: none;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 1.5rem 1.5rem;
        }

        .stats-cards {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            flex: 1;
            background-color: #fff;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card-aguardando {
            border-top: 4px solid #ffc107;
        }

        .stat-card-pronto {
            border-top: 4px solid #28a745;
        }

        .stat-card-entregue {
            border-top: 4px solid #0d6efd;
        }

        .stat-card-total {
            border-top: 4px solid #6f42c1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .stat-card-aguardando .stat-icon {
            color: #ffc107;
        }

        .stat-card-pronto .stat-icon {
            color: #28a745;
        }

        .stat-card-entregue .stat-icon {
            color: #0d6efd;
        }

        .stat-card-total .stat-icon {
            color: #6f42c1;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .stats-cards {
                flex-direction: column;
            }

            .header-section h1 {
                font-size: 1.5rem;
            }

            .header-section p {
                font-size: 0.9rem;
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
                        <li><a class="dropdown-item" href="cad_exame2.php">Cadastro de Exames</a></li>
                        <li><a class="dropdown-item" href="listar_exames.php">Lista de Exames</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-clipboard-check me-1"></i> Regulação
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="exames_pacientes.php">Vincular Exames a Pacientes</a></li>
                        <li><a class="dropdown-item active" href="status_exames.php">Status de Exames Realizados</a></li>
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
        <h1>Status de Exames Realizados</h1>
        <p>Gerencie e acompanhe a situação de todos os exames realizados pelos pacientes</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <!-- Cards de Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card stat-card-aguardando">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $examesAguardando; ?></div>
                <div class="stat-label">Aguardando</div>
            </div>
            <div class="stat-card stat-card-pronto">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $examesProntos; ?></div>
                <div class="stat-label">Prontos</div>
            </div>
            <div class="stat-card stat-card-entregue">
                <div class="stat-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-value"><?php echo $examesEntregues; ?></div>
                <div class="stat-label">Entregues</div>
            </div>
            <div class="stat-card stat-card-total">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-value"><?php echo $totalExames; ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>

        <div class="content-container">
            <div class="content-header">
                <i class="fas fa-tasks"></i> Gerenciamento de Status de Exames
            </div>
            <div class="content-body">
                <div class="search-box">
                    <form class="row g-2 align-items-center" method="POST" action="">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="inp_busca" name="busca"
                                       placeholder="Pesquisar por nome do paciente ou tipo de exame"
                                       value="<?= isset($_POST['busca']) ? htmlspecialchars($_POST['busca']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='status_exames.php'">
                                <i class="fas fa-redo-alt me-1"></i> Limpar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Exame</th>
                            <th>Data Pedido</th>
                            <th>Data Resultado</th>
                            <th>Data Entrega</th>
                            <th>Situação</th>
                            <th class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($exames) > 0): ?>
                            <?php foreach($exames as $exame): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exame['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($exame['exame']); ?></td>
                                    <td><?php echo htmlspecialchars($exame['dataPedido']); ?></td>
                                    <td><?php echo htmlspecialchars($exame['dataResultado']); ?></td>
                                    <td><?php echo htmlspecialchars($exame['dataEntrega']); ?></td>
                                    <td>
                                        <span class="status-badge <?php
                                        echo $exame['situacao'] == 'Entregue' ? 'status-entregue' :
                                            ($exame['situacao'] == 'Pronto' ? 'status-pronto' : 'status-aguardando');
                                        ?>">
                                            <?php echo htmlspecialchars($exame['situacao'] ?: 'Aguardando'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm btn-action"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="<?php echo htmlspecialchars($exame['codigo']); ?>"
                                                data-situacao="<?php echo htmlspecialchars($exame['situacao']); ?>"
                                                data-data-entrega="<?php echo htmlspecialchars($exame['dataEntrega']); ?>">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </button>
                                        <a href="excluir_examePaciente.php?codigo=<?php echo htmlspecialchars($exame['codigo']); ?>"
                                           class="btn btn-danger btn-sm btn-action"
                                           onclick="return confirm('Tem certeza que deseja excluir este exame?');">
                                            <i class="fas fa-trash-alt me-1"></i> Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Nenhum exame encontrado.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($exames) > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="badge bg-primary">
                                <i class="fas fa-clipboard-list me-1"></i> Total: <?= count($exames) ?> exames
                            </span>
                        </div>
                        <a href="exames_pacientes.php" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Vincular Novo Exame
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center mt-3">
                        <a href="exames_pacientes.php" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Vincular Novo Exame
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit me-2"></i> Atualizar Status do Exame
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="atualizar_exame.php">
                    <input type="hidden" id="editId" name="codigo">
                    <div class="mb-3">
                        <label class="form-label">Situação</label>
                        <select class="form-select" id="editSituacao" name="situacao" required>
                            <option value="Pronto">Pronto</option>
                            <option value="Entregue">Entregue</option>
                            <option value="Aguardando">Aguardando</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data de Entrega</label>
                        <input type="date" class="form-control" id="editDataEntrega" name="dataEntrega">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Atualizar
                        </button>
                    </div>
                </form>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Configuração do modal de edição
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const situacao = button.getAttribute('data-situacao');
                const dataEntrega = button.getAttribute('data-data-entrega');

                document.getElementById('editId').value = id;

                // Selecionar a situação atual no select
                const selectSituacao = document.getElementById('editSituacao');
                for (let i = 0; i < selectSituacao.options.length; i++) {
                    if (selectSituacao.options[i].value === situacao) {
                        selectSituacao.selectedIndex = i;
                        break;
                    }
                }

                document.getElementById('editDataEntrega').value = dataEntrega;
            });
        }

        // Atualizar data de entrega automaticamente ao selecionar "Entregue"
        const selectSituacao = document.getElementById('editSituacao');
        if (selectSituacao) {
            selectSituacao.addEventListener('change', function() {
                const dataEntregaInput = document.getElementById('editDataEntrega');
                if (this.value === 'Entregue' && (!dataEntregaInput.value || dataEntregaInput.value === '')) {
                    // Formatar a data atual para o formato yyyy-mm-dd
                    const hoje = new Date();
                    const ano = hoje.getFullYear();
                    const mes = String(hoje.getMonth() + 1).padStart(2, '0');
                    const dia = String(hoje.getDate()).padStart(2, '0');
                    dataEntregaInput.value = `${ano}-${mes}-${dia}`;
                }
            });
        }
    });
</script>
</body>
</html>

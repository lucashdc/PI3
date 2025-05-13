<?php
include 'db_connection.php';

function buscarPacientes($conn, $search = null)
{
    $query = "SELECT * FROM PICadPacientes";
    if ($search) {
        $query .= " WHERE nome LIKE ? OR cpf = ? OR sus = ? OR prontuario = ?";
        $stmt = $conn->prepare($query);
        $searchParam = "%$search%";
        $stmt->bind_param("ssss", $searchParam, $search, $search, $search);
    } else {
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$pacientes = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];
    $pacientes = buscarPacientes($conn, $search);
} else {
    $pacientes = buscarPacientes($conn);
}

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $stmt = $conn->prepare("SELECT endereco, numero, cidade, uf, cep FROM PICadPacientes WHERE codigo = ?");
    $stmt->bind_param("i", $codigo);
    $stmt->execute();
    $paciente = $stmt->get_result()->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($paciente ? $paciente : ['erro' => 'Paciente não encontrado']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes - Sistema de Gestão Hospitalar</title>
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

        /* Estilos específicos para o mapa */
        #mapContainer {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        #modal, #overlay {
            display: none;
            position: fixed;
            z-index: 1051;
        }

        #overlay {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        #modal {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        #endereco {
            font-weight: 500;
            color: #495057;
            margin-top: 1rem;
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
                    <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-1"></i> Pacientes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cad_pacientes.php">Cadastrar Pacientes</a></li>
                        <li><a class="dropdown-item active" href="lista_pacientes.php">Listar Pacientes</a></li>
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
        <h1>Lista de Pacientes</h1>
        <p>Visualize, pesquise e gerencie todos os pacientes cadastrados no sistema</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="content-container">
            <div class="content-header">
                <i class="fas fa-users"></i> Gerenciamento de Pacientes
            </div>
            <div class="content-body">
                <div class="search-box">
                    <form method="get" action="" class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Pesquisar por nome, CPF, nº SUS ou prontuário" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="lista_pacientes.php" class="btn btn-secondary">
                                <i class="fas fa-redo-alt me-1"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>SUS</th>
                            <th>Prontuário</th>
                            <th class="text-center">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pacientes)): ?>
                            <?php foreach ($pacientes as $paciente): ?>
                                <tr>
                                    <td><?= htmlspecialchars($paciente['nome']) ?></td>
                                    <td><?= htmlspecialchars($paciente['cpf']) ?></td>
                                    <td><?= htmlspecialchars($paciente['sus']) ?></td>
                                    <td><?= htmlspecialchars($paciente['prontuario']) ?></td>
                                    <td class="text-center">
                                        <a href="alterar_paciente.php?codigo=<?= $paciente['codigo'] ?>" class="btn btn-warning btn-sm btn-action">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                        <a href="excluir_paciente.php?codigo=<?= $paciente['codigo'] ?>" onclick="return confirm('Tem certeza que deseja excluir este paciente?');" class="btn btn-danger btn-sm btn-action">
                                            <i class="fas fa-trash-alt me-1"></i> Excluir
                                        </a>
                                        <button type="button" class="btn btn-info btn-sm btn-action" onclick="verNoMapa(<?= $paciente['codigo'] ?>)">
                                            <i class="fas fa-map-marker-alt me-1"></i> Mapa
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Nenhum paciente encontrado.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($pacientes) > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="badge bg-primary">
                                <i class="fas fa-users me-1"></i> Total: <?= count($pacientes) ?> pacientes
                            </span>
                        </div>
                        <a href="cad_pacientes.html" class="btn btn-success">
                            <i class="fas fa-user-plus me-1"></i> Novo Paciente
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center mt-3">
                        <a href="cad_pacientes.html" class="btn btn-success">
                            <i class="fas fa-user-plus me-1"></i> Cadastrar Novo Paciente
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="overlay" onclick="fecharModal()"></div>
<div id="modal">
    <div class="mb-3">
        <h5 class="mb-3">
            <i class="fas fa-map-marked-alt me-2"></i> Localização do Paciente
            <button type="button" class="btn-close float-end" onclick="fecharModal()" aria-label="Close"></button>
        </h5>
    </div>
    <div id="mapContainer"></div>
    <p id="endereco" class="text-center mt-3"></p>
    <div class="text-end mt-3">
        <button type="button" class="btn btn-secondary" onclick="fecharModal()">
            <i class="fas fa-times me-1"></i> Fechar
        </button>
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
<!-- Scripts HERE Maps -->
<script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<link rel="stylesheet" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />

<script>
    let platform;
    let map;
    let behavior;
    let ui;

    function verNoMapa(codigo) {
        fetch(`?codigo=${codigo}`)
            .then(response => {
                if (!response.ok) throw new Error("Erro na resposta do servidor");
                return response.json();
            })
            .then(data => {
                if (data.erro) {
                    alert("Erro: " + data.erro);
                    return;
                }

                const {endereco, numero, cidade, uf, cep} = data;
                const fullEndereco = `${endereco}, ${numero}, ${cidade} - ${uf}, ${cep}`;
                document.getElementById("endereco").innerHTML = `<i class="fas fa-map-pin me-1"></i> ${fullEndereco}`;

                mostrarMapa(fullEndereco);

                // Exibe o modal e o overlay
                document.getElementById("modal").style.display = "block";
                document.getElementById("overlay").style.display = "block";
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                alert("Ocorreu um erro ao buscar o endereço do paciente.");
            });
    }

    function fecharModal() {
        document.getElementById("modal").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }

    function mostrarMapa(endereco) {
        if (!platform) {
            platform = new H.service.Platform({
                apikey: '5ZiI__Wi2H8gQQToKOSBB_4U_9-I8umhDGPgeYJV34Q'
            });
        }

        const mapContainer = document.getElementById('mapContainer');

        if (!map) {
            const defaultLayers = platform.createDefaultLayers();
            map = new H.Map(
                mapContainer,
                defaultLayers.vector.normal.map,
                {
                    zoom: 14,
                    center: {lat: 0, lng: 0},
                    pixelRatio: window.devicePixelRatio || 1
                }
            );

            ui = H.ui.UI.createDefault(map, defaultLayers);
            behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

            window.addEventListener('resize', () => map.getViewPort().resize());
        }

        map.removeObjects(map.getObjects());

        // Mapa para o endereço
        const geocoder = platform.getSearchService();
        geocoder.geocode({
            q: endereco
        }, (result) => {
            if (result.items && result.items.length > 0) {
                const location = result.items[0].position;

                // Adicionar marcador
                const marker = new H.map.Marker(location);
                map.addObject(marker);

                // Centralizar o mapa na localização
                map.setCenter(location);
                map.setZoom(15);
            } else {
                document.getElementById("endereco").innerHTML =
                    `<div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i> Não foi possível localizar o endereço: ${endereco}
                     </div>`;
            }
        }, (error) => {
            console.error("Erro na geocodificação:", error);
            document.getElementById("endereco").innerHTML =
                `<div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i> Erro ao buscar localização
                 </div>`;
        });
    }
</script>

</body>
</html>
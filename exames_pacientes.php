<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Exames a Pacientes - Sistema de Gestão Hospitalar</title>
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
            background-color: #dc3545;
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
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
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

        /* Estilos para as sugestões */
        .sugestoes {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
            z-index: 1000;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .sugestoes li {
            padding: 10px 15px;
            cursor: pointer;
            list-style-type: none;
            border-bottom: 1px solid #f1f1f1;
            transition: all 0.2s ease;
        }

        .sugestoes li:last-child {
            border-bottom: none;
        }

        .sugestoes li:hover {
            background-color: #f8f9fa;
            padding-left: 20px;
        }

        .position-relative {
            position: relative;
        }

        /* Animação para os campos do formulário */
        .form-control, .form-select {
            transition: all 0.3s ease;
        }

        .form-control:hover, .form-select:hover {
            border-color: #0d6efd;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para mostrar sugestões ao digitar
            function configurarSugestoes(inputId, sugestoesId, idSelecionadoId, url) {
                const input = document.getElementById(inputId);
                const sugestoes = document.getElementById(sugestoesId);
                const idSelecionado = document.getElementById(idSelecionadoId);

                input.addEventListener('keyup', function() {
                    const valor = input.value;
                    if (valor.length < 2) {
                        sugestoes.style.display = 'none';
                        return;
                    }

                    // Fazer requisição AJAX para buscar sugestões
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', url, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            sugestoes.innerHTML = xhr.responseText;
                            sugestoes.style.display = 'block';

                            // Configurar eventos de clique para os itens das sugestões
                            const itens = sugestoes.querySelectorAll('li');
                            itens.forEach(function(item) {
                                item.addEventListener('click', function() {
                                    input.value = item.textContent;
                                    idSelecionado.value = item.getAttribute('data-id');
                                    sugestoes.style.display = 'none';
                                });
                            });
                        }
                    };

                    if (inputId === 'idPaciente') {
                        xhr.send('nome=' + valor);
                    } else {
                        xhr.send('exame=' + valor);
                    }
                });

                // Fechar sugestões ao clicar fora
                document.addEventListener('click', function(e) {
                    if (e.target !== input && e.target !== sugestoes) {
                        sugestoes.style.display = 'none';
                    }
                });
            }

            // Configurar sugestões para pacientes e exames
            configurarSugestoes('idPaciente', 'sugestoes', 'idSelecionado', 'buscar_sugestoes.php');
            configurarSugestoes('idExame', 'sugestoesUsuario', 'idExameSelecionado', 'buscar_exames.php');
        });
    </script>
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
                        <li><a class="dropdown-item active" href="exames_pacientes.php">Vincular Exames a Pacientes</a></li>
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
        <h1>Vincular Exames a Pacientes</h1>
        <p>Cadastre exames para pacientes e controle todo o fluxo de atendimento</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <i class="fas fa-link"></i> Formulário de Vinculação
            </div>
            <div class="form-body">
                <form class="row g-3" method="post" action="cad_examesPacientes.php">
                    <div class="col-md-6 position-relative">
                        <label for="idPaciente" class="form-label">Paciente:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input class="form-control" type="text" id="idPaciente" name="idPaciente" autocomplete="off" placeholder="Digite o nome do paciente" required>
                        </div>
                        <ul id="sugestoes" class="sugestoes"></ul>
                    </div>

                    <div class="col-md-6 position-relative">
                        <label for="idExame" class="form-label">Exame:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-flask"></i></span>
                            <input class="form-control" type="text" id="idExame" name="idExame" autocomplete="off" placeholder="Digite o nome do exame" required>
                        </div>
                        <ul id="sugestoesUsuario" class="sugestoes"></ul>
                    </div>

                    <div class="col-md-3">
                        <label for="dataPedido" class="form-label">Data do Pedido:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" class="form-control" name="dataPedido" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="dataResultado" class="form-label">Data do Resultado:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                            <input type="date" class="form-control" name="dataResultado">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="situacao" class="form-label">Situação:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                            <select class="form-select" name="situacao" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="Pronto">Pronto</option>
                                <option value="Entregue">Entregue</option>
                                <option value="Aguardando">Aguardando</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="dataEntrega" class="form-label">Data da Entrega:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-paper-plane"></i></span>
                            <input type="date" class="form-control" name="dataEntrega">
                        </div>
                    </div>

                    <input type="hidden" id="idSelecionado" name="paciente" value="">
                    <input type="hidden" id="idExameSelecionado" name="exame" value="">

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-save me-2"></i> Cadastrar Exame
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-info-circle me-2"></i> Instruções
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Digite o nome do paciente e selecione na lista de sugestões</li>
                    <li>Digite o nome do exame e selecione na lista de sugestões</li>
                    <li>Preencha a data do pedido e selecione a situação do exame</li>
                    <li>Preencha as datas de resultado e entrega quando disponíveis</li>
                    <li>Clique em "Cadastrar Exame" para finalizar</li>
                </ol>
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
</body>
</html>

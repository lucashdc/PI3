<?php
require_once 'db_connection.php';

if (isset($_GET['codigo'])) {
    $codPaciente = $_GET['codigo'];

    $sql = "SELECT * FROM PICadPacientes WHERE codigo = $codPaciente";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $paciente = $result->fetch_assoc();
    } else {
        echo "Paciente não encontrado.";
        exit;
    }
} else {
    echo "ID do paciente não especificado.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $dataNasc = $_POST['dataNasc'];
    $genero = $_POST['genero'];
    $cpf = $_POST['cpf'];
    $sus = $_POST['sus'];
    $prontuario = $_POST['prontuario'];
    $cidadeNasc = $_POST['cidadeNasc'];
    $paisNasc = $_POST['paisNasc'];
    $nomeMae = $_POST['nomeMae'];
    $nomePai = $_POST['nomePai'];
    $unidadeSaude = $_POST['unidadeSaude'];
    $cep = $_POST['cep'];
    $endereco = $_POST['endereco'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $complemento = $_POST['complemento'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];
    $referencia = $_POST['referencia'];
    $telefone = $_POST['telefone'];
    $celular = $_POST['celular'];
    $ufNasc = $_POST['ufNasc'];

    $sql = "UPDATE PICadPacientes SET 
                nome = '$nome', 
                dataNasc = '$dataNasc',
                genero = '$genero',
                cpf = '$cpf',
                sus = '$sus',
                prontuario = '$prontuario',
                cidadeNasc = '$cidadeNasc',
                paisNasc = '$paisNasc',
                nomeMae = '$nomeMae',
                nomePai = '$nomePai',
                unidadeSaude = '$unidadeSaude',
                cep = '$cep',
                endereco = '$endereco',
                numero = '$numero',
                bairro = '$bairro',
                complemento = '$complemento',
                cidade = '$cidade',
                uf = '$uf',
                referencia = '$referencia',
                telefone = '$telefone',
                celular = '$celular',
                ufNasc = '$ufNasc'
            WHERE codigo = $codPaciente";

    if ($conn->query($sql) === TRUE) {
        header("Location: lista_pacientes.php");
        exit;
    } else {
        echo "Erro ao atualizar o cadastro do Paciente: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Paciente - Sistema de Gestão Hospitalar</title>
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
            background-color: #0d6efd;
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

        .form-control, .form-select {
            transition: all 0.3s ease;
        }

        .form-control:hover, .form-select:hover {
            border-color: #0d6efd;
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
        <h1>Alteração de Cadastro de Paciente</h1>
        <p>Atualize os dados do paciente conforme necessário</p>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <form method="post" action="">
            <!-- Dados Pessoais -->
            <div class="form-container">
                <div class="form-header">
                    <i class="fas fa-user-circle"></i> Dados Pessoais
                </div>
                <div class="form-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nomeCompleto" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nomeCompleto" name="nome"
                                   placeholder="Digite o nome completo do paciente" value="<?= htmlspecialchars($paciente['nome']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="dataNascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="dataNascimento" name="dataNasc"
                                   value="<?= htmlspecialchars($paciente['dataNasc']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select id="sexo" name="genero" class="form-select" required>
                                <option value="M" <?= $paciente['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= $paciente['genero'] == 'F' ? 'selected' : '' ?>>Feminino</option>
                                <option value="O" <?= $paciente['genero'] == 'O' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nomeMae" class="form-label">Nome da Mãe</label>
                            <input type="text" class="form-control" id="nomeMae" name="nomeMae"
                                   placeholder="Digite o nome da mãe" value="<?= htmlspecialchars($paciente['nomeMae']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="nomePai" class="form-label">Nome do Pai</label>
                            <input type="text" class="form-control" id="nomePai" name="nomePai"
                                   placeholder="Digite o nome do pai" value="<?= htmlspecialchars($paciente['nomePai']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00"
                                   value="<?= htmlspecialchars($paciente['cpf']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="numSUS" class="form-label">Nº do SUS</label>
                            <input type="text" class="form-control" id="numSUS" name="sus"
                                   placeholder="Digite o número do SUS" maxlength="16" value="<?= htmlspecialchars($paciente['sus']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="numProntuario" class="form-label">Nº de Prontuário</label>
                            <input type="text" class="form-control" id="numProntuario" name="prontuario"
                                   placeholder="Digite o número de prontuário" maxlength="6" value="<?= htmlspecialchars($paciente['prontuario']) ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="municipioNascimento" class="form-label">Município de Nascimento</label>
                            <input type="text" class="form-control" id="municipioNascimento" name="cidadeNasc"
                                   placeholder="Digite o município de nascimento" value="<?= htmlspecialchars($paciente['cidadeNasc']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="uf1" class="form-label">UF</label>
                            <select id="uf1" name="ufNasc" class="form-select">
                                <option value="AC" <?= $paciente['ufNasc'] == 'AC' ? 'selected' : '' ?>>AC</option>
                                <option value="AL" <?= $paciente['ufNasc'] == 'AL' ? 'selected' : '' ?>>AL</option>
                                <option value="AM" <?= $paciente['ufNasc'] == 'AM' ? 'selected' : '' ?>>AM</option>
                                <option value="AP" <?= $paciente['ufNasc'] == 'AP' ? 'selected' : '' ?>>AP</option>
                                <option value="BA" <?= $paciente['ufNasc'] == 'BA' ? 'selected' : '' ?>>BA</option>
                                <option value="CE" <?= $paciente['ufNasc'] == 'CE' ? 'selected' : '' ?>>CE</option>
                                <option value="DF" <?= $paciente['ufNasc'] == 'DF' ? 'selected' : '' ?>>DF</option>
                                <option value="ES" <?= $paciente['ufNasc'] == 'ES' ? 'selected' : '' ?>>ES</option>
                                <option value="GO" <?= $paciente['ufNasc'] == 'GO' ? 'selected' : '' ?>>GO</option>
                                <option value="MA" <?= $paciente['ufNasc'] == 'MA' ? 'selected' : '' ?>>MA</option>
                                <option value="MG" <?= $paciente['ufNasc'] == 'MG' ? 'selected' : '' ?>>MG</option>
                                <option value="MS" <?= $paciente['ufNasc'] == 'MS' ? 'selected' : '' ?>>MS</option>
                                <option value="MT" <?= $paciente['ufNasc'] == 'MT' ? 'selected' : '' ?>>MT</option>
                                <option value="PA" <?= $paciente['ufNasc'] == 'PA' ? 'selected' : '' ?>>PA</option>
                                <option value="PB" <?= $paciente['ufNasc'] == 'PB' ? 'selected' : '' ?>>PB</option>
                                <option value="PE" <?= $paciente['ufNasc'] == 'PE' ? 'selected' : '' ?>>PE</option>
                                <option value="PI" <?= $paciente['ufNasc'] == 'PI' ? 'selected' : '' ?>>PI</option>
                                <option value="PR" <?= $paciente['ufNasc'] == 'PR' ? 'selected' : '' ?>>PR</option>
                                <option value="RJ" <?= $paciente['ufNasc'] == 'RJ' ? 'selected' : '' ?>>RJ</option>
                                <option value="RN" <?= $paciente['ufNasc'] == 'RN' ? 'selected' : '' ?>>RN</option>
                                <option value="RO" <?= $paciente['ufNasc'] == 'RO' ? 'selected' : '' ?>>RO</option>
                                <option value="RR" <?= $paciente['ufNasc'] == 'RR' ? 'selected' : '' ?>>RR</option>
                                <option value="RS" <?= $paciente['ufNasc'] == 'RS' ? 'selected' : '' ?>>RS</option>
                                <option value="SC" <?= $paciente['ufNasc'] == 'SC' ? 'selected' : '' ?>>SC</option>
                                <option value="SE" <?= $paciente['ufNasc'] == 'SE' ? 'selected' : '' ?>>SE</option>
                                <option value="SP" <?= $paciente['ufNasc'] == 'SP' ? 'selected' : '' ?>>SP</option>
                                <option value="TO" <?= $paciente['ufNasc'] == 'TO' ? 'selected' : '' ?>>TO</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="paisNascimento" class="form-label">País de Nascimento</label>
                            <input type="text" class="form-control" id="paisNascimento" name="paisNasc"
                                   placeholder="Digite o país de nascimento" value="<?= htmlspecialchars($paciente['paisNasc']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Endereço e Contato -->
            <div class="form-container">
                <div class="form-header">
                    <i class="fas fa-map-marker-alt"></i> Endereço e Contato
                </div>
                <div class="form-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="unidadeSaude" class="form-label">Unidade de Saúde</label>
                            <input type="text" class="form-control" id="unidadeSaude" name="unidadeSaude"
                                   placeholder="Digite a unidade de saúde" value="<?= htmlspecialchars($paciente['unidadeSaude']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000"
                                   value="<?= htmlspecialchars($paciente['cep']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="numeroCasa" class="form-label">Nº da Casa</label>
                            <input type="text" class="form-control" id="numeroCasa" name="numero"
                                   placeholder="Nº" value="<?= htmlspecialchars($paciente['numero']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rua" class="form-label">Rua</label>
                            <input type="text" class="form-control" id="rua" name="endereco" placeholder="Digite a rua"
                                   readonly value="<?= htmlspecialchars($paciente['endereco']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Digite o bairro"
                                   readonly value="<?= htmlspecialchars($paciente['bairro']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento"
                                   placeholder="Digite o complemento" value="<?= htmlspecialchars($paciente['complemento']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Digite a Cidade"
                                   readonly value="<?= htmlspecialchars($paciente['cidade']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="uf2" class="form-label">UF</label>
                            <select id="uf2" name="uf" class="form-select">
                                <option value="AC" <?= $paciente['uf'] == 'AC' ? 'selected' : '' ?>>AC</option>
                                <option value="AL" <?= $paciente['uf'] == 'AL' ? 'selected' : '' ?>>AL</option>
                                <option value="AM" <?= $paciente['uf'] == 'AM' ? 'selected' : '' ?>>AM</option>
                                <option value="AP" <?= $paciente['uf'] == 'AP' ? 'selected' : '' ?>>AP</option>
                                <option value="BA" <?= $paciente['uf'] == 'BA' ? 'selected' : '' ?>>BA</option>
                                <option value="CE" <?= $paciente['uf'] == 'CE' ? 'selected' : '' ?>>CE</option>
                                <option value="DF" <?= $paciente['uf'] == 'DF' ? 'selected' : '' ?>>DF</option>
                                <option value="ES" <?= $paciente['uf'] == 'ES' ? 'selected' : '' ?>>ES</option>
                                <option value="GO" <?= $paciente['uf'] == 'GO' ? 'selected' : '' ?>>GO</option>
                                <option value="MA" <?= $paciente['uf'] == 'MA' ? 'selected' : '' ?>>MA</option>
                                <option value="MG" <?= $paciente['uf'] == 'MG' ? 'selected' : '' ?>>MG</option>
                                <option value="MS" <?= $paciente['uf'] == 'MS' ? 'selected' : '' ?>>MS</option>
                                <option value="MT" <?= $paciente['uf'] == 'MT' ? 'selected' : '' ?>>MT</option>
                                <option value="PA" <?= $paciente['uf'] == 'PA' ? 'selected' : '' ?>>PA</option>
                                <option value="PB" <?= $paciente['uf'] == 'PB' ? 'selected' : '' ?>>PB</option>
                                <option value="PE" <?= $paciente['uf'] == 'PE' ? 'selected' : '' ?>>PE</option>
                                <option value="PI" <?= $paciente['uf'] == 'PI' ? 'selected' : '' ?>>PI</option>
                                <option value="PR" <?= $paciente['uf'] == 'PR' ? 'selected' : '' ?>>PR</option>
                                <option value="RJ" <?= $paciente['uf'] == 'RJ' ? 'selected' : '' ?>>RJ</option>
                                <option value="RN" <?= $paciente['uf'] == 'RN' ? 'selected' : '' ?>>RN</option>
                                <option value="RO" <?= $paciente['uf'] == 'RO' ? 'selected' : '' ?>>RO</option>
                                <option value="RR" <?= $paciente['uf'] == 'RR' ? 'selected' : '' ?>>RR</option>
                                <option value="RS" <?= $paciente['uf'] == 'RS' ? 'selected' : '' ?>>RS</option>
                                <option value="SC" <?= $paciente['uf'] == 'SC' ? 'selected' : '' ?>>SC</option>
                                <option value="SE" <?= $paciente['uf'] == 'SE' ? 'selected' : '' ?>>SE</option>
                                <option value="SP" <?= $paciente['uf'] == 'SP' ? 'selected' : '' ?>>SP</option>
                                <option value="TO" <?= $paciente['uf'] == 'TO' ? 'selected' : '' ?>>TO</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="referencia" class="form-label">Referência</label>
                            <input type="text" class="form-control" id="referencia" name="referencia"
                                   placeholder="Digite uma referência" value="<?= htmlspecialchars($paciente['referencia']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="telefone" class="form-label">Telefone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="telefone" name="telefone"
                                       placeholder="(00) 0000-0000" value="<?= htmlspecialchars($paciente['telefone']) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="celular" class="form-label">Celular</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                <input type="text" class="form-control" id="celular" name="celular"
                                       placeholder="(00) 00000-0000" value="<?= htmlspecialchars($paciente['celular']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center mb-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i> Atualizar
                </button>
                <a href="lista_pacientes.php" class="btn btn-secondary btn-lg px-5">
                    <i class="fas fa-times me-2"></i> Cancelar
                </a>
            </div>
        </form>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function () {
        $('#cpf').mask('000.000.000-00');
        $('#numSUS').mask('000000000000000');
        $('#numProntuario').mask('000000');
        $('#cep').mask('00000-000');
        $('#telefone').mask('(00) 0000-0000');
        $('#celular').mask('(00) 00000-0000');

        $('#cep').on('blur', function () {
            let cep = $(this).val().replace('-', '');
            if (cep.length === 8) {
                $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function (data) {
                    if (!("erro" in data)) {
                        $('#rua').val(data.logradouro);
                        $('#bairro').val(data.bairro);
                        $('#uf2').val(data.uf);
                        $('#cidade').val(data.localidade);
                    } else {
                        alert("CEP não encontrado.");
                    }
                }).fail(function () {
                    alert("Erro ao consultar o CEP.");
                });
            } else if (cep.length > 0) {
                alert("CEP inválido. Deve conter 8 dígitos.");
            }
        });

        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if( target.length ) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
    });
</script>

</body>
</html>
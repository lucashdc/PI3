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
    <title>Cadastro de Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }

        .card-header {
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="d-flex justify-content-between w-100">
            <a class="navbar-brand" href="inicio.html">Sistema de Gestão Hospitalar</a>
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
                            <li><a class="dropdown-item" href="exames_pacientes.php">Vincular Exames a Pacientes</a>
                            </li>
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

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Cadastro de Pacientes</h2>
    <form method="post" action="">

        <div class="card mb-4">
            <div class="card-header">Dados Pessoais</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nomeCompleto" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nomeCompleto" name="nome"
                               placeholder="Digite seu nome completo"
                               value="<?= htmlspecialchars($paciente['nome']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="dataNascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="dataNascimento" name="dataNasc"
                               value="<?= htmlspecialchars($paciente['dataNasc']) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sexo" class="form-label">Sexo</label>
                        <select id="sexo" name="genero" class="form-select">
                            <option value="M" <?= $paciente['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $paciente['genero'] == 'F' ? 'selected' : '' ?>>Feminino</option>
                            <option value="O" <?= $paciente['genero'] == 'O' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nomePai" class="form-label">Nome do Pai</label>
                        <input type="text" class="form-control" id="nomePai" name="nomePai"
                               placeholder="Digite o nome do pai"  value="<?= htmlspecialchars($paciente['nomePai']) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nomeMae" class="form-label">Nome da Mãe</label>
                        <input type="text" class="form-control" id="nomeMae" name="nomeMae"
                               placeholder="Digite o nome da mãe"  value="<?= htmlspecialchars($paciente['nomeMae']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite seu CPF"
                               value="<?= htmlspecialchars($paciente['cpf']) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="numSUS" class="form-label">Nº do SUS</label>
                        <input type="text" class="form-control" id="numSUS" name="sus"
                               placeholder="Digite o número do SUS" maxlength="16"
                               value="<?= htmlspecialchars($paciente['sus']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="numProntuario" class="form-label">Nº de Prontuário</label>
                        <input type="text" class="form-control" id="numPront" name="prontuario"
                               placeholder="Digite o número de prontuário" maxlength="6"
                               value="<?= htmlspecialchars($paciente['prontuario']) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label for="municipioNascimento" class="form-label">Município de Nascimento</label>
                        <input type="text" class="form-control" id="municipioNascimento" name="cidadeNasc"
                               placeholder="Digite o município de nascimento"  value="<?= htmlspecialchars($paciente['cidadeNasc']) ?>">
                    </div>
                    <div class="col-md-1">
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
                    <div class="col-md-6">
                        <label for="paisNascimento" class="form-label">País de Nascimento</label>
                        <input type="text" class="form-control" id="paisNascimento" name="paisNasc"
                               placeholder="Digite o país de nascimento" value="<?= htmlspecialchars($paciente['paisNasc']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Logradouro</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="unidadeSaude" class="form-label">Unidade de Saúde</label>
                        <input type="text" class="form-control" id="unidadeSaude" name="unidadeSaude"
                               placeholder="Digite a unidade de saúde" value="<?= htmlspecialchars($paciente['unidadeSaude']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" name="cep" placeholder="Digite o CEP" value="<?= htmlspecialchars($paciente['cep']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" class="form-control" id="rua" name="endereco" placeholder="Digite a rua" readonly value="<?= htmlspecialchars($paciente['endereco']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="numeroCasa" class="form-label">Nº da Casa</label>
                        <input type="text" class="form-control" id="numeroCasa" name="numero"
                               placeholder="Digite o número da casa" value="<?= htmlspecialchars($paciente['numero']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Digite o bairro" readonly value="<?= htmlspecialchars($paciente['bairro']) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento"
                               placeholder="Digite o complemento" value="<?= htmlspecialchars($paciente['complemento']) ?>">
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
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Digite a Cidade" readonly value="<?= htmlspecialchars($paciente['cidade']) ?>">
                    </div>
                </div>
                <div class="row mb-3">

                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="referencia" class="form-label">Referência</label>
                        <input type="text" class="form-control" id="referencia" name="referencia"
                               placeholder="Digite uma referência" value="<?= htmlspecialchars($paciente['referencia']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone"
                               placeholder="Digite o telefone" value="<?= htmlspecialchars($paciente['telefone']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular"
                               placeholder="Digite o celular" value="<?= htmlspecialchars($paciente['celular']) ?>">
                    </div>
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary mx-2">Cadastrar</button>
            <button type="reset" class="btn btn-secondary mx-2">Cancelar</button>
        </div>
    </form>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script>
    $(document).ready(function () {
        $('#cpf').mask('000.000.000-00');
        $('#numSUS').mask('000000000000000');
        $('#numPront').mask('000000');
        $('#cep').mask('00000-000');
        $('#telefone').mask('(00) 0000-0000');
        $('#celular').mask('(00) 0 0000-0000');


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
            } else {
                alert("CEP inválido.");
            }
        });
    });
</script>


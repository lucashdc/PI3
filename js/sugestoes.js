$(document).ready(function () {
    // Manipulador de eventos para sugestões de pacientes
    $("#idPaciente").on("input", function () {
        var nome = $(this).val();
        if (nome.length > 2) {
            $.ajax({
                url: "buscar_sugestoes.php",
                method: "POST",
                data: {nome: nome},
                success: function (data) {
                    $("#sugestoes").html(data).slideDown();
                }
            });
        } else {
            $("#sugestoes").slideUp();
        }
    });

    // Selecionar paciente da lista de sugestões
    $(document).on("click", "#sugestoes li", function () {
        var id = $(this).data("id");
        var texto = $(this).text();
        $("#idPaciente").val(texto.split(" - ")[1]); // Atualiza o campo idPaciente com o nome selecionado
        $("#idSelecionado").val(id); // Atualiza o campo oculto com o ID do paciente selecionado
        $("#sugestoes").slideUp();
    });

    // Manipulador de eventos para sugestões de exames
    $("#idExame").on("input", function () {
        var exame = $(this).val();
        if (exame.length > 2) {
            $.ajax({
                url: "buscar_exames.php", // Atualizado para buscar_exames.php
                method: "POST",
                data: {exame: exame}, // Alterado para buscar por 'exame'
                success: function (data) {
                    $("#sugestoesUsuario").html(data).slideDown();
                }
            });
        } else {
            $("#sugestoesUsuario").slideUp();
        }
    });

    // Selecionar exame da lista de sugestões
    $(document).on("click", "#sugestoesUsuario li", function (e) {
        e.stopPropagation(); // Prevenir propagação do evento
        var idExame = $(this).data("id");
        var nomeExame = $(this).text();
        $("#idExame").val(nomeExame);
        $("#idExameSelecionado").val(idExame); // Atualiza o campo oculto com o ID do exame selecionado
        $("#sugestoesUsuario").slideUp();
    });

    // Esconde as listas de sugestões ao clicar fora
    $(document).on("click", function (e) {
        if (!$(e.target).closest('#idPaciente').length) {
            $('#sugestoes').slideUp();
        }
        if (!$(e.target).closest('#idExame').length) {
            $('#sugestoesUsuario').slideUp();
        }
    });
});

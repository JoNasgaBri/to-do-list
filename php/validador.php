<?php

// 1. Verifica se os dados foram enviados via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dadosDoFormulario = $_POST;

    $temCampoVazio = false;

    // 2. Validação: Itera sobre cada dado recebido
    foreach ($dadosDoFormulario as $campo => $valor) {
        if (empty($valor)) {
            $temCampoVazio = true;
            break; 
    }

    // 3. Resposta ao usuário
    if ($temCampoVazio) {
        echo '<h1>Erro: Por favor, preencha todos os campos!</h1><a href="javascript:history.back()">Voltar</a>';
    } else {
        echo '<h1>Sucesso: Formulário recebido e validado com sucesso!</h1><a href="javascript:history.back()">Voltar</a>';
    }

} else {
    echo '<h1>Acesso negado.</h1>';
}

?>
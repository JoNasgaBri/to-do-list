document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form-cadastro");

    // Evento de Cadastro
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const nome = document.getElementById("nome").value.trim();
        const email = document.getElementById("email").value.trim();
        const senha = document.getElementById("senha").value;
        const confirmar_senha = document.getElementById("confirmar_senha").value;
        const data_nasc = document.getElementById("data_nasc").value;

        // ========== VALIDAÇÕES NO FRONTEND ==========

        // Validar Nome (mínimo 3 caracteres, apenas letras e espaços)
        if (nome.length < 3) {
            alert("O nome deve ter pelo menos 3 caracteres!");
            document.getElementById("nome").focus();
            return;
        }
        if (!/^[A-Za-zÀ-ÿ\s]+$/.test(nome)) {
            alert("O nome deve conter apenas letras e espaços!");
            document.getElementById("nome").focus();
            return;
        }

        // Validar Email (formato válido)
        if (!email) {
            alert("O email é obrigatório!");
            document.getElementById("email").focus();
            return;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Digite um email válido! (exemplo: usuario@email.com)");
            document.getElementById("email").focus();
            return;
        }

        // Validar Data de Nascimento
        if (!data_nasc) {
            alert("A data de nascimento é obrigatória!");
            document.getElementById("data_nasc").focus();
            return;
        }
        
        // Verificar se a data é válida e se a pessoa tem pelo menos 10 anos
        const dataNasc = new Date(data_nasc);
        const hoje = new Date();
        const idade = Math.floor((hoje - dataNasc) / (365.25 * 24 * 60 * 60 * 1000));
        
        if (dataNasc >= hoje) {
            alert("A data de nascimento deve ser anterior a hoje!");
            document.getElementById("data_nasc").focus();
            return;
        }
        if (idade < 10) {
            alert("Você deve ter pelo menos 10 anos para se cadastrar!");
            document.getElementById("data_nasc").focus();
            return;
        }
        if (idade > 120) {
            alert("Data de nascimento inválida!");
            document.getElementById("data_nasc").focus();
            return;
        }

        // Validar Senha
        if (senha.length < 4) {
            alert("A senha deve ter pelo menos 4 caracteres!");
            document.getElementById("senha").focus();
            return;
        }

        // Validar confirmação de senha
        if (senha !== confirmar_senha) {
            alert("As senhas não coincidem!");
            document.getElementById("confirmar_senha").focus();
            return;
        }

        // ========== ENVIAR DADOS ==========
        const dados = { 
            nome: nome, 
            email: email, 
            senha: senha,
            confirmar_senha: confirmar_senha,
            data_nasc: data_nasc
        };

        // Mostrar loading
        const btnSubmit = form.querySelector('button[type="submit"]');
        const textoOriginal = btnSubmit.textContent;
        btnSubmit.textContent = "Cadastrando...";
        btnSubmit.disabled = true;

        fetch('../php/api_cadastro.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert(data.mensagem);
                // Redirecionar para login
                window.location.href = "login.html";
            } else {
                alert("Erro: " + data.erro);
                btnSubmit.textContent = textoOriginal;
                btnSubmit.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert("Erro ao comunicar com o servidor");
            btnSubmit.textContent = textoOriginal;
            btnSubmit.disabled = false;
        });
    });
});

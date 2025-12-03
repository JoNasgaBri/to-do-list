document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form-login");
    
    // Verificar se já está logado (redireciona se sim)
    verificarLoginLocal();

    // Evento de Login
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const email = document.getElementById("email").value;
        const senha = document.getElementById("senha").value;

        const dados = { email: email, senha: senha };

        // Mostrar loading
        const btnSubmit = form.querySelector('button[type="submit"]');
        const textoOriginal = btnSubmit.textContent;
        btnSubmit.textContent = "Entrando...";
        btnSubmit.disabled = true;

        fetch('../php/api_login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("Bem-vindo, " + data.usuario.nome + "!");
                // Redirecionar para página inicial
                window.location.href = "../index.html";
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

// Verificar se usuário já está logado (apenas para página de login)
function verificarLoginLocal() {
    fetch('../php/api_login.php')
        .then(response => response.json())
        .then(data => {
            if (data.logado) {
                // Usuário já está logado, redirecionar para home
                window.location.href = "../index.html";
            }
        })
        .catch(error => {
            console.error('Erro ao verificar login:', error);
        });
}

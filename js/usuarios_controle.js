document.addEventListener("DOMContentLoaded", function () {
    listarUsuarios();

    const form = document.getElementById("form-usuario");
    const btnPesquisar = document.getElementById("btn-pesquisar");
    const inputPesquisa = document.getElementById("pesquisa-usuario");

    // Evento de Salvar (Adicionar ou Editar)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("id_usuario").value;
        const nome = document.getElementById("nome_usuario").value;
        const email = document.getElementById("email_usuario").value;
        const senha = document.getElementById("senha_usuario").value;
        const confirmarSenha = document.getElementById("confirmar_senha_usuario").value;

        // Validar senhas
        if (senha && senha !== confirmarSenha) {
            alert("As senhas não coincidem!");
            return;
        }

        // Se é edição e senha está vazia, não envia senha (mantém a atual)
        const dados = { 
            id: id, 
            nome: nome, 
            email: email
        };
        
        if (senha) {
            dados.senha = senha;
        }

        fetch('../php/api_usuarios.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert(id ? "Usuário atualizado com sucesso!" : "Usuário cadastrado com sucesso!");
                limparFormulario();
                listarUsuarios();
            } else {
                alert("Erro: " + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert("Erro ao comunicar com o servidor");
        });
    });

    // Evento de Pesquisa
    btnPesquisar.addEventListener("click", function () {
        const termo = inputPesquisa.value;
        listarUsuarios(termo);
    });

    // Pesquisar ao pressionar Enter
    inputPesquisa.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const termo = inputPesquisa.value;
            listarUsuarios(termo);
        }
    });

    // Botão limpar pesquisa
    const btnLimpar = document.getElementById("btn-limpar-pesquisa");
    if (btnLimpar) {
        btnLimpar.addEventListener("click", function () {
            inputPesquisa.value = "";
            listarUsuarios();
        });
    }
});

// Função para limpar o formulário
function limparFormulario() {
    document.getElementById("form-usuario").reset();
    document.getElementById("id_usuario").value = "";
    document.getElementById("form-titulo").textContent = "Cadastrar Novo Usuário";
    document.getElementById("btn-salvar").textContent = "Cadastrar Usuário";
    document.getElementById("senha_usuario").required = true;
    document.getElementById("confirmar_senha_usuario").required = true;
    document.getElementById("aviso-senha").style.display = "none";
}

// Função para buscar dados do PHP e montar a tabela
function listarUsuarios(pesquisa = "") {
    let url = '../php/api_usuarios.php';
    if (pesquisa) {
        url += '?pesquisa=' + encodeURIComponent(pesquisa);
    }

    fetch(url)
        .then(response => response.json())
        .then(usuarios => {
            const tbody = document.getElementById("tabela-usuarios-corpo");
            tbody.innerHTML = "";

            if (usuarios.length === 0) {
                tbody.innerHTML = "<tr><td colspan='5'>Nenhum usuário encontrado.</td></tr>";
                return;
            }

            usuarios.forEach(user => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.nome}</td>
                    <td>${user.email}</td>
                    <td>${user.data_cadastro_formatada}</td>
                    <td>
                        <button class="btn btn-edit" onclick="editarUsuario(${user.id}, '${escapeHtml(user.nome)}', '${escapeHtml(user.email)}')">Editar</button>
                        <button class="btn btn-delete" onclick="excluirUsuario(${user.id}, '${escapeHtml(user.nome)}')">Excluir</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar usuários:', error);
        });
}

// Função para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'");
}

// Preenche o formulário para edição
function editarUsuario(id, nome, email) {
    document.getElementById("id_usuario").value = id;
    document.getElementById("nome_usuario").value = nome;
    document.getElementById("email_usuario").value = email;
    document.getElementById("senha_usuario").value = "";
    document.getElementById("confirmar_senha_usuario").value = "";
    
    // Senha não é obrigatória na edição
    document.getElementById("senha_usuario").required = false;
    document.getElementById("confirmar_senha_usuario").required = false;
    document.getElementById("aviso-senha").style.display = "block";
    
    document.getElementById("form-titulo").textContent = "Editar Usuário";
    document.getElementById("btn-salvar").textContent = "Atualizar Usuário";
    window.scrollTo(0, 0);
}

// Função de excluir
function excluirUsuario(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o usuário "${nome}"?\n\nEsta ação não pode ser desfeita!`)) {
        fetch(`../php/api_usuarios.php?acao=excluir&id=${id}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("Usuário excluído com sucesso!");
                listarUsuarios();
            } else {
                alert("Erro ao excluir: " + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert("Erro ao comunicar com o servidor");
        });
    }
}

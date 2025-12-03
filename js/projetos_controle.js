document.addEventListener("DOMContentLoaded", function () {
    listarProjetos();

    const form = document.getElementById("form-projeto");

    // Evento de Salvar (Adicionar ou Editar)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("id_projeto").value;
        const nome = document.getElementById("nome_projeto").value;
        const descricao = document.getElementById("descricao_projeto").value;

        const dados = { id: id, nome: nome, descricao: descricao };

        fetch('../php/api_projetos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert(id ? "Projeto atualizado com sucesso!" : "Projeto salvo com sucesso!");
                limparFormulario();
                listarProjetos();
            } else {
                alert("Erro: " + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert("Erro ao comunicar com o servidor");
        });
    });
});

// Função para limpar o formulário
function limparFormulario() {
    document.getElementById("form-projeto").reset();
    document.getElementById("id_projeto").value = "";
    document.getElementById("form-titulo-projeto").textContent = "Adicionar Novo Projeto";
    document.getElementById("btn-salvar-projeto").textContent = "Salvar Projeto";
}

// Função para buscar dados do PHP e montar a tabela
function listarProjetos() {
    fetch('../php/api_projetos.php')
        .then(response => response.json())
        .then(projetos => {
            const tbody = document.getElementById("tabela-projetos-corpo");
            tbody.innerHTML = "";

            if (projetos.length === 0) {
                tbody.innerHTML = "<tr><td colspan='4'>Nenhum projeto cadastrado.</td></tr>";
                return;
            }

            projetos.forEach(proj => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${proj.nome}</td>
                    <td>${proj.descricao || '-'}</td>
                    <td>${proj.data_criacao_formatada}</td>
                    <td>
                        <button class="btn btn-edit" onclick="editarProjeto(${proj.id}, '${escapeHtml(proj.nome)}', '${escapeHtml(proj.descricao || '')}')">Editar</button>
                        <button class="btn btn-delete" onclick="excluirProjeto(${proj.id}, '${escapeHtml(proj.nome)}')">Excluir</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar projetos:', error);
        });
}

// Função para escapar HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'").replace(/\n/g, "\\n");
}

// Preenche o formulário para edição
function editarProjeto(id, nome, descricao) {
    document.getElementById("id_projeto").value = id;
    document.getElementById("nome_projeto").value = nome;
    document.getElementById("descricao_projeto").value = descricao;
    
    document.getElementById("form-titulo-projeto").textContent = "Editar Projeto";
    document.getElementById("btn-salvar-projeto").textContent = "Atualizar Projeto";
    window.scrollTo(0, 0);
}

// Função de excluir
function excluirProjeto(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o projeto "${nome}"?\n\nTodas as tarefas associadas também serão excluídas!`)) {
        fetch(`../php/api_projetos.php?acao=excluir&id=${id}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("Projeto excluído com sucesso!");
                listarProjetos();
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

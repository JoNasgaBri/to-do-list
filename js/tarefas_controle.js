document.addEventListener("DOMContentLoaded", function () {
    carregarProjetos();
    listarTarefas();

    const form = document.getElementById("form-tarefa");

    // Evento de Salvar (Adicionar ou Editar)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("id_tarefa").value;
        const titulo = document.getElementById("titulo_tarefa").value;
        const descricao = document.getElementById("descricao_tarefa").value;
        const projeto_id = document.getElementById("projeto_id").value;
        const data_limite = document.getElementById("data_limite").value;
        const status = document.getElementById("status").value;

        const dados = { 
            id: id, 
            titulo: titulo, 
            descricao: descricao,
            projeto_id: projeto_id,
            data_limite: data_limite,
            status: status
        };

        fetch('../php/api_tarefas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert(id ? "Tarefa atualizada com sucesso!" : "Tarefa salva com sucesso!");
                limparFormulario();
                listarTarefas();
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

// Carregar projetos no select
function carregarProjetos() {
    fetch('../php/api_tarefas.php?tipo=projetos')
        .then(response => response.json())
        .then(projetos => {
            const select = document.getElementById("projeto_id");
            select.innerHTML = '<option value="">Selecione um projeto</option>';
            
            projetos.forEach(proj => {
                const option = document.createElement("option");
                option.value = proj.id;
                option.textContent = proj.nome;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar projetos:', error);
        });
}

// Função para limpar o formulário
function limparFormulario() {
    document.getElementById("form-tarefa").reset();
    document.getElementById("id_tarefa").value = "";
    document.getElementById("form-titulo-tarefa").textContent = "Adicionar Nova Tarefa";
    document.getElementById("btn-salvar-tarefa").textContent = "Salvar Tarefa";
}

// Função para buscar dados do PHP e montar a tabela
function listarTarefas() {
    fetch('../php/api_tarefas.php')
        .then(response => response.json())
        .then(tarefas => {
            const tbody = document.getElementById("tabela-tarefas-corpo");
            tbody.innerHTML = "";

            if (tarefas.length === 0) {
                tbody.innerHTML = "<tr><td colspan='5'>Nenhuma tarefa cadastrada.</td></tr>";
                return;
            }

            tarefas.forEach(tarefa => {
                const tr = document.createElement("tr");
                
                // Adicionar classe se vencida ou próxima
                if (tarefa.vencida) {
                    tr.classList.add('tarefa-vencida');
                } else if (tarefa.proxima) {
                    tr.classList.add('tarefa-proxima-vencimento');
                }

                // Montar alerta de vencimento
                let alertaVencimento = '';
                if (tarefa.vencida) {
                    alertaVencimento = '<span class="alerta-vencida">⚠️ VENCIDA</span>';
                } else if (tarefa.proxima) {
                    alertaVencimento = '<span class="alerta-proxima">🔔 PRÓXIMA</span>';
                }

                // Montar info de dias
                let infoDias = '';
                if (tarefa.dias_restantes !== null) {
                    if (tarefa.dias_restantes < 0) {
                        infoDias = `<br><small>(${Math.abs(tarefa.dias_restantes)} dias atrás)</small>`;
                    } else if (tarefa.dias_restantes === 0) {
                        infoDias = '<br><small>(Hoje)</small>';
                    } else {
                        infoDias = `<br><small>(${tarefa.dias_restantes} dias restantes)</small>`;
                    }
                }

                // Formatar status com classe
                const statusClass = {
                    'Pendente': 'status-pendente',
                    'Em Andamento': 'status-andamento',
                    'Concluída': 'status-concluida'
                };

                tr.innerHTML = `
                    <td>
                        ${escapeHtml(tarefa.titulo)}
                        ${alertaVencimento}
                    </td>
                    <td>${escapeHtml(tarefa.nome_projeto)}</td>
                    <td>${tarefa.data_limite_formatada}${infoDias}</td>
                    <td><span class="${statusClass[tarefa.status] || ''}">${tarefa.status}</span></td>
                    <td>
                        <button class="btn btn-edit" onclick="editarTarefa(${tarefa.id}, '${escapeHtml(tarefa.titulo)}', '${escapeHtml(tarefa.descricao || '')}', ${tarefa.projeto_id}, '${tarefa.data_limite || ''}', '${tarefa.status}')">Editar</button>
                        <button class="btn btn-delete" onclick="excluirTarefa(${tarefa.id}, '${escapeHtml(tarefa.titulo)}')">Excluir</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar tarefas:', error);
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
function editarTarefa(id, titulo, descricao, projeto_id, data_limite, status) {
    document.getElementById("id_tarefa").value = id;
    document.getElementById("titulo_tarefa").value = titulo;
    document.getElementById("descricao_tarefa").value = descricao;
    document.getElementById("projeto_id").value = projeto_id;
    document.getElementById("data_limite").value = data_limite;
    document.getElementById("status").value = status;
    
    document.getElementById("form-titulo-tarefa").textContent = "Editar Tarefa";
    document.getElementById("btn-salvar-tarefa").textContent = "Atualizar Tarefa";
    window.scrollTo(0, 0);
}

// Função de excluir
function excluirTarefa(id, titulo) {
    if (confirm(`Tem certeza que deseja excluir a tarefa "${titulo}"?`)) {
        fetch(`../php/api_tarefas.php?acao=excluir&id=${id}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("Tarefa excluída com sucesso!");
                listarTarefas();
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

document.addEventListener("DOMContentLoaded", function () {
    listarCategorias();

    const form = document.getElementById("form-categoria");

    // Evento de Salvar (Adicionar ou Editar)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("id_categoria").value;
        const nome = document.getElementById("nome_categoria").value;
        const cor = document.getElementById("cor_categoria").value;

        const dados = { id: id, nome: nome, cor: cor };

        fetch('../php/api_categorias.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert("Salvo com sucesso!");
                form.reset();
                document.getElementById("id_categoria").value = "";
                document.getElementById("form-titulo").textContent = "Adicionar Nova Categoria";
                document.getElementById("btn-salvar").textContent = "Salvar Categoria";
                listarCategorias(); // Recarrega a tabela
            } else {
                alert("Erro: " + data.erro);
            }
        })
        .catch(error => console.error('Erro:', error));
    });
});

// Função para buscar dados do PHP e montar a tabela
function listarCategorias() {
    fetch('../php/api_categorias.php')
        .then(response => response.json())
        .then(categorias => {
            const tbody = document.getElementById("tabela-categorias-corpo");
            tbody.innerHTML = ""; // Limpa a tabela

            if (categorias.length === 0) {
                tbody.innerHTML = "<tr><td colspan='3'>Nenhuma categoria cadastrada.</td></tr>";
                return;
            }

            categorias.forEach(cat => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${cat.nome}</td>
                    <td>
                        <span class="color-swatch" style="background-color: ${cat.cor};"></span>
                        ${cat.cor}
                    </td>
                    <td>
                        <button class="btn btn-edit" onclick="editarCategoria(${cat.id}, '${cat.nome}', '${cat.cor}')">Editar</button>
                        <button class="btn btn-delete" onclick="excluirCategoria(${cat.id})">Excluir</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}

// Preenche o formulário para edição
function editarCategoria(id, nome, cor) {
    document.getElementById("id_categoria").value = id;
    document.getElementById("nome_categoria").value = nome;
    document.getElementById("cor_categoria").value = cor;
    
    document.getElementById("form-titulo").textContent = "Editar Categoria";
    document.getElementById("btn-salvar").textContent = "Atualizar Categoria";
    window.scrollTo(0, 0);
}

// Função de excluir
function excluirCategoria(id) {
    if (confirm("Tem certeza que deseja excluir?")) {
        fetch(`../php/api_categorias.php?acao=excluir&id=${id}`, {
            method: 'POST' // Ou DELETE, dependendo da configuração do servidor
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                listarCategorias();
            } else {
                alert("Erro ao excluir.");
            }
        });
    }
}

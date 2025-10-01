document.addEventListener("DOMContentLoaded", function () {
  // --- LÓGICA PARA A PÁGINA DE CATEGORIAS ---

  const botoesEditarCategoria = document.querySelectorAll(
    ".btn-editar-categoria"
  );

  if (botoesEditarCategoria.length > 0) {
    const formTituloCategoria = document.getElementById("form-titulo");
    const inputIdCategoria = document.getElementById("id_categoria");
    const inputNomeCategoria = document.getElementById("nome_categoria");
    const inputCorCategoria = document.getElementById("cor_categoria");
    const btnSalvarCategoria = document.getElementById("btn-salvar");

    botoesEditarCategoria.forEach((botao) => {
      botao.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        const nome = this.getAttribute("data-nome");
        const cor = this.getAttribute("data-cor");

        formTituloCategoria.textContent = "Editar Categoria";
        inputIdCategoria.value = id;
        inputNomeCategoria.value = nome;
        inputCorCategoria.value = cor;
        btnSalvarCategoria.textContent = "Atualizar Categoria";

        window.scrollTo(0, 0);
      });
    });

    const linksExcluirCategoria = document.querySelectorAll(
      ".btn-excluir-categoria"
    );
    linksExcluirCategoria.forEach((link) => {
      link.addEventListener("click", function (event) {
        if (!confirm("Tem certeza que deseja excluir esta categoria?")) {
          event.preventDefault();
        }
      });
    });
  }

  // --- LÓGICA PARA A PÁGINA DE PROJETOS ---
  const botoesEditarProjeto = document.querySelectorAll(".btn-editar-projeto");

  if (botoesEditarProjeto.length > 0) {
    const formTituloProjeto = document.getElementById("form-titulo-projeto");
    const inputIdProjeto = document.getElementById("id_projeto");
    const inputNomeProjeto = document.getElementById("nome_projeto");
    const inputDescricaoProjeto = document.getElementById("descricao_projeto");
    const btnSalvarProjeto = document.getElementById("btn-salvar-projeto");

    botoesEditarProjeto.forEach((botao) => {
      botao.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        const nome = this.getAttribute("data-nome");
        const descricao = this.getAttribute("data-descricao");

        formTituloProjeto.textContent = "Editar Projeto";
        inputIdProjeto.value = id;
        inputNomeProjeto.value = nome;
        inputDescricaoProjeto.value = descricao;
        btnSalvarProjeto.textContent = "Atualizar Projeto";

        window.scrollTo(0, 0);
      });
    });

    const linksExcluirProjeto = document.querySelectorAll(
      ".btn-excluir-projeto"
    );
    linksExcluirProjeto.forEach((link) => {
      link.addEventListener("click", function (event) {
        if (
          !confirm(
            "Tem certeza que deseja excluir este projeto? Todas as tarefas associadas a ele também serão excluídas."
          )
        ) {
          event.preventDefault();
        }
      });
    });
  }

  // --- LÓGICA PARA A PÁGINA DE TAREFAS ---
  const botoesEditarTarefa = document.querySelectorAll(".btn-editar-tarefa");

  if (botoesEditarTarefa.length > 0) {
    const formTituloTarefa = document.getElementById("form-titulo-tarefa");
    const inputIdTarefa = document.getElementById("id_tarefa");
    const inputTituloTarefa = document.getElementById("titulo_tarefa");
    const inputDescricaoTarefa = document.getElementById("descricao_tarefa");
    const selectProjeto = document.getElementById("projeto_id");
    const inputDataLimite = document.getElementById("data_limite");
    const selectStatus = document.getElementById("status");
    const btnSalvarTarefa = document.getElementById("btn-salvar-tarefa");

    botoesEditarTarefa.forEach((botao) => {
      botao.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        const titulo = this.getAttribute("data-titulo");
        const descricao = this.getAttribute("data-descricao");
        const projetoId = this.getAttribute("data-projeto-id");
        const dataLimite = this.getAttribute("data-data-limite");
        const status = this.getAttribute("data-status");

        formTituloTarefa.textContent = "Editar Tarefa";
        inputIdTarefa.value = id;
        inputTituloTarefa.value = titulo;
        inputDescricaoTarefa.value = descricao;
        selectProjeto.value = projetoId;
        inputDataLimite.value = dataLimite;
        selectStatus.value = status;
        btnSalvarTarefa.textContent = "Atualizar Tarefa";

        window.scrollTo(0, 0);
      });
    });

    const linksExcluirTarefa = document.querySelectorAll(".btn-excluir-tarefa");
    linksExcluirTarefa.forEach((link) => {
      link.addEventListener("click", function (event) {
        if (!confirm("Tem certeza que deseja excluir esta tarefa?")) {
          event.preventDefault();
        }
      });
    });
  }
});
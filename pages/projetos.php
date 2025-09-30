<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List - Meus Projetos</title>
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
    <header>
      <h1>Meu Gerenciador de Tarefas</h1>
    </header>

    <nav>
      <ul>
        <li><a href="../index.php">Início</a></li>
        <li><a href="projetos.php">Meus Projetos</a></li>
        <li><a href="tarefas.php">Minhas Tarefas</a></li>
        <li><a href="categorias.php">Categorias</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="cadastro.php">Cadastre-se</a></li>
      </ul>
    </nav>

    <main>
      <h2>Gerenciamento de Projetos</h2>

      <div class="card">
        <h3>Adicionar / Editar Projeto</h3>
        <form action="validador.php" method="POST">
          <div class="form-group">
            <label for="nome_projeto">Nome do Projeto:</label>
            <input type="text" id="nome_projeto" name="nome_projeto" required />
          </div>
          <div class="form-group">
            <label for="descricao_projeto">Descrição:</label>
            <textarea
              id="descricao_projeto"
              name="descricao_projeto"
              rows="3"
            ></textarea>
          </div>
          <button type="submit" class="btn">Salvar Projeto</button>
        </form>
      </div>

      <div class="card">
        <h3>Meus Projetos</h3>
        <table class="crud-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Descrição</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Trabalho da Faculdade</td>
              <td>
                Desenvolver o sistema de To-Do List para a matéria de TPI.
              </td>
              <td>
                <button class="btn btn-edit">Editar</button>
                <button class="btn btn-delete">Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Compras de Supermercado</td>
              <td>Lista de itens para comprar no final de semana.</td>
              <td>
                <button class="btn btn-edit">Editar</button>
                <button class="btn btn-delete">Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 - Desenvolvido por [Jonas e Antonio]</p>
    </footer>
  </body>
</html>

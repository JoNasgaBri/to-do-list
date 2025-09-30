<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List - Minhas Tarefas</title>
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
      <div class="card">
        <h2>Gerenciamento de Tarefas</h2>

        <form action="validador.php" method="POST">
          <div class="form-group">
            <label for="titulo_tarefa">Título da Tarefa:</label>
            <input
              type="text"
              id="titulo_tarefa"
              name="titulo_tarefa"
              required
            />
          </div>
          <div class="form-group">
            <label for="projeto">Associar ao Projeto:</label>
            <select id="projeto" name="projeto">
              <option value="1">Trabalho da Faculdade</option>
              <option value="2">Compras de Supermercado</option>
            </select>
          </div>
          <div class="form-group">
            <label for="data_limite">Data Limite:</label>
            <input type="date" id="data_limite" name="data_limite" />
          </div>
          <button type="submit" class="btn">Salvar Tarefa</button>
        </form>
      </div>

      <div class="card">
        <h3>Minhas Tarefas</h3>
        <table class="crud-table">
          <thead>
            <tr>
              <th>Tarefa</th>
              <th>Projeto</th>
              <th>Data Limite</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Desenvolver o banco de dados</td>
              <td>Trabalho da Faculdade</td>
              <td>2025-10-10</td>
              <td>Em Andamento</td>
              <td>
                <button class="btn btn-edit">Editar</button>
                <button class="btn btn-delete">Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Comprar leite e pão</td>
              <td>Compras de Supermercado</td>
              <td>2025-10-02</td>
              <td>Pendente</td>
              <td>
                <button class="btn btn-edit">Editar</button>
                <button class="btn btn-delete">Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Estilizar páginas com CSS</td>
              <td>Trabalho da Faculdade</td>
              <td>2025-10-05</td>
              <td>Concluída</td>
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
      <p>&copy; 2025 - Desenvolvido por [Seu Nome e Nome do Colega]</p>
    </footer>

    <style>
      select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
        background-color: white;
      }
    </style>
  </body>
</html>

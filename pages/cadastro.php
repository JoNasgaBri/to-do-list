<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List - Cadastro</title>
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
        <h2>Crie sua Conta (Em Desenvolvimento)</h2>
        <form action="../php/validador.php" method="POST">
          <div class="form-group">
            <label for="nome">Nome Completo:</label>
            <input type="text" id="nome" name="nome" required />
          </div>
          <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required />
          </div>
          <div class="form-group">
            <label for="confirmar_senha">Confirmar Senha:</label>
            <input
              type="password"
              id="confirmar_senha"
              name="confirmar_senha"
              required
            />
          </div>
          <div class="form-group">
            <label for="data_nasc">Data de Nascimento:</label>
            <input type="date" id="data_nasc" name="data_nasc" required />
          </div>
          <button type="submit" class="btn">Cadastrar</button>
        </form>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 - Desenvolvido por [Jonas e Antonio]</p>
    </footer>
  </body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List - Login</title>
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
        <h2>Acesse sua Conta</h2>
        <form action="validador.php" method="POST">
          <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required />
          </div>
          <button type="submit" class="btn">Entrar</button>
        </form>
        <p class="form-link">
          Não tem uma conta? <a href="cadastro.html">Cadastre-se aqui</a>
        </p>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 - Desenvolvido por [Seu Nome e Nome do Colega]</p>
    </footer>
  </body>
</html>

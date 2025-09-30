<?php
// Definindo o caminho base para links e assets 
$base_path = '/to-do-list/';

// 1. INCLUIR A CONEXÃO COM O BANCO DE DADOS
include '../php/conexao.php';

// --- LÓGICA PARA PROCESSAR AS AÇÕES (ADICIONAR, EDITAR, EXCLUIR) ---

// VERIFICA SE O FORMULÁRIO FOI ENVIADO (MÉTODO POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome_categoria'];
    $cor = $_POST['cor_categoria'];
    $id = $_POST['id_categoria']; 

    // Se o ID não estiver vazio, é uma ATUALIZAÇÃO (UPDATE)
    if (!empty($id)) {
        $sql = "UPDATE Categorias SET nome = ?, cor = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $nome, $cor, $id);
    } 
    // Se o ID estiver vazio, é uma INSERÇÃO (INSERT)
    else {
        $sql = "INSERT INTO Categorias (nome, cor) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $nome, $cor);
    }

    // Executa a query e redireciona para a mesma página para atualizar a lista
    if ($stmt->execute()) {
        header("Location: categorias.php");
        exit();
    } else {
        echo "Erro ao salvar categoria: " . $conexao->error;
    }
}

// VERIFICA SE UMA AÇÃO DE EXCLUSÃO FOI SOLICITADA (MÉTODO GET)
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Categorias WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);

    // Executa a query e redireciona
    if ($stmt->execute()) {
        header("Location: categorias.php");
        exit();
    } else {
        echo "Erro ao excluir categoria: " . $conexao->error;
    }
}

// --- LÓGICA PARA BUSCAR OS DADOS NO BANCO ---
$lista_categorias = [];
$sql_busca = "SELECT id, nome, cor FROM Categorias ORDER BY nome ASC";
$resultado = $conexao->query($sql_busca);

if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $lista_categorias[] = $linha;
    }
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Minhas Categorias</title>
   
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/style.css">
</head>
<body>

    <header>
        <h1>Meu Gerenciador de Tarefas</h1>
    </header>

    <nav>
        <ul>
            
            <li><a href="<?php echo $base_path; ?>index.php">Início</a></li>
            <li><a href="<?php echo $base_path; ?>pages/projetos.php">Meus Projetos</a></li>
            <li><a href="<?php echo $base_path; ?>pages/tarefas.php">Minhas Tarefas</a></li>
            <li><a href="<?php echo $base_path; ?>pages/categorias.php">Categorias</a></li>
            <li><a href="<?php echo $base_path; ?>pages/login.php">Login</a></li>
            <li><a href="<?php echo $base_path; ?>pages/cadastro.php">Cadastre-se</a></li>
        </ul>
    </nav>

    <main>
        <div class="card">
            <h2 id="form-titulo">Adicionar Nova Categoria</h2>
            <!-- Formulário para adicionar nova categoria -->
            <form action="categorias.php" method="POST">
                <input type="hidden" name="id_categoria" id="id_categoria">
                
                <div class="form-group">
                    <label for="nome_categoria">Nome da Categoria:</label>
                    <input type="text" id="nome_categoria" name="nome_categoria" required>
                </div>
                <div class="form-group">
                    <label for="cor_categoria">Cor da Categoria:</label>
                    <input type="color" id="cor_categoria" name="cor_categoria" value="#7f5af0">
                </div>
                <button type="submit" class="btn" id="btn-salvar">Salvar Categoria</button>
            </form>
        </div>

        <div class="card">
            <h3>Minhas Categorias</h3>
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lista_categorias)): ?>
                        <tr>
                            <td colspan="3">Nenhuma categoria cadastrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lista_categorias as $categoria): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                <td>
                                    <span class="color-swatch" style="background-color: <?php echo htmlspecialchars($categoria['cor']); ?>;"></span>
                                    <?php echo htmlspecialchars($categoria['cor']); ?>
                                </td>
                                <td>
                                    <button 
                                        class="btn btn-edit btn-editar-categoria"
                                        data-id="<?php echo $categoria['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($categoria['nome']); ?>"
                                        data-cor="<?php echo htmlspecialchars($categoria['cor']); ?>">
                                        Editar
                                    </button>
                                    <!-- Botão de exclusão com confirmação -->
                                    <a href="categorias.php?acao=excluir&id=<?php echo $categoria['id']; ?>" class="btn btn-delete btn-excluir-categoria">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 - Desenvolvido por [Jonas e Antonio]</p>
    </footer>

    
    <script src="<?php echo $base_path; ?>js/scripts.js"></script>
</body>
</html>


<?php
// Define o caminho base do projeto
$base_path = '/to-do-list/';

// 1. INCLUIR A CONEXÃO COM O BANCO DE DADOS
include '../php/conexao.php';

// Função para formatar datas no padrão brasileiro
function formatarDataBrasil($data, $incluirHora = false) {
    if (empty($data) || $data == '0000-00-00' || $data == '0000-00-00 00:00:00') {
        return 'Não definida';
    }
    
    try {
        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return 'Data inválida';
        }
        
        if ($incluirHora) {
            return date("d/m/Y H:i", $timestamp);
        } else {
            return date("d/m/Y", $timestamp);
        }
    } catch (Exception $e) {
        return 'Data inválida';
    }
}

// Verificar se existe um usuário padrão, se não existir, criar um
$sql_check_user = "SELECT id FROM Usuarios WHERE id = 1";
$result = $conexao->query($sql_check_user);

if ($result->num_rows == 0) {
    // Criar um usuário padrão usando INSERT IGNORE para evitar erro se já existir
    $sql_create_user = "INSERT IGNORE INTO Usuarios (id, nome, email, senha, data_cadastro) VALUES (1, 'Usuário Padrão', 'admin@todolist.com', 'admin123', NOW())";
    if (!$conexao->query($sql_create_user)) {
        // Se falhar, tentar sem especificar o ID (deixar auto-increment)
        $sql_create_user_auto = "INSERT INTO Usuarios (nome, email, senha, data_cadastro) VALUES ('Usuário Padrão', 'admin@todolist.com', 'admin123', NOW())";
        $conexao->query($sql_create_user_auto);
        
        // Buscar o ID que foi criado automaticamente
        $result = $conexao->query("SELECT id FROM Usuarios WHERE email = 'admin@todolist.com' LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $usuario_padrao_id = $row['id'];
        } else {
            $usuario_padrao_id = 1; // fallback
        }
    } else {
        $usuario_padrao_id = 1;
    }
} else {
    $usuario_padrao_id = 1;
}

// --- LÓGICA PARA PROCESSAR AS AÇÕES (ADICIONAR, EDITAR, EXCLUIR) ---

// VERIFICA SE O FORMULÁRIO FOI ENVIADO (MÉTODO POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Usar o ID do usuário padrão que foi verificado/criado acima
    $usuario_id = isset($usuario_padrao_id) ? $usuario_padrao_id : 1; 
    $nome = $_POST['nome_projeto'];
    $descricao = $_POST['descricao_projeto'];
    $id = $_POST['id_projeto'];

    // Se o ID não estiver vazio, é uma ATUALIZAÇÃO (UPDATE)
    if (!empty($id)) {
        $sql = "UPDATE Projetos SET nome = ?, descricao = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssii", $nome, $descricao, $id, $usuario_id);
    } 
    // Se o ID estiver vazio, é uma INSERÇÃO (INSERT)
    else {
        $sql = "INSERT INTO Projetos (nome, descricao, usuario_id) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $nome, $descricao, $usuario_id);
    }

    if ($stmt->execute()) {
        header("Location: projetos.php");
        exit();
    } else {
        echo "Erro ao salvar projeto: " . $stmt->error . "<br>";
        echo "SQL Estado: " . $conexao->sqlstate . "<br>";
        echo "Código de erro: " . $conexao->errno . "<br>";
        if ($conexao->errno == 1452) {
            echo "⚠️ Erro de chave estrangeira: O usuário especificado não existe. Verifique se existe um usuário com ID {$usuario_id} na tabela Usuarios.";
        }
    }
}

// VERIFICA SE UMA AÇÃO DE EXCLUSÃO FOI SOLICITADA (MÉTODO GET)
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario_id = isset($usuario_padrao_id) ? $usuario_padrao_id : 1; // Usar o mesmo ID do usuário padrão
    $sql = "DELETE FROM Projetos WHERE id = ? AND usuario_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id, $usuario_id);

    if ($stmt->execute()) {
        header("Location: projetos.php");
        exit();
    } else {
        echo "Erro ao excluir projeto: " . $conexao->error;
    }
}

// --- LÓGICA PARA BUSCAR OS DADOS NO BANCO ---
$lista_projetos = [];
$usuario_id = isset($usuario_padrao_id) ? $usuario_padrao_id : 1; // Usar o mesmo ID do usuário padrão
$sql_busca = "SELECT id, nome, descricao, data_criacao FROM Projetos WHERE usuario_id = ? ORDER BY nome ASC";
$stmt_busca = $conexao->prepare($sql_busca);
$stmt_busca->bind_param("i", $usuario_id);
$stmt_busca->execute();
$resultado = $stmt_busca->get_result();

if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $lista_projetos[] = $linha;
    }
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Meus Projetos</title>
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
            <h2 id="form-titulo-projeto">Adicionar Novo Projeto</h2>
            <form action="projetos.php" method="POST">
                <input type="hidden" name="id_projeto" id="id_projeto">
                
                <div class="form-group">
                    <label for="nome_projeto">Nome do Projeto:</label>
                    <input type="text" id="nome_projeto" name="nome_projeto" required>
                </div>
                <div class="form-group">
                    <label for="descricao_projeto">Descrição:</label>
                    <textarea id="descricao_projeto" name="descricao_projeto" rows="3"></textarea>
                </div>
                <button type="submit" class="btn" id="btn-salvar-projeto">Salvar Projeto</button>
            </form>
        </div>

        <div class="card">
            <h3>Meus Projetos</h3>
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lista_projetos)): ?>
                        <tr>
                            <td colspan="4">Nenhum projeto cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lista_projetos as $projeto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($projeto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($projeto['descricao']); ?></td>
                                <td><?php echo formatarDataBrasil($projeto['data_criacao'], true); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-edit btn-editar-projeto"
                                        data-id="<?php echo $projeto['id']; ?>"
                                        data-nome="<?php echo htmlspecialchars($projeto['nome']); ?>"
                                        data-descricao="<?php echo htmlspecialchars($projeto['descricao']); ?>">
                                        Editar
                                    </button>
                                    <a href="projetos.php?acao=excluir&id=<?php echo $projeto['id']; ?>" class="btn btn-delete btn-excluir-projeto">Excluir</a>
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

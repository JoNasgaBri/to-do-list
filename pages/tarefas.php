<?php
// Define o caminho base do projeto
$base_path = '/to-do-list/';

// 1. INCLUIR A CONEXÃO COM O BANCO DE DADOS
include '../php/conexao.php';

// 2. INCLUIR FUNÇÕES UTILITÁRIAS
include '../php/funcoes.php';

// --- LÓGICA PARA PROCESSAR AS AÇÕES (ADICIONAR, EDITAR, EXCLUIR) ---

// Assumindo que o usuário_id será 1 por enquanto (usuário fixo)
$usuario_id = 1;

// VERIFICA SE O FORMULÁRIO FOI ENVIADO (MÉTODO POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo_tarefa'];
    $descricao = $_POST['descricao_tarefa'];
    $projeto_id = $_POST['projeto_id'];
    $data_limite = $_POST['data_limite'];
    $status = $_POST['status'];
    $id = $_POST['id_tarefa'];

    // Se o ID não estiver vazio, é uma ATUALIZAÇÃO (UPDATE)
    if (!empty($id)) {
        $sql = "UPDATE Tarefas SET titulo = ?, descricao = ?, projeto_id = ?, data_limite = ?, status = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssissi", $titulo, $descricao, $projeto_id, $data_limite, $status, $id);
    } 
    // Se o ID estiver vazio, é uma INSERÇÃO (INSERT)
    else {
        $sql = "INSERT INTO Tarefas (titulo, descricao, projeto_id, data_limite, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssiss", $titulo, $descricao, $projeto_id, $data_limite, $status);
    }

    if ($stmt->execute()) {
        header("Location: tarefas.php");
        exit();
    } else {
        echo "Erro ao salvar tarefa: " . $conexao->error;
    }
}

// VERIFICA SE UMA AÇÃO DE EXCLUSÃO FOI SOLICITADA (MÉTODO GET)
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Tarefas WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: tarefas.php");
        exit();
    } else {
        echo "Erro ao excluir tarefa: " . $conexao->error;
    }
}

// --- LÓGICA PARA BUSCAR OS DADOS NO BANCO ---

// 1. Buscar a lista de projetos do usuário para o formulário
$lista_projetos_form = [];
$sql_projetos = "SELECT id, nome FROM Projetos WHERE usuario_id = ?";
$stmt_projetos = $conexao->prepare($sql_projetos);
$stmt_projetos->bind_param("i", $usuario_id);
$stmt_projetos->execute();
$resultado_projetos = $stmt_projetos->get_result();
if ($resultado_projetos->num_rows > 0) {
    while($linha = $resultado_projetos->fetch_assoc()) {
        $lista_projetos_form[] = $linha;
    }
}

// 2. Buscar a lista de tarefas para a tabela
$lista_tarefas = [];
// Usamos um JOIN para buscar o nome do projeto junto com os dados da tarefa
$sql_busca = "SELECT t.id, t.titulo, t.descricao, t.status, t.data_limite, p.nome AS nome_projeto, t.projeto_id 
              FROM Tarefas AS t
              JOIN Projetos AS p ON t.projeto_id = p.id
              WHERE p.usuario_id = ? 
              ORDER BY t.data_limite ASC";
$stmt_busca = $conexao->prepare($sql_busca);
$stmt_busca->bind_param("i", $usuario_id);
$stmt_busca->execute();
$resultado = $stmt_busca->get_result();

if ($resultado->num_rows > 0) {
    while($linha = $resultado->fetch_assoc()) {
        $lista_tarefas[] = $linha;
    }
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Minhas Tarefas</title>
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
            <h2 id="form-titulo-tarefa">Adicionar Nova Tarefa</h2>
            <form action="tarefas.php" method="POST">
                <input type="hidden" name="id_tarefa" id="id_tarefa">
                
                <div class="form-group">
                    <label for="titulo_tarefa">Título da Tarefa:</label>
                    <input type="text" id="titulo_tarefa" name="titulo_tarefa" required>
                </div>
                <div class="form-group">
                    <label for="descricao_tarefa">Descrição:</label>
                    <textarea id="descricao_tarefa" name="descricao_tarefa" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="projeto_id">Projeto:</label>
                    <select id="projeto_id" name="projeto_id" required>
                        <option value="">Selecione um projeto</option>
                        <!-- Loop PHP para popular os projetos -->
                        <?php foreach ($lista_projetos_form as $projeto): ?>
                            <option value="<?php echo $projeto['id']; ?>"><?php echo htmlspecialchars($projeto['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_limite">Data Limite:</label>
                    <input type="date" id="data_limite" name="data_limite">
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Concluída">Concluída</option>
                    </select>
                </div>
                <button type="submit" class="btn" id="btn-salvar-tarefa">Salvar Tarefa</button>
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
                    <?php if (empty($lista_tarefas)): ?>
                        <tr>
                            <td colspan="5">Nenhuma tarefa cadastrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lista_tarefas as $tarefa): ?>
                            <?php 
                                // Adicionar classes CSS baseadas no status da tarefa
                                $classeLinha = '';
                                if (dataVencida($tarefa['data_limite'])) {
                                    $classeLinha = 'tarefa-vencida';
                                } elseif (dataProximaVencimento($tarefa['data_limite'])) {
                                    $classeLinha = 'tarefa-proxima-vencimento';
                                }
                            ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td>
                                    <?php echo htmlspecialchars($tarefa['titulo']); ?>
                                    <?php if (dataVencida($tarefa['data_limite'])): ?>
                                        <span class="alerta-vencida">⚠️ VENCIDA</span>
                                    <?php elseif (dataProximaVencimento($tarefa['data_limite'])): ?>
                                        <span class="alerta-proxima">🔔 PRÓXIMA</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($tarefa['nome_projeto']); ?></td>
                                <td>
                                    <?php echo formatarDataBrasil($tarefa['data_limite']); ?>
                                    <?php if (!empty($tarefa['data_limite']) && $tarefa['data_limite'] != '0000-00-00'): ?>
                                        <br><small>(<?php 
                                            $dias = calcularDiferencaDias(date('Y-m-d'), $tarefa['data_limite']);
                                            if ($dias < 0) {
                                                echo abs($dias) . ' dias atrás';
                                            } elseif ($dias == 0) {
                                                echo 'Hoje';
                                            } else {
                                                echo $dias . ' dias restantes';
                                            }
                                        ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatarStatusTarefa($tarefa['status']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-edit btn-editar-tarefa"
                                        data-id="<?php echo $tarefa['id']; ?>"
                                        data-titulo="<?php echo htmlspecialchars($tarefa['titulo']); ?>"
                                        data-descricao="<?php echo htmlspecialchars($tarefa['descricao']); ?>"
                                        data-projeto-id="<?php echo $tarefa['projeto_id']; ?>"
                                        data-data-limite="<?php echo $tarefa['data_limite']; ?>"
                                        data-status="<?php echo $tarefa['status']; ?>">
                                        Editar
                                    </button>
                                    <a href="tarefas.php?acao=excluir&id=<?php echo $tarefa['id']; ?>" class="btn btn-delete btn-excluir-tarefa">Excluir</a>
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
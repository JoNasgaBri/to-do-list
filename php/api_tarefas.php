<?php
// php/api_tarefas.php
header('Content-Type: application/json');
session_start();
include 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// Pegar o ID do usuário logado (ou usar 1 como padrão para testes)
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1;

// 1. LISTAR TAREFAS (GET)
if ($metodo == 'GET') {
    // Se pediu lista de projetos para o select
    if (isset($_GET['tipo']) && $_GET['tipo'] == 'projetos') {
        $lista = [];
        $sql = "SELECT id, nome FROM Projetos WHERE usuario_id = ? ORDER BY nome ASC";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        while($linha = $resultado->fetch_assoc()) {
            $lista[] = $linha;
        }
        echo json_encode($lista);
        exit;
    }
    
    // Lista de tarefas
    $lista = [];
    $sql = "SELECT t.id, t.titulo, t.descricao, t.status, t.data_limite, 
                   p.nome AS nome_projeto, t.projeto_id 
            FROM Tarefas AS t
            JOIN Projetos AS p ON t.projeto_id = p.id
            WHERE p.usuario_id = ? 
            ORDER BY t.data_limite ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            // Formatar data
            if ($linha['data_limite'] && $linha['data_limite'] != '0000-00-00') {
                $linha['data_limite_formatada'] = date('d/m/Y', strtotime($linha['data_limite']));
                
                // Calcular dias restantes
                $hoje = new DateTime();
                $limite = new DateTime($linha['data_limite']);
                $diff = $hoje->diff($limite);
                $dias = $diff->days * ($diff->invert ? -1 : 1);
                
                $linha['dias_restantes'] = $dias;
                $linha['vencida'] = $dias < 0;
                $linha['proxima'] = $dias >= 0 && $dias <= 3;
            } else {
                $linha['data_limite_formatada'] = 'Não definida';
                $linha['dias_restantes'] = null;
                $linha['vencida'] = false;
                $linha['proxima'] = false;
            }
            $lista[] = $linha;
        }
    }
    echo json_encode($lista);
    exit;
}

// 2. ADICIONAR OU EDITAR (POST)
if ($metodo == 'POST' && !isset($_GET['acao'])) {
    $dados = json_decode(file_get_contents("php://input"), true);

    $titulo = $dados['titulo'] ?? '';
    $descricao = $dados['descricao'] ?? '';
    $projeto_id = $dados['projeto_id'] ?? '';
    $data_limite = $dados['data_limite'] ?? null;
    $status = $dados['status'] ?? 'Pendente';
    $id = $dados['id'] ?? '';

    if (empty($titulo)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Título da tarefa é obrigatório']);
        exit;
    }
    if (empty($projeto_id)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Selecione um projeto']);
        exit;
    }

    // Tratar data vazia
    if (empty($data_limite)) {
        $data_limite = null;
    }

    if (!empty($id)) {
        // Atualizar
        $stmt = $conexao->prepare("UPDATE Tarefas SET titulo = ?, descricao = ?, projeto_id = ?, data_limite = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssissi", $titulo, $descricao, $projeto_id, $data_limite, $status, $id);
    } else {
        // Inserir
        $stmt = $conexao->prepare("INSERT INTO Tarefas (titulo, descricao, projeto_id, data_limite, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $titulo, $descricao, $projeto_id, $data_limite, $status);
    }

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'id' => $id ? $id : $conexao->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
    }
    exit;
}

// 3. EXCLUIR (POST com acao=excluir)
if ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $stmt = $conexao->prepare("DELETE FROM Tarefas WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
        }
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
    }
    exit;
}

$conexao->close();
?>

<?php
// php/api_projetos.php
header('Content-Type: application/json');
session_start();
include 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// Pegar o ID do usuário logado (ou usar 1 como padrão para testes)
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1;

// 1. LISTAR PROJETOS (GET)
if ($metodo == 'GET') {
    $lista = [];
    $sql = "SELECT id, nome, descricao, data_criacao FROM Projetos WHERE usuario_id = ? ORDER BY nome ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            // Formatar data
            $linha['data_criacao_formatada'] = date('d/m/Y H:i', strtotime($linha['data_criacao']));
            $lista[] = $linha;
        }
    }
    echo json_encode($lista);
    exit;
}

// 2. ADICIONAR OU EDITAR (POST)
if ($metodo == 'POST' && !isset($_GET['acao'])) {
    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = $dados['nome'] ?? '';
    $descricao = $dados['descricao'] ?? '';
    $id = $dados['id'] ?? '';

    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome do projeto é obrigatório']);
        exit;
    }

    if (!empty($id)) {
        // Atualizar
        $stmt = $conexao->prepare("UPDATE Projetos SET nome = ?, descricao = ? WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ssii", $nome, $descricao, $id, $usuario_id);
    } else {
        // Inserir
        $stmt = $conexao->prepare("INSERT INTO Projetos (nome, descricao, usuario_id, data_criacao) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssi", $nome, $descricao, $usuario_id);
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
        $stmt = $conexao->prepare("DELETE FROM Projetos WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $id, $usuario_id);
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

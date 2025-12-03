<?php
// php/api_categorias.php
header('Content-Type: application/json');
include 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. LISTAR CATEGORIAS (GET)
if ($metodo == 'GET') {
    $lista = [];
    $sql = "SELECT id, nome, cor FROM Categorias ORDER BY nome ASC";
    $resultado = $conexao->query($sql);

    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            $lista[] = $linha;
        }
    }
    echo json_encode($lista);
    exit;
}

// 2. ADICIONAR OU EDITAR (POST)
if ($metodo == 'POST') {
    // Lê o JSON enviado pelo JavaScript
    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = $dados['nome'] ?? '';
    $cor = $dados['cor'] ?? '#FFFFFF';
    $id = $dados['id'] ?? '';

    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome é obrigatório']);
        exit;
    }

    if (!empty($id)) {
        // Atualizar
        $stmt = $conexao->prepare("UPDATE Categorias SET nome = ?, cor = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $cor, $id);
    } else {
        // Inserir
        $stmt = $conexao->prepare("INSERT INTO Categorias (nome, cor) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cor);
    }

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
    }
    exit;
}

// 3. EXCLUIR (DELETE) - Vamos simular via POST com ação específica ou usar DELETE real
// Para simplificar, vou assumir que recebes um objeto JSON com acao: 'excluir'
if ($metodo == 'DELETE' || ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir')) {
    // Se for DELETE real, lê do input, se for query string lê do GET
    $id = $_GET['id'] ?? null;
    
    if($id) {
        $stmt = $conexao->prepare("DELETE FROM Categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
        }
    }
    exit;
}

$conexao->close();
?>

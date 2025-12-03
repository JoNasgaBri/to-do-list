<?php
// php/api_usuarios.php
header('Content-Type: application/json');
session_start();
include 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. LISTAR/PESQUISAR USUÁRIOS (GET)
if ($metodo == 'GET') {
    $lista = [];
    
    // Se tem parâmetro de pesquisa
    if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
        $pesquisa = '%' . $_GET['pesquisa'] . '%';
        $sql = "SELECT id, nome, email, data_cadastro FROM Usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY nome ASC";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $pesquisa, $pesquisa);
        $stmt->execute();
        $resultado = $stmt->get_result();
    } else {
        $sql = "SELECT id, nome, email, data_cadastro FROM Usuarios ORDER BY nome ASC";
        $resultado = $conexao->query($sql);
    }

    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            // Formatar data para exibição
            $linha['data_cadastro_formatada'] = date('d/m/Y H:i', strtotime($linha['data_cadastro']));
            $lista[] = $linha;
        }
    }
    echo json_encode($lista);
    exit;
}

// 2. ADICIONAR OU EDITAR USUÁRIO (POST)
if ($metodo == 'POST' && !isset($_GET['acao'])) {
    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = $dados['nome'] ?? '';
    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';
    $id = $dados['id'] ?? '';

    // Validações
    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome é obrigatório']);
        exit;
    }
    if (empty($email)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email é obrigatório']);
        exit;
    }

    // Verificar se email já existe (para outro usuário)
    $sql_check = "SELECT id FROM Usuarios WHERE email = ? AND id != ?";
    $stmt_check = $conexao->prepare($sql_check);
    $id_check = empty($id) ? 0 : $id;
    $stmt_check->bind_param("si", $email, $id_check);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Este email já está cadastrado']);
        exit;
    }

    if (!empty($id)) {
        // ATUALIZAR
        if (!empty($senha)) {
            // Atualiza com nova senha
            $stmt = $conexao->prepare("UPDATE Usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $email, $senha, $id);
        } else {
            // Atualiza sem mudar senha
            $stmt = $conexao->prepare("UPDATE Usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $id);
        }
    } else {
        // INSERIR
        if (empty($senha)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Senha é obrigatória para novo usuário']);
            exit;
        }
        $stmt = $conexao->prepare("INSERT INTO Usuarios (nome, email, senha, data_cadastro) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $nome, $email, $senha);
    }

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'id' => $id ? $id : $conexao->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
    }
    exit;
}

// 3. EXCLUIR USUÁRIO (POST com acao=excluir)
if ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Não permitir excluir o próprio usuário logado
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            echo json_encode(['sucesso' => false, 'erro' => 'Você não pode excluir seu próprio usuário enquanto está logado']);
            exit;
        }
        
        $stmt = $conexao->prepare("DELETE FROM Usuarios WHERE id = ?");
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

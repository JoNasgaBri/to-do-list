<?php
// php/api_login.php
header('Content-Type: application/json');
session_start();
include 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// VERIFICAR SE USUÁRIO ESTÁ LOGADO (GET)
if ($metodo == 'GET') {
    if (isset($_SESSION['usuario_id'])) {
        echo json_encode([
            'logado' => true,
            'usuario' => [
                'id' => $_SESSION['usuario_id'],
                'nome' => $_SESSION['usuario_nome'],
                'email' => $_SESSION['usuario_email']
            ]
        ]);
    } else {
        echo json_encode(['logado' => false]);
    }
    exit;
}

// LOGIN (POST)
if ($metodo == 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);
    
    // Verificar se é logout
    if (isset($dados['acao']) && $dados['acao'] == 'logout') {
        session_destroy();
        echo json_encode(['sucesso' => true, 'mensagem' => 'Logout realizado com sucesso']);
        exit;
    }

    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email e senha são obrigatórios']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email inválido']);
        exit;
    }

    // Buscar usuário no banco
    $sql = "SELECT id, nome, email, senha FROM Usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Usuário não encontrado']);
        exit;
    }

    $usuario = $resultado->fetch_assoc();

    // Verificar senha (comparação direta - em produção usar password_verify)
    if ($usuario['senha'] !== $senha) {
        echo json_encode(['sucesso' => false, 'erro' => 'Senha incorreta']);
        exit;
    }

    // Login bem sucedido - criar sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso',
        'usuario' => [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email']
        ]
    ]);
    exit;
}

$conexao->close();
?>

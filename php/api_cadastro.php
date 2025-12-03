<?php
// php/api_cadastro.php
header('Content-Type: application/json');
include 'conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];

// CADASTRAR USUÁRIO (POST)
if ($metodo == 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = trim($dados['nome'] ?? '');
    $email = trim($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';
    $confirmar_senha = $dados['confirmar_senha'] ?? '';
    $data_nasc = $dados['data_nasc'] ?? '';

    // ========== VALIDAÇÕES ==========

    // Validar Nome
    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome é obrigatório']);
        exit;
    }
    if (strlen($nome) < 3) {
        echo json_encode(['sucesso' => false, 'erro' => 'O nome deve ter pelo menos 3 caracteres']);
        exit;
    }
    if (!preg_match('/^[A-Za-zÀ-ÿ\s]+$/u', $nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'O nome deve conter apenas letras e espaços']);
        exit;
    }

    // Validar Email
    if (empty($email)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email é obrigatório']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email inválido']);
        exit;
    }

    // Validar Data de Nascimento
    if (empty($data_nasc)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Data de nascimento é obrigatória']);
        exit;
    }
    $dataNasc = DateTime::createFromFormat('Y-m-d', $data_nasc);
    if (!$dataNasc) {
        echo json_encode(['sucesso' => false, 'erro' => 'Data de nascimento inválida']);
        exit;
    }
    $hoje = new DateTime();
    $idade = $hoje->diff($dataNasc)->y;
    if ($dataNasc >= $hoje) {
        echo json_encode(['sucesso' => false, 'erro' => 'A data de nascimento deve ser anterior a hoje']);
        exit;
    }
    if ($idade < 10) {
        echo json_encode(['sucesso' => false, 'erro' => 'Você deve ter pelo menos 10 anos para se cadastrar']);
        exit;
    }
    if ($idade > 120) {
        echo json_encode(['sucesso' => false, 'erro' => 'Data de nascimento inválida']);
        exit;
    }

    // Validar Senha
    if (empty($senha)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Senha é obrigatória']);
        exit;
    }
    if (strlen($senha) < 4) {
        echo json_encode(['sucesso' => false, 'erro' => 'A senha deve ter pelo menos 4 caracteres']);
        exit;
    }
    if ($senha !== $confirmar_senha) {
        echo json_encode(['sucesso' => false, 'erro' => 'As senhas não coincidem']);
        exit;
    }

    // Verificar se email já existe
    $sql_check = "SELECT id FROM Usuarios WHERE email = ?";
    $stmt_check = $conexao->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Este email já está cadastrado']);
        exit;
    }

    // Inserir usuário
    $stmt = $conexao->prepare("INSERT INTO Usuarios (nome, email, senha, data_cadastro) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        echo json_encode([
            'sucesso' => true, 
            'mensagem' => 'Cadastro realizado com sucesso! Faça login para continuar.',
            'id' => $conexao->insert_id
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
    }
    exit;
}

echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido']);
$conexao->close();
?>

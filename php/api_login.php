<?php
/**
 * ============================================================
 * ARQUIVO: api_login.php
 * DESCRIÇÃO: API de autenticação (Login e Logout)
 * 
 * Esta API gerencia o sistema de login e logout do sistema.
 * Ela trabalha com sessões PHP para manter o estado de
 * autenticação entre as requisições.
 * 
 * Métodos:
 * - GET: Verificar se o usuário está logado
 * - POST: Fazer login ou logout
 * ============================================================
 */

// Defino o tipo de resposta como JSON
header('Content-Type: application/json');

// Inicio a sessão PHP (necessário para armazenar dados do usuário)
session_start();

// Incluo o arquivo de conexão com o banco
include 'conexao.php';

// Pego o método HTTP usado na requisição
$metodo = $_SERVER['REQUEST_METHOD'];

// ============================================================
// MÉTODO GET: Verificar status de autenticação
// O JavaScript chama isso para saber se o usuário está logado
// ============================================================
if ($metodo == 'GET') {
    // Verifico se existe um usuario_id na sessão
    if (isset($_SESSION['usuario_id'])) {
        // Usuário está logado - retorno os dados dele
        echo json_encode([
            'logado' => true,
            'usuario' => [
                'id' => $_SESSION['usuario_id'],
                'nome' => $_SESSION['usuario_nome'],
                'email' => $_SESSION['usuario_email']
            ]
        ]);
    } else {
        // Usuário não está logado
        echo json_encode(['logado' => false]);
    }
    exit;
}

// ============================================================
// MÉTODO POST: Fazer login ou logout
// ============================================================
if ($metodo == 'POST') {
    // Leio os dados enviados no corpo da requisição (JSON)
    $dados = json_decode(file_get_contents("php://input"), true);
    
    // --------------------------------------------
    // LOGOUT: Se a ação for 'logout', destruo a sessão
    // --------------------------------------------
    if (isset($dados['acao']) && $dados['acao'] == 'logout') {
        // Destruo a sessão (remove todos os dados)
        session_destroy();
        echo json_encode(['sucesso' => true, 'mensagem' => 'Logout realizado com sucesso']);
        exit;
    }

    // --------------------------------------------
    // LOGIN: Valido as credenciais
    // --------------------------------------------
    
    // Pego email e senha dos dados enviados
    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';

    // Validação: campos obrigatórios
    if (empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email e senha são obrigatórios']);
        exit;
    }
    
    // Validação: formato do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email inválido']);
        exit;
    }

    // Busco o usuário no banco de dados pelo email
    // Uso prepared statement para prevenir SQL Injection
    $sql = "SELECT id, nome, email, senha FROM Usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email); // "s" indica string
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verifico se encontrou o usuário
    if ($resultado->num_rows == 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Usuário não encontrado']);
        exit;
    }

    // Pego os dados do usuário encontrado
    $usuario = $resultado->fetch_assoc();

    // Verifico se a senha está correta
    // NOTA: Em produção, usar password_hash/password_verify para segurança!
    if ($usuario['senha'] !== $senha) {
        echo json_encode(['sucesso' => false, 'erro' => 'Senha incorreta']);
        exit;
    }

    // ============================================
    // LOGIN BEM SUCEDIDO
    // Armazeno os dados do usuário na sessão
    // ============================================
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];

    // Retorno sucesso com os dados do usuário
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

// Fecho a conexão com o banco
$conexao->close();
?>

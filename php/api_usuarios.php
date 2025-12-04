<?php
/**
 * ============================================================
 * ARQUIVO: api_usuarios.php
 * DESCRIÇÃO: API CRUD completa para gerenciamento de usuários
 * 
 * Esta API implementa todas as operações de CRUD para usuários:
 * - GET: Listar todos ou pesquisar usuários
 * - POST: Adicionar ou editar usuário
 * - POST com acao=excluir: Deletar usuário
 * 
 * Recursos:
 * - Pesquisa por nome ou email
 * - Validação de email único
 * - Proteção contra auto-exclusão
 * ============================================================
 */

// Defino o tipo de resposta como JSON
header('Content-Type: application/json');

// Inicio a sessão para verificar o usuário logado
session_start();

// Incluo a conexão com o banco
include 'conexao.php';

// Pego o método HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

// ============================================================
// 1. LISTAR/PESQUISAR USUÁRIOS (GET)
// ============================================================
if ($metodo == 'GET') {
    $lista = [];
    
    // Verifico se tem parâmetro de pesquisa
    if (isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
        // Pesquisa com LIKE para encontrar correspondências parciais
        // O % antes e depois permite encontrar o termo em qualquer posição
        $pesquisa = '%' . $_GET['pesquisa'] . '%';
        
        // Busco por nome OU email (OR)
        $sql = "SELECT id, nome, email, data_cadastro FROM Usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY nome ASC";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $pesquisa, $pesquisa);
        $stmt->execute();
        $resultado = $stmt->get_result();
    } else {
        // Sem pesquisa - retorno todos os usuários
        $sql = "SELECT id, nome, email, data_cadastro FROM Usuarios ORDER BY nome ASC";
        $resultado = $conexao->query($sql);
    }

    // Monto o array de resultados
    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            // Formato a data para exibição no padrão brasileiro
            $linha['data_cadastro_formatada'] = date('d/m/Y H:i', strtotime($linha['data_cadastro']));
            $lista[] = $linha;
        }
    }
    
    // Retorno a lista em JSON
    echo json_encode($lista);
    exit;
}

// ============================================================
// 2. ADICIONAR OU EDITAR USUÁRIO (POST)
// Se o ID está preenchido, é edição. Senão, é inserção.
// ============================================================
if ($metodo == 'POST' && !isset($_GET['acao'])) {
    // Leio os dados JSON
    $dados = json_decode(file_get_contents("php://input"), true);

    // Capturo os campos
    $nome = $dados['nome'] ?? '';
    $email = $dados['email'] ?? '';
    $senha = $dados['senha'] ?? '';
    $id = $dados['id'] ?? '';

    // Validações básicas
    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome é obrigatório']);
        exit;
    }
    if (empty($email)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email é obrigatório']);
        exit;
    }

    // ============================================
    // VERIFICAÇÃO DE EMAIL DUPLICADO
    // O email deve ser único, mas preciso excluir
    // o próprio usuário da verificação (na edição)
    // ============================================
    $sql_check = "SELECT id FROM Usuarios WHERE email = ? AND id != ?";
    $stmt_check = $conexao->prepare($sql_check);
    $id_check = empty($id) ? 0 : $id;  // Se é inserção, uso 0
    $stmt_check->bind_param("si", $email, $id_check);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Este email já está cadastrado']);
        exit;
    }

    // ============================================
    // ATUALIZAÇÃO (se tem ID)
    // ============================================
    if (!empty($id)) {
        if (!empty($senha)) {
            // Atualiza TODOS os campos, incluindo senha
            $stmt = $conexao->prepare("UPDATE Usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $email, $senha, $id);
        } else {
            // Atualiza SEM mudar a senha (o usuário deixou em branco)
            $stmt = $conexao->prepare("UPDATE Usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $id);
        }
    } else {
        // ============================================
        // INSERÇÃO (novo usuário)
        // ============================================
        if (empty($senha)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Senha é obrigatória para novo usuário']);
            exit;
        }
        $stmt = $conexao->prepare("INSERT INTO Usuarios (nome, email, senha, data_cadastro) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $nome, $email, $senha);
    }

    // Executo a operação
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'id' => $id ? $id : $conexao->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
    }
    exit;
}

// ============================================================
// 3. EXCLUIR USUÁRIO (POST com acao=excluir)
// ============================================================
if ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // ============================================
        // PROTEÇÃO: Não permitir excluir a si mesmo
        // Isso evita que o usuário trave o sistema
        // ============================================
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            echo json_encode(['sucesso' => false, 'erro' => 'Você não pode excluir seu próprio usuário enquanto está logado']);
            exit;
        }
        
        // Executo a exclusão
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

// Fecho a conexão
$conexao->close();
?>

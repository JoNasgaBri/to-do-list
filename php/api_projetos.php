<?php
/**
 * ============================================================
 * ARQUIVO: api_projetos.php
 * DESCRIÇÃO: API CRUD para gerenciamento de projetos
 * 
 * Esta API implementa as operações CRUD para projetos:
 * - GET: Listar projetos do usuário logado
 * - POST: Adicionar ou editar projeto
 * - POST com acao=excluir: Deletar projeto
 * 
 * Importante: Cada projeto pertence a um usuário específico,
 * então todas as operações são filtradas pelo usuario_id.
 * ============================================================
 */

// Defino o tipo de resposta como JSON
header('Content-Type: application/json');

// Inicio a sessão para pegar o ID do usuário logado
session_start();

// Incluo a conexão com o banco
include 'conexao.php';

// Pego o método HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

// ============================================================
// IDENTIFICAÇÃO DO USUÁRIO
// Pego o ID do usuário da sessão. Se não estiver logado,
// uso 1 como padrão (útil para testes durante desenvolvimento)
// ============================================================
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1;

// ============================================================
// 1. LISTAR PROJETOS (GET)
// Retorna apenas os projetos do usuário logado
// ============================================================
if ($metodo == 'GET') {
    $lista = [];
    
    // Query com filtro por usuario_id
    $sql = "SELECT id, nome, descricao, data_criacao FROM Projetos WHERE usuario_id = ? ORDER BY nome ASC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Monto o array de resultados
    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            // Formato a data para exibição
            $linha['data_criacao_formatada'] = date('d/m/Y H:i', strtotime($linha['data_criacao']));
            $lista[] = $linha;
        }
    }
    
    // Retorno a lista em JSON
    echo json_encode($lista);
    exit;
}

// ============================================================
// 2. ADICIONAR OU EDITAR PROJETO (POST)
// ============================================================
if ($metodo == 'POST' && !isset($_GET['acao'])) {
    // Leio os dados JSON
    $dados = json_decode(file_get_contents("php://input"), true);

    // Capturo os campos
    $nome = $dados['nome'] ?? '';
    $descricao = $dados['descricao'] ?? '';
    $id = $dados['id'] ?? '';

    // Validação: nome é obrigatório
    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome do projeto é obrigatório']);
        exit;
    }

    if (!empty($id)) {
        // ============================================
        // ATUALIZAÇÃO
        // Uso AND usuario_id = ? para garantir que o
        // usuário só possa editar seus próprios projetos
        // ============================================
        $stmt = $conexao->prepare("UPDATE Projetos SET nome = ?, descricao = ? WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ssii", $nome, $descricao, $id, $usuario_id);
    } else {
        // ============================================
        // INSERÇÃO
        // Associo o projeto ao usuário logado
        // ============================================
        $stmt = $conexao->prepare("INSERT INTO Projetos (nome, descricao, usuario_id, data_criacao) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssi", $nome, $descricao, $usuario_id);
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
// 3. EXCLUIR PROJETO (POST com acao=excluir)
// ATENÇÃO: Isso também exclui todas as tarefas do projeto!
// (Por causa do ON DELETE CASCADE no banco de dados)
// ============================================================
if ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Excluo apenas se pertencer ao usuário logado
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

// Fecho a conexão
$conexao->close();
?>

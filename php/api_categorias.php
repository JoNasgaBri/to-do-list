<?php
/**
 * ============================================================
 * ARQUIVO: api_categorias.php
 * DESCRIÇÃO: API CRUD para gerenciamento de categorias
 * 
 * Esta API gerencia as categorias do sistema. Cada categoria
 * tem um nome e uma cor personalizada que pode ser usada
 * para organizar visualmente as tarefas e projetos.
 * 
 * Métodos:
 * - GET: Listar todas as categorias
 * - POST: Adicionar ou editar categoria
 * - POST/DELETE com acao=excluir: Deletar categoria
 * ============================================================
 */

// Defino o tipo de resposta como JSON
header('Content-Type: application/json');

// Incluo a conexão com o banco
include 'conexao.php';

// Pego o método HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

// ============================================================
// 1. LISTAR CATEGORIAS (GET)
// ============================================================
if ($metodo == 'GET') {
    $lista = [];
    
    // Busco todas as categorias ordenadas por nome
    $sql = "SELECT id, nome, cor FROM Categorias ORDER BY nome ASC";
    $resultado = $conexao->query($sql);

    // Monto o array de resultados
    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            $lista[] = $linha;
        }
    }
    
    // Retorno a lista em JSON
    echo json_encode($lista);
    exit;
}

// ============================================================
// 2. ADICIONAR OU EDITAR CATEGORIA (POST)
// ============================================================
if ($metodo == 'POST') {
    // Leio os dados JSON enviados pelo JavaScript
    $dados = json_decode(file_get_contents("php://input"), true);

    // Capturo os campos
    $nome = $dados['nome'] ?? '';
    $cor = $dados['cor'] ?? '#FFFFFF';  // Branco como cor padrão
    $id = $dados['id'] ?? '';

    // Validação: nome é obrigatório
    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Nome é obrigatório']);
        exit;
    }

    if (!empty($id)) {
        // ============================================
        // ATUALIZAÇÃO
        // ============================================
        $stmt = $conexao->prepare("UPDATE Categorias SET nome = ?, cor = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $cor, $id);
    } else {
        // ============================================
        // INSERÇÃO
        // ============================================
        $stmt = $conexao->prepare("INSERT INTO Categorias (nome, cor) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cor);
    }

    // Executo a operação
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conexao->error]);
    }
    exit;
}

// ============================================================
// 3. EXCLUIR CATEGORIA
// Aceito tanto DELETE quanto POST com acao=excluir
// (para compatibilidade com diferentes configurações de servidor)
// ============================================================
if ($metodo == 'DELETE' || ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir')) {
    // Pego o ID da query string
    $id = $_GET['id'] ?? null;
    
    if($id) {
        // Executo a exclusão
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

// Fecho a conexão
$conexao->close();
?>

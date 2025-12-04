<?php
/**
 * ============================================================
 * ARQUIVO: api_tarefas.php
 * DESCRIÇÃO: API CRUD para gerenciamento de tarefas
 * 
 * Esta API é a mais complexa do sistema! Ela gerencia tarefas
 * que são vinculadas a projetos. Cada tarefa possui:
 * - Título e descrição
 * - Status (Pendente, Em Andamento, Concluída)
 * - Data limite (para controle de prazos)
 * 
 * Funcionalidades especiais:
 * - Calcula dias restantes/atrasados
 * - Indica tarefas vencidas ou próximas do vencimento
 * - Lista projetos para o select do formulário
 * ============================================================
 */

// Defino o tipo de resposta como JSON
header('Content-Type: application/json');

// Inicio a sessão para pegar o usuário logado
session_start();

// Incluo a conexão com o banco
include 'conexao.php';

// Pego o método HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

// ============================================================
// IDENTIFICAÇÃO DO USUÁRIO
// Pego o ID do usuário da sessão, ou uso 1 como padrão
// ============================================================
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1;

// ============================================================
// 1. LISTAR TAREFAS OU PROJETOS (GET)
// ============================================================
if ($metodo == 'GET') {
    // --------------------------------------------
    // Se pediu lista de projetos (para popular o select)
    // --------------------------------------------
    if (isset($_GET['tipo']) && $_GET['tipo'] == 'projetos') {
        $lista = [];
        // Busco apenas os projetos do usuário logado
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
    
    // --------------------------------------------
    // Lista de tarefas (comportamento padrão)
    // Uso JOIN para pegar o nome do projeto
    // Filtro por usuario_id através da tabela Projetos
    // --------------------------------------------
    $lista = [];
    $sql = "SELECT t.id, t.titulo, t.descricao, t.status, t.data_limite, 
                   p.nome AS nome_projeto, t.projeto_id 
            FROM Tarefas AS t
            JOIN Projetos AS p ON t.projeto_id = p.id
            WHERE p.usuario_id = ? 
            ORDER BY t.data_limite ASC";  // Ordeno por data para priorizar próximas
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while($linha = $resultado->fetch_assoc()) {
            // ============================================
            // PROCESSAMENTO DA DATA LIMITE
            // Aqui calculo informações extras sobre prazos
            // ============================================
            if ($linha['data_limite'] && $linha['data_limite'] != '0000-00-00') {
                // Formato a data para exibição
                $linha['data_limite_formatada'] = date('d/m/Y', strtotime($linha['data_limite']));
                
                // Calculo a diferença em dias
                $hoje = new DateTime();
                $limite = new DateTime($linha['data_limite']);
                $diff = $hoje->diff($limite);
                
                // Dias positivos = falta, negativos = atrasado
                $dias = $diff->days * ($diff->invert ? -1 : 1);
                
                // Adiciono informações extras para o JavaScript usar
                $linha['dias_restantes'] = $dias;
                $linha['vencida'] = $dias < 0;              // Está atrasada?
                $linha['proxima'] = $dias >= 0 && $dias <= 3;  // Próxima do vencimento?
            } else {
                // Se não tem data limite definida
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

// ============================================================
// 2. ADICIONAR OU EDITAR TAREFA (POST)
// ============================================================
if ($metodo == 'POST' && !isset($_GET['acao'])) {
    // Leio os dados JSON
    $dados = json_decode(file_get_contents("php://input"), true);

    // Capturo os campos
    $titulo = $dados['titulo'] ?? '';
    $descricao = $dados['descricao'] ?? '';
    $projeto_id = $dados['projeto_id'] ?? '';
    $data_limite = $dados['data_limite'] ?? null;
    $status = $dados['status'] ?? 'Pendente';  // Valor padrão
    $id = $dados['id'] ?? '';

    // Validação: título obrigatório
    if (empty($titulo)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Título da tarefa é obrigatório']);
        exit;
    }
    // Validação: projeto obrigatório
    if (empty($projeto_id)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Selecione um projeto']);
        exit;
    }

    // Trato data vazia (input vazio vira null)
    if (empty($data_limite)) {
        $data_limite = null;
    }

    if (!empty($id)) {
        // ============================================
        // ATUALIZAÇÃO
        // ============================================
        $stmt = $conexao->prepare("UPDATE Tarefas SET titulo = ?, descricao = ?, projeto_id = ?, data_limite = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssissi", $titulo, $descricao, $projeto_id, $data_limite, $status, $id);
    } else {
        // ============================================
        // INSERÇÃO
        // ============================================
        $stmt = $conexao->prepare("INSERT INTO Tarefas (titulo, descricao, projeto_id, data_limite, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $titulo, $descricao, $projeto_id, $data_limite, $status);
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
// 3. EXCLUIR TAREFA (POST com acao=excluir)
// ============================================================
if ($metodo == 'POST' && isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Excluo a tarefa pelo ID
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

// Fecho a conexão
$conexao->close();
?>

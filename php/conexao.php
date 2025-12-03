<?php
// Configurações do Banco de Dados
$servidor = "localhost";
$usuario = "root"; 
$senha = "Admin@000"; 
$banco = "todo_list_db";

// Função para mostrar erro amigável
function mostrarErroConexao($mensagemTecnica) {
    // Se a requisição espera JSON, retorna erro em JSON
    $isJson = (
        isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
    ) || (
        isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
    );
    
    if ($isJson) {
        header('Content-Type: application/json');
        echo json_encode([
            'sucesso' => false, 
            'erro' => 'Falha na conexão com o banco de dados. Verifique se o MySQL está rodando.'
        ]);
        exit;
    }
    
    // Caso contrário, mostra mensagem HTML amigável
    echo "<div style='font-family: Arial; padding: 20px; background: #fee2e2; border: 2px solid #dc2626; border-radius: 12px; margin: 20px; color: #991b1b; max-width: 600px;'>
        <h2 style='margin-top:0;'>⚠️ Erro de Conexão com o Banco de Dados</h2>
        <p>Não foi possível conectar ao banco de dados MySQL.</p>
        <p><strong>Possíveis causas:</strong></p>
        <ul>
            <li>O servidor MySQL não está rodando</li>
            <li>Usuário ou senha incorretos</li>
            <li>O banco de dados '<strong>todo_list_db</strong>' não existe</li>
        </ul>
        <p><strong>Como resolver:</strong></p>
        <ol>
            <li>Verifique se o MySQL está rodando: <code>sudo service mysql status</code></li>
            <li>Execute o script SQL: <code>mysql -u root -p < sql/database_completo.sql</code></li>
            <li>Verifique a senha em <code>php/conexao.php</code></li>
        </ol>
        <details>
            <summary style='cursor:pointer; color:#666;'>Detalhes técnicos</summary>
            <pre style='background:#fef2f2; padding:10px; border-radius:4px; overflow:auto;'>" . htmlspecialchars($mensagemTecnica) . "</pre>
        </details>
    </div>";
    exit;
}

// Tenta criar a conexão com tratamento de exceção
try {
    $conexao = new mysqli($servidor, $usuario, $senha, $banco);
    
    // Verifica se a conexão foi bem-sucedida
    if ($conexao->connect_error) {
        mostrarErroConexao($conexao->connect_error);
    }
    
    $conexao->set_charset("utf8");
    
} catch (mysqli_sql_exception $e) {
    mostrarErroConexao($e->getMessage());
} catch (Exception $e) {
    mostrarErroConexao($e->getMessage());
}
?>
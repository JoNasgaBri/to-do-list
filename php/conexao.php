<?php
// Configurações do Banco de Dados
$servidor = "localhost";
$usuario = "root"; 
$senha = ""; 
$banco = "todo_list_db";

// Cria a conexão com o banco de dados usando MySQLi
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8");


?>
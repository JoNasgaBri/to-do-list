# 📋 To-Do List - Gerenciador de Tarefas

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

**Sistema completo de gerenciamento de tarefas e projetos**

[Funcionalidades](#-funcionalidades) •
[Instalação](#-instalação) •
[Estrutura](#-estrutura-do-projeto) •
[API](#-documentação-da-api) •
[Autores](#-autores)

</div>

---

## ✨ Funcionalidades

### 👤 Gestão de Usuários
- ✅ Cadastro com validação completa (nome, email, data de nascimento)
- ✅ Login seguro com sessão PHP
- ✅ Logout com destruição de sessão
- ✅ Pesquisa de usuários por nome ou email
- ✅ Edição e exclusão de usuários

### 📁 Gestão de Projetos
- ✅ Criar, editar e excluir projetos
- ✅ Descrição detalhada para cada projeto
- ✅ Listagem organizada

### ✅ Gestão de Tarefas
- ✅ Criar tarefas vinculadas a projetos
- ✅ Definir data limite
- ✅ Status: Pendente, Em Andamento, Concluída
- ✅ Edição e exclusão

### 🏷️ Categorias
- ✅ Criar categorias personalizadas
- ✅ Cores customizáveis
- ✅ Organização visual

### 🔐 Segurança
- ✅ Proteção de páginas (requer login)
- ✅ Validação no frontend e backend
- ✅ Sessões PHP seguras
- ✅ Prepared Statements (proteção contra SQL Injection)

---

## 🚀 Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Apache (XAMPP, LAMP, etc.)

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/JoNasgaBri/to-do-list.git
```

2. **Mova para o diretório do servidor web**
```bash
# Linux
sudo mv to-do-list /var/www/html/

# Windows (XAMPP)
move to-do-list C:\xampp\htdocs\
```

3. **Crie o banco de dados**
```bash
mysql -u root -p < /var/www/html/to-do-list/sql/database_completo.sql
```

4. **Configure a conexão**

Edite o arquivo `php/conexao.php` com suas credenciais:
```php
$servidor = "localhost";
$usuario = "root";
$senha = "SUA_SENHA";
$banco = "todo_list_db";
```

5. **Acesse o sistema**
```
http://localhost/to-do-list/
```

---

## 📁 Estrutura do Projeto

```
to-do-list/
│
├── 📄 index.html              # Página inicial
│
├── 📁 css/
│   └── style.css              # Estilos do sistema
│
├── 📁 js/
│   ├── auth.js                # Autenticação global
│   ├── cadastro_controle.js   # Lógica do cadastro
│   ├── categorias_controle.js # Lógica das categorias
│   ├── login_controle.js      # Lógica do login
│   ├── projetos_controle.js   # Lógica dos projetos
│   ├── tarefas_controle.js    # Lógica das tarefas
│   └── usuarios_controle.js   # Lógica dos usuários
│
├── 📁 pages/
│   ├── cadastro.html          # Página de cadastro
│   ├── categorias.html        # Gestão de categorias
│   ├── login.html             # Página de login
│   ├── projetos.html          # Gestão de projetos
│   ├── tarefas.html           # Gestão de tarefas
│   └── usuarios.html          # Gestão de usuários
│
├── 📁 php/
│   ├── api_cadastro.php       # API de cadastro
│   ├── api_categorias.php     # API de categorias
│   ├── api_login.php          # API de login/sessão
│   ├── api_projetos.php       # API de projetos
│   ├── api_tarefas.php        # API de tarefas
│   ├── api_usuarios.php       # API de usuários
│   ├── conexao.php            # Conexão com MySQL
│   ├── funcoes.php            # Funções auxiliares
│   └── testar_conexao.php     # Teste de conexão
│
└── 📁 sql/
    └── database_completo.sql  # Script do banco de dados
```

---

## 🔌 Documentação da API

### Autenticação

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/php/api_login.php` | Fazer login |
| `GET` | `/php/api_login.php` | Verificar sessão |
| `POST` | `/php/api_login.php` | Logout (com `acao: 'logout'`) |
| `POST` | `/php/api_cadastro.php` | Cadastrar usuário |

### Usuários

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/php/api_usuarios.php` | Listar todos |
| `GET` | `/php/api_usuarios.php?pesquisa=termo` | Pesquisar |
| `POST` | `/php/api_usuarios.php` | Criar/Editar |
| `POST` | `/php/api_usuarios.php?acao=excluir&id=X` | Excluir |

### Projetos, Tarefas e Categorias

Seguem o mesmo padrão da API de usuários.

---

## 🎨 Screenshots

### Página Inicial
- Design moderno com gradientes
- Cards de funcionalidades
- Estatísticas em tempo real

### Sistema de Login
- Validação de campos
- Mensagens de erro claras
- Redirecionamento automático

### Gestão de Usuários
- Tabela com todos os usuários
- Pesquisa em tempo real
- Botões de editar/excluir

---

## 🛡️ Arquitetura

O projeto segue o padrão de **separação de responsabilidades**:

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Frontend     │────▶│    Backend      │────▶│    Database     │
│   HTML + JS     │     │   PHP (APIs)    │     │     MySQL       │
│                 │◀────│                 │◀────│                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
     Fetch API              JSON                    SQL
```

**Benefícios:**
- ✅ Código HTML limpo (sem PHP misturado)
- ✅ APIs reutilizáveis
- ✅ Fácil manutenção
- ✅ Segurança aprimorada

---

## 📝 Validações Implementadas

### Cadastro de Usuário

| Campo | Regra |
|-------|-------|
| Nome | Mínimo 3 caracteres, apenas letras |
| Email | Formato válido, único no sistema |
| Data de Nascimento | Idade entre 10 e 120 anos |
| Senha | Mínimo 4 caracteres |
| Confirmar Senha | Deve coincidir |

---

## 🧪 Testando o Sistema

1. **Testar conexão com banco:**
```
http://localhost/to-do-list/php/testar_conexao.php
```

2. **Usuário padrão para testes:**
```
Email: admin@todolist.com
Senha: admin123
```

---

## 👨‍💻 Autores

<table>
  <tr>
    <td align="center">
      <b>Jonas</b><br>
      <sub>Desenvolvedor</sub>
    </td>
    <td align="center">
      <b>Antonio</b><br>
      <sub>Desenvolvedor</sub>
    </td>
  </tr>
</table>

---

## 📄 Licença

Este projeto foi desenvolvido para fins acadêmicos.

---

<div align="center">

**⭐ Se este projeto te ajudou, deixe uma estrela!**

Feito com ❤️ para a disciplina de Desenvolvimento Web

</div>

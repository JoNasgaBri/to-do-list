/**
 * Arquivo de autenticação - Gerencia login/logout em todas as páginas
 * Incluir este arquivo em TODAS as páginas do sistema
 */

// Páginas que requerem login
const PAGINAS_PROTEGIDAS = ['usuarios.html', 'projetos.html', 'tarefas.html', 'categorias.html'];

// Verificar imediatamente se precisa de proteção
(function() {
    const paginaAtual = window.location.pathname.split('/').pop();
    const isInPages = window.location.pathname.includes('/pages/');
    
    // Se é uma página protegida, esconder o conteúdo até verificar login
    if (PAGINAS_PROTEGIDAS.includes(paginaAtual)) {
        document.documentElement.style.visibility = 'hidden';
    }
})();

document.addEventListener("DOMContentLoaded", function() {
    verificarAutenticacao();
});

// Verifica se usuário está logado e atualiza a interface
function verificarAutenticacao() {
    const isInPages = window.location.pathname.includes('/pages/');
    const apiPath = isInPages ? '../php/api_login.php' : 'php/api_login.php';
    const loginPath = isInPages ? 'login.html' : 'pages/login.html';
    const paginaAtual = window.location.pathname.split('/').pop();
    
    fetch(apiPath)
        .then(response => response.json())
        .then(data => {
            // Verificar se a página atual requer login
            if (PAGINAS_PROTEGIDAS.includes(paginaAtual) && !data.logado) {
                alert('Você precisa fazer login para acessar esta página!');
                window.location.href = loginPath;
                return; // Não continuar executando
            }
            
            // Se chegou aqui, pode mostrar a página
            document.documentElement.style.visibility = 'visible';
            
            // Atualizar menu
            atualizarMenu(data, isInPages);
        })
        .catch(error => {
            console.error('Erro ao verificar autenticação:', error);
            // Em caso de erro, mostrar a página mas não proteger
            document.documentElement.style.visibility = 'visible';
        });
}

// Atualiza o menu de navegação baseado no estado de login
function atualizarMenu(data, isInPages) {
    const nav = document.querySelector('nav ul');
    if (!nav) return;
    
    // Remover itens de login/cadastro/logout existentes
    const itensRemover = nav.querySelectorAll('.auth-item');
    itensRemover.forEach(item => item.remove());
    
    // Adicionar info do usuário no header se logado
    const header = document.querySelector('header');
    let userInfo = document.getElementById('user-info');
    
    if (data.logado) {
        // Mostrar nome do usuário
        if (!userInfo) {
            userInfo = document.createElement('div');
            userInfo.id = 'user-info';
            userInfo.style.cssText = 'color: white; font-size: 0.9em; margin-top: 5px;';
            header.appendChild(userInfo);
        }
        userInfo.innerHTML = `👤 Olá, <strong>${data.usuario.nome}</strong>`;
        
        // Adicionar botão de logout no menu
        const logoutItem = document.createElement('li');
        logoutItem.className = 'auth-item';
        logoutItem.innerHTML = `<a href="#" onclick="fazerLogout(); return false;" style="color: #ff6b6b;">Sair</a>`;
        nav.appendChild(logoutItem);
    } else {
        // Remover info do usuário
        if (userInfo) userInfo.remove();
        
        // Adicionar links de login e cadastro
        const prefix = isInPages ? '' : 'pages/';
        
        const loginItem = document.createElement('li');
        loginItem.className = 'auth-item';
        loginItem.innerHTML = `<a href="${prefix}login.html">Login</a>`;
        nav.appendChild(loginItem);
        
        const cadastroItem = document.createElement('li');
        cadastroItem.className = 'auth-item';
        cadastroItem.innerHTML = `<a href="${prefix}cadastro.html">Cadastre-se</a>`;
        nav.appendChild(cadastroItem);
    }
}

// Função de logout global
function fazerLogout() {
    const isInPages = window.location.pathname.includes('/pages/');
    const apiPath = isInPages ? '../php/api_login.php' : 'php/api_login.php';
    const loginPath = isInPages ? 'login.html' : 'pages/login.html';
    
    fetch(apiPath, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ acao: 'logout' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            alert('Você saiu do sistema!');
            window.location.href = loginPath;
        }
    })
    .catch(error => {
        console.error('Erro ao fazer logout:', error);
    });
}

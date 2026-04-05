function toggleMenu(event, menuName) {
    // Esconde todos os conteúdos do menu
    const contents = document.querySelectorAll('.menu-content');
    contents.forEach(content => content.classList.remove('active'));

    // Remove a classe 'active' de todos os botões
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Mostra o menu atual e ativa o botão clicado
    document.getElementById(menuName).classList.add('active');
    event.currentTarget.classList.add('active');
}

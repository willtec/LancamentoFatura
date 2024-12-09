document.addEventListener('DOMContentLoaded', function () {
    const mensagens = document.querySelectorAll('.mensagem');
    mensagens.forEach(mensagem => {
        setTimeout(() => {
            mensagem.style.display = 'none';
        }, 5000); // Oculta mensagens após 5 segundos
    });
});

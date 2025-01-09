// Aguarde o carregamento completo do DOM
document.addEventListener('DOMContentLoaded', function () {
    // Função para confirmar exclusão
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este usuário?')) {
            window.location.href = `/LancamentoFatura/usuarios/excluir/${id}`;
        }
    }

    // Busca em tempo real
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const nome = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const email = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const nivel = row.querySelector('.badge')?.textContent.toLowerCase() || '';

                if (nome.includes(searchTerm) || email.includes(searchTerm) || nivel.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    } else {
        console.error('Elemento com ID "searchInput" não encontrado.');
    }

    // Botão de atualizar tabela
    const refreshButton = document.getElementById('refreshTable');
    if (refreshButton) {
        refreshButton.addEventListener('click', function () {
            // Atualizar a tabela simulando uma recarga de página
            location.reload();
        });
    } else {
        console.error('Elemento com ID "refreshTable" não encontrado.');
    }
});

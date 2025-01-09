document.addEventListener('DOMContentLoaded', function() {
    const transportadoraInput = document.getElementById('transportadora');
    const suggestionsList = document.getElementById('transportadora-suggestions');
    const valorInput = document.getElementById('valor');

    // Função para formatar o valor como moeda brasileira
    function formatCurrency(value) {
        value = value.replace(/\D/g, ''); // Remove tudo que não for dígito
        value = (value / 100).toFixed(2) + ''; // Divide por 100 e fixa duas casas decimais
        value = value.replace('.', ','); // Substitui o ponto pela vírgula
        value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'); // Adiciona pontos como separadores de milhar
        return 'R$ ' + value; // Adiciona o símbolo de moeda no início
    }

    // Função para manter o cursor após o valor formatado
    function setCursorPosition(el, position) {
        el.setSelectionRange(position, position);
    }

    // Inicializar o campo de valor com a formatação padrão
    function initializeCurrencyInput() {
        valorInput.value = formatCurrency(valorInput.value);
        setCursorPosition(valorInput, valorInput.value.length);
    }

    // Event listener para o campo de valor
    valorInput.addEventListener('input', function() {
        let cursorPosition = valorInput.selectionStart;
        const originalLength = valorInput.value.length;

        valorInput.value = formatCurrency(valorInput.value);

        const newLength = valorInput.value.length;
        cursorPosition = cursorPosition + (newLength - originalLength);

        // Garantir que o cursor não fique antes de "R$"
        if (cursorPosition < 3) {
            cursorPosition = 3;
        }

        setCursorPosition(valorInput, cursorPosition);
    });

    // Chamada para inicializar o campo de valor ao carregar a página
    initializeCurrencyInput();

    // Event listener para o campo de transportadora
    transportadoraInput.addEventListener('input', function() {
        const query = transportadoraInput.value.trim();

        if (query.length >= 2) { // Mostrar sugestões apenas se a query tiver 2 caracteres ou mais
            fetch('/api/transportadoras?q=' + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    suggestionsList.innerHTML = '';
                    suggestionsList.classList.add('hidden');

                    if (data.length > 0) {
                        data.forEach(item => {
                            const suggestionItem = document.createElement('li');
                            suggestionItem.textContent = `${item.codigo} - ${item.nome} - ${item.cnpj}`;
                            suggestionItem.classList.add('suggestion-item');
                            suggestionItem.addEventListener('click', function() {
                                transportadoraInput.value = item.nome;
                                suggestionsList.classList.add('hidden');
                            });
                            suggestionsList.appendChild(suggestionItem);
                        });

                        suggestionsList.classList.remove('hidden');
                    } else {
                        suggestionsList.innerHTML = "<li class='px-4 py-2 text-gray-500'>Nenhuma transportadora encontrada</li>";
                        suggestionsList.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar sugestões:', error);
                    suggestionsList.classList.add('hidden');
                });
        } else {
            suggestionsList.classList.add('hidden');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
  const transportadoraInput = document.getElementById('transportadora');
  const suggestionsList = document.getElementById('transportadora-suggestions');

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
                          suggestionItem.textContent = `${item.codigo} - ${item.nome}`;
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
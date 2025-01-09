document.addEventListener("DOMContentLoaded", () => {
  const transportadoraInput = document.getElementById("transportadora_id");
  const suggestionsList = document.getElementById("transportadora-suggestions");

  if (transportadoraInput && suggestionsList) {
    transportadoraInput.addEventListener("input", async () => {
      const query = transportadoraInput.value.trim();

      // Esconde a lista se o campo estiver vazio
      if (query === "") {
        suggestionsList.innerHTML = "";
        suggestionsList.classList.add("hidden");
        return;
      }

      try {
        // Faz a requisição para o backend
        const response = await fetch(
          `/../../routes/api/transportadoras?q=${encodeURIComponent(query)}`
        );

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const suggestions = await response.json();

        // Limpa a lista
        suggestionsList.innerHTML = "";

        if (suggestions.length > 0) {
          // Adiciona cada sugestão à lista
          suggestions.forEach((item) => {
            const listItem = document.createElement("li");
            listItem.textContent = `${item.codigo} - ${item.nome}`;
            listItem.classList.add(
              "px-4",
              "py-2",
              "hover:bg-gray-200",
              "cursor-pointer"
            );
            listItem.addEventListener("click", () => {
              transportadoraInput.value = `${item.codigo} - ${item.nome}`;
              suggestionsList.innerHTML = "";
              suggestionsList.classList.add("hidden");
            });
            suggestionsList.appendChild(listItem);
          });

          suggestionsList.classList.remove("hidden");
        } else {
          suggestionsList.innerHTML =
            "<li class='px-4 py-2 text-gray-500'>Nenhuma transportadora encontrada</li>";
          suggestionsList.classList.remove("hidden");
        }
      } catch (error) {
        console.error("Erro ao buscar transportadoras:", error);
      }
    });

    // Esconde a lista ao clicar fora
    document.addEventListener("click", (event) => {
      if (
        !transportadoraInput.contains(event.target) &&
        !suggestionsList.contains(event.target)
      ) {
        suggestionsList.innerHTML = "";
        suggestionsList.classList.add("hidden");
      }
    });
  }
});

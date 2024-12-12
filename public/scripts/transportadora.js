document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.getElementById("search-form");
  const paginationButtons = document.querySelectorAll(".btn-pagination");

  // Manipulação da busca
  searchForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const searchInput = e.target.querySelector('input[name="search"]');
    const searchTerm = searchInput.value.trim();

    // Redireciona para a mesma página com parâmetros de busca
    window.location.href = `/transportadoras?search=${encodeURIComponent(
      searchTerm
    )}`;
  });

  // Manipulação da paginação
  paginationButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (button.hasAttribute("disabled")) return;

      const page = e.target.getAttribute("data-page");
      const searchInput = document.querySelector('input[name="search"]');
      const searchTerm = searchInput ? searchInput.value.trim() : "";

      // Construir URL com parâmetros de página e busca
      const url = new URL(window.location);
      url.searchParams.set("page", page);
      if (searchTerm) {
        url.searchParams.set("search", searchTerm);
      }

      window.location.href = url.toString();
    });
  });
});

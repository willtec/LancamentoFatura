document.addEventListener("DOMContentLoaded", function () {
  const themeToggleBtn = document.getElementById("themeToggle");
  const icon = themeToggleBtn.querySelector("i");

  function getInitialTheme() {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
      return savedTheme;
    }
    return window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
  }

  function applyTheme(theme) {
    // Toggle body class
    document.body.classList.remove("theme-light", "theme-dark");
    document.body.classList.add(`theme-${theme}`);

    // Update icon
    icon.className = theme === "dark" ? "fas fa-sun" : "fas fa-moon";

    // Store preference
    localStorage.setItem("theme", theme);
  }

  // Initialize theme
  const initialTheme = getInitialTheme();
  applyTheme(initialTheme);

  // Handle button click
  themeToggleBtn.addEventListener("click", function () {
    const currentTheme = localStorage.getItem("theme") || initialTheme;
    const newTheme = currentTheme === "dark" ? "light" : "dark";

    // Add rotation class
    themeToggleBtn.classList.add("rotating");

    // Remove class after animation
    setTimeout(() => {
      themeToggleBtn.classList.remove("rotating");
    }, 500);

    applyTheme(newTheme);
  });

  // Handle system theme changes
  window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", function (e) {
      if (!localStorage.getItem("theme")) {
        applyTheme(e.matches ? "dark" : "light");
      }
    });
});

document.addEventListener("DOMContentLoaded", function () {
  const senhaInput = document.getElementById("senha");
  const togglePassword = document.getElementById("togglePassword");
  const strengthMeter = document.querySelector(".password-strength-meter");
  const strengthText = document.getElementById("strengthText");
  const submitBtn = document.getElementById("submitBtn");
  const nomeInput = document.getElementById("nome");
  const emailInput = document.getElementById("email");
  const nomeError = document.getElementById("nomeError");
  const emailError = document.getElementById("emailError");

  // Requisitos da senha
  const requirements = {
    length: { regex: /.{8,}/, element: document.getElementById("length") },
    uppercase: {
      regex: /[A-Z]/,
      element: document.getElementById("uppercase"),
    },
    lowercase: {
      regex: /[a-z]/,
      element: document.getElementById("lowercase"),
    },
    number: { regex: /[0-9]/, element: document.getElementById("number") },
    special: {
      regex: /[!@#$%^&*]/,
      element: document.getElementById("special"),
    },
  };

  // Alternar visibilidade da senha
  togglePassword.addEventListener("click", function () {
    const type =
      senhaInput.getAttribute("type") === "password" ? "text" : "password";
    senhaInput.setAttribute("type", type);
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });

  // Verificar força da nova senha
  senhaInput.addEventListener("input", function () {
    const password = this.value;
    let strength = 0;
    let validRequirements = 0;

    Object.keys(requirements).forEach((req) => {
      const isValid = requirements[req].regex.test(password);
      requirements[req].element.classList.toggle("valid", isValid);
      requirements[req].element.classList.toggle("invalid", !isValid);
      requirements[req].element.querySelector("i").className = `fas ${
        isValid ? "fa-check-circle" : "fa-circle"
      }`;

      if (isValid) {
        strength += 20;
        validRequirements++;
      }
    });

    strengthMeter.className = "password-strength-meter";

    if (strength >= 100) {
      strengthMeter.classList.add("strength-strong");
      strengthText.textContent = "Forte";
    } else if (strength >= 80) {
      strengthMeter.classList.add("strength-good");
      strengthText.textContent = "Boa";
    } else if (strength >= 60) {
      strengthMeter.classList.add("strength-medium");
      strengthText.textContent = "Média";
    } else {
      strengthMeter.classList.add("strength-weak");
      strengthText.textContent = "Fraca";
    }
  });

  // Validação básica de campos
  document.getElementById("userForm").addEventListener("submit", function (e) {
    if (!nomeInput.value.trim() || !emailInput.value.trim()) {
      e.preventDefault();
      alert("Por favor, preencha todos os campos obrigatórios.");
    }
  });

  // Limpeza de mensagens de erro ao digitar
  [nomeInput, emailInput].forEach((input) =>
    input.addEventListener("input", () => {
      nomeError.textContent = "";
      emailError.textContent = "";
    })
  );
});

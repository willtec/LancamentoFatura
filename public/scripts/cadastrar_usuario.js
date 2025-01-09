document.addEventListener("DOMContentLoaded", function () {
  const senhaInput = document.getElementById("senha");
  const togglePassword = document.getElementById("togglePassword");
  const strengthMeter = document.querySelector(".password-strength-meter");
  const strengthText = document.getElementById("strengthText");
  const submitBtn = document.getElementById("submitBtn");
  const form = document.getElementById("userForm");
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

  // Função para verificar disponibilidade de nome e email
  async function verificarDisponibilidade() {
    const nome = nomeInput.value.trim();
    const email = emailInput.value.trim();

    // Limpar mensagens de erro ao iniciar a verificação
    nomeError.textContent = "";
    emailError.textContent = "";

    if (!nome && !email) return;

    const response = await fetch("/LancamentoFatura/usuarios/verificar", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(
        email
      )}`,
    });

    const data = await response.json();

    const nomeValido = !data.nome_existe;
    const emailValido = !data.email_existe;

    nomeInput.classList.toggle("invalid", !nomeValido);
    emailInput.classList.toggle("invalid", !emailValido);

    if (!nomeValido) {
      nomeError.textContent = "Este nome já está cadastrado. Escolha outro.";
    }

    if (!emailValido) {
      emailError.textContent = "Este e-mail já está cadastrado. Escolha outro.";
    }

    // Desabilitar o botão de envio se nome ou email já existirem
    submitBtn.disabled = !nomeValido || !emailValido;
  }

  // Verificar disponibilidade ao sair dos campos de nome e email
  nomeInput.addEventListener("blur", verificarDisponibilidade);
  emailInput.addEventListener("blur", verificarDisponibilidade);

  // Toggle visualização da senha
  togglePassword.addEventListener("click", function () {
    const type =
      senhaInput.getAttribute("type") === "password" ? "text" : "password";
    senhaInput.setAttribute("type", type);
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
  });

  // Verificar força da senha
  senhaInput.addEventListener("input", function () {
    const password = this.value;
    let strength = 0;
    let validRequirements = 0;

    // Verificar cada requisito
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

    // Atualizar medidor de força
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

    // Habilitar/desabilitar botão de submit
    submitBtn.disabled = validRequirements < 5;
  });

  // Validação do formulário
  form.addEventListener("submit", function (event) {
    const isFormValid = form.checkValidity();
    if (!isFormValid) {
      event.preventDefault();
      alert(
        "Por favor, preencha todos os campos corretamente antes de enviar o formulário."
      );
    }
  });
});

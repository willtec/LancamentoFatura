document.addEventListener("DOMContentLoaded", function () {
  const cnpjInput = document.getElementById("cnpj");

  // Função para aplicar a máscara de CNPJ
  function aplicarMascaraCNPJ(cnpj) {
    return cnpj.replace(
      /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,
      "$1.$2.$3/$4-$5"
    );
  }

  // Formatar o CNPJ ao carregar a página
  if (cnpjInput && cnpjInput.value) {
    const value = cnpjInput.value.replace(/\D/g, ""); // Remove caracteres não numéricos
    cnpjInput.value = aplicarMascaraCNPJ(value);
  }

  // Máscara dinâmica para o campo de CNPJ
  cnpjInput.addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, ""); // Remove caracteres não numéricos

    // Limita a 14 caracteres para evitar erros
    if (value.length > 14) {
      value = value.slice(0, 14);
    }

    // Aplica a máscara
    e.target.value = aplicarMascaraCNPJ(value);
  });

  // Validação de CNPJ no envio do formulário
  document.querySelector("form").addEventListener("submit", function (e) {
    // Remove a máscara do CNPJ antes de enviar
    cnpjInput.value = cnpjInput.value.replace(/\D/g, ""); // Remove pontuação

    // Valida o CNPJ
    if (!validarCNPJ()) {
      e.preventDefault(); // Impede o envio do formulário se inválido
    }
  });

  // Função para validar CNPJ
  function validarCNPJ() {
    const cnpj = cnpjInput.value.replace(/\D/g, ""); // Remove máscara para validação
    if (!cnpjValido(cnpj)) {
      alert("CNPJ inválido. Por favor, verifique os dados.");
      return false;
    }
    return true;
  }

  // Lógica para verificar a validade do CNPJ
  function cnpjValido(cnpj) {
    if (cnpj.length !== 14) return false;

    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1+$/.test(cnpj)) return false;

    let soma = 0;
    let peso = 5;

    // Cálculo do primeiro dígito verificador
    for (let i = 0; i < 12; i++) {
      soma += parseInt(cnpj[i]) * peso;
      peso = peso === 2 ? 9 : peso - 1;
    }

    let resto = soma % 11;
    let digito1 = resto < 2 ? 0 : 11 - resto;

    if (parseInt(cnpj[12]) !== digito1) return false;

    // Cálculo do segundo dígito verificador
    soma = 0;
    peso = 6;

    for (let i = 0; i < 13; i++) {
      soma += parseInt(cnpj[i]) * peso;
      peso = peso === 2 ? 9 : peso - 1;
    }

    resto = soma % 11;
    let digito2 = resto < 2 ? 0 : 11 - resto;

    return parseInt(cnpj[13]) === digito2;
  }
});

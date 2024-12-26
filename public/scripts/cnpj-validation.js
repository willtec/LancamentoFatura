// Máscara para CNPJ
document.getElementById("cnpj").addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, "");
  e.target.value = value.replace(
    /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,
    "$1.$2.$3/$4-$5"
  );
});

// Validação de CNPJ
function validarCNPJ() {
  const cnpj = document.getElementById("cnpj").value.replace(/\D/g, "");
  if (!cnpjValido(cnpj)) {
    alert("CNPJ inválido. Por favor, verifique os dados.");
    return false;
  }
  return true;
}

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

<!-- cadastrar.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fatura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/public/styles/cadastrar_fatura.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-2xl font-bold text-center py-4">Cadastro de Fatura</h1>

            <!-- Exibir mensagens de erro ou sucesso -->
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="mb-4 p-4 text-white <?=$_SESSION['mensagem']['tipo'] === 'sucesso' ? 'bg-green-500' : 'bg-red-500';?>">
                    <?=$_SESSION['mensagem']['texto'];?>
                </div>
                <?php unset($_SESSION['mensagem']);?>
            <?php endif;?>

            <form action="/LancamentoFatura/faturas/cadastrar" method="POST" enctype="multipart/form-data" class="space-y-4">
                <!-- Input oculto para ID da fatura (usado em edição) -->
                <?php if (isset($fatura['id'])): ?>
                    <input type="hidden" name="id" value="<?=htmlspecialchars($fatura['id']);?>">
                <?php endif;?>

                <!-- Campo com autocomplete para Transportadora -->
                <div class="form-group relative">
                    <label for="transportadora" class="label">Transportadora</label>
                    <input
                        type="text"
                        name="transportadora"
                        id="transportadora"
                        class="input-field w-full"
                        placeholder="Digite o código ou nome"
                        autocomplete="off"
                        value="<?=htmlspecialchars($fatura['transportadora'] ?? '');?>"
                        required
                    >
                    <ul id="transportadora-suggestions" class="hidden bg-white border rounded-md shadow-lg absolute mt-1 max-h-40 overflow-y-auto w-full"></ul>
                </div>

                <!-- Outros campos do formulário -->
                <div class="form-group">
                    <label for="numero_fatura" class="label">Número da Fatura</label>
                    <input
                        type="text"
                        name="numero_fatura"
                        id="numero_fatura"
                        class="input-field"
                        value="<?=htmlspecialchars($fatura['numero_fatura'] ?? '');?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="valor" class="label">Valor</label>
                    <input
                        type="number"
                        step="0.01"
                        name="valor"
                        id="valor"
                        class="input-field"
                        value="<?=htmlspecialchars($fatura['valor'] ?? '');?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="vencimento" class="label">Data de Vencimento</label>
                    <input
                        type="date"
                        name="vencimento"
                        id="vencimento"
                        class="input-field"
                        value="<?=htmlspecialchars($fatura['vencimento'] ?? '');?>"
                        required
                    >
                </div>
                <div class="form-group">
                    <label for="boleto" class="label">Anexar Boleto (PDF)</label>
                    <input
                        type="file"
                        name="boleto"
                        id="boleto"
                        accept="application/pdf"
                        class="input-field"
                    >
                    <?php if (isset($fatura['boleto']) && $fatura['boleto']): ?>
                        <p class="text-sm mt-1">Arquivo atual: <a href="/uploads/boletos/<?=htmlspecialchars($fatura['boleto']);?>" target="_blank" class="text-blue-500 underline">Visualizar</a></p>
                    <?php endif;?>
                </div>
                <div class="form-group">
                    <label for="arquivos_cte" class="label">Anexar Arquivo(s) de CTe (ZIP)</label>
                    <input
                        type="file"
                        name="arquivos_cte"
                        id="arquivos_cte"
                        accept=".zip"
                        class="input-field"
                    >
                    <?php if (isset($fatura['arquivos_cte']) && $fatura['arquivos_cte']): ?>
                        <p class="text-sm mt-1">Arquivo atual: <a href="/uploads/ctes/<?=htmlspecialchars($fatura['arquivos_cte']);?>" target="_blank" class="text-blue-500 underline">Visualizar</a></p>
                    <?php endif;?>
                </div>
                <div class="text-center">
                    <button type="submit" class="submit-button">
                        <?=isset($fatura['id']) ? 'Atualizar Fatura' : 'Cadastrar Fatura';?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const transportadoraInput = document.getElementById('transportadora');
        const suggestionsList = document.getElementById('transportadora-suggestions');

        transportadoraInput.addEventListener('input', function() {
            const query = transportadoraInput.value.trim();

            if (query.length >= 2) { // Mostrar sugestões apenas se a query tiver 2 caracteres ou mais
                fetch(`/LancamentoFatura/routes/api.php?transportadoras&q=${encodeURIComponent(query)}`)
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
    </script>

</body>
</html>

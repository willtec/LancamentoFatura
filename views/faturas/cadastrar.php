<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fatura</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/../public/styles/cadastrar_fatura.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold text-center py-4">Cadastro de Fatura</h1>

            <form action="/LancamentoFatura/faturas/cadastrar" method="POST" class="space-y-4">
                <!-- Campos do formulário -->
                <div>
                    <label for="transportadora" class="block text-sm font-medium">Transportadora</label>
                    <input type="text" name="transportadora" id="transportadora" class="w-full px-4 py-2 border rounded-md" required>
                </div>
                <div>
                    <label for="valor" class="block text-sm font-medium">Valor</label>
                    <input type="number" name="valor" id="valor" class="w-full px-4 py-2 border rounded-md" required>
                </div>
                <div>
                    <label for="vencimento" class="block text-sm font-medium">Data de Vencimento</label>
                    <input type="date" name="vencimento" id="vencimento" class="w-full px-4 py-2 border rounded-md" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Cadastrar Fatura
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
</html>

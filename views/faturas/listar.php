<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faturas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/../public/scripts/tailwind.config.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="/../public/styles/listar_fatura.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg">
            <!-- Header -->
            <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center rounded-t-lg">
                <div>
                    <h1 class="text-2xl font-bold">Gerenciamento de Faturas</h1>
                    <p class="text-sm text-blue-200">Visualize e gerencie suas faturas</p>
                </div>
                <!-- Alteração do link de cadastro -->
                <a href="/LancamentoFatura/faturas/cadastrar" class="btn-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Nova Fatura
                </a>
            </div>

            <?php
// Verificar se o parâmetro "cadastrar" está na URL
// Não será mais necessário, pois a navegação já está controlada pela rota
?>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex space-x-4">
                    <div class="flex-1">
                        <input type="text" placeholder="Buscar por transportadora" class="w-full px-3 py-2 border rounded-md">
                    </div>
                    <div class="flex-1">
                        <select class="w-full px-3 py-2 border rounded-md">
                            <option>Filtrar por mês</option>
                            <option>Janeiro</option>
                            <option>Fevereiro</option>
                        </select>
                    </div>
                    <button class="btn-primary">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="responsive-table overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="table-header-cell">Código</th>
                            <th class="table-header-cell">Transportadora</th>
                            <th class="table-header-cell">Fatura</th>
                            <th class="table-header-cell">Vencimento</th>
                            <th class="table-header-cell">Valor</th>
                            <th class="table-header-cell">Total Notas</th>
                            <th class="table-header-cell">%</th>
                            <th class="table-header-cell">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($faturas)): ?>
                            <?php foreach ($faturas as $fatura): ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="table-body-cell"><?=$fatura['id'];?></td>
                                    <td class="table-body-cell"><?=$fatura['transportadora'];?></td>
                                    <td class="table-body-cell"><?=$fatura['numero_fatura'];?></td>
                                    <td class="table-body-cell text-red-600"><?=$fatura['vencimento'];?></td>
                                    <td class="table-body-cell text-green-600 font-semibold">
                                        R$ <?=number_format($fatura['valor'], 2, ',', '.');?>
                                    </td>
                                    <td class="table-body-cell text-blue-600 font-semibold">
                                        R$ <?=number_format($fatura['valor_total_notas'], 2, ',', '.');?>
                                    </td>
                                    <td class="table-body-cell">
                                        <?php
$percentual = ($fatura['valor_total_notas'] > 0) ? ($fatura['valor'] / $fatura['valor_total_notas']) * 100 : 0;
echo number_format($percentual, 2);
?>%
                                    </td>
                                    <td class="table-body-cell">
                                        <div class="flex space-x-3">
                                            <a href="/faturas/editar?id=<?=$fatura['id'];?>" class="action-icon text-indigo-600 hover:text-indigo-900" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/faturas/relatorio?id=<?=$fatura['id'];?>" class="action-icon text-green-600 hover:text-green-900" title="Relatório">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            <a href="<?=$fatura['boleto'];?>" target="_blank" class="action-icon text-blue-600 hover:text-blue-900" title="Boleto">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="<?=$fatura['arquivos_cte'];?>" target="_blank" class="action-icon text-gray-600 hover:text-gray-900" title="XML">
                                                <i class="fas fa-file-code"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                        <p>Nenhuma fatura encontrada</p>
                                        <a href="/faturas/cadastrar" class="mt-2 text-blue-600 hover:underline">
                                            Cadastrar nova fatura
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Mostrando 1-10 de 50 faturas
                </div>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border rounded-md hover:bg-gray-100">
                        Anterior
                    </button>
                    <button class="px-3 py-1 border rounded-md hover:bg-gray-100">
                        Próximo
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Faturas</title>
</head>
<body>
    <h1>Faturas</h1>
    <a href="/faturas/cadastrar">Lançar Nova Fatura</a>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Transportadora</th>
                <th>Fatura</th>
                <th>Vencimento</th>
                <th>Data de Lançamento</th>
                <th>Valor</th>
                <th>Total Notas</th>
                <th>%</th>
                <th>Boleto</th>
                <th>Arquivos XML</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($faturas)): ?>
                <?php foreach ($faturas as $fatura): ?>
                    <tr>
                        <td><?= $fatura['id']; ?></td>
                        <td><?= $fatura['transportadora']; ?></td>
                        <td><?= $fatura['numero_fatura']; ?></td>
                        <td><?= $fatura['vencimento']; ?></td>
                        <td><?= $fatura['data_lancamento']; ?></td>
                        <td>R$ <?= number_format($fatura['valor'], 2, ',', '.'); ?></td>
                        <td>R$ <?= number_format($fatura['valor_total_notas'], 2, ',', '.'); ?></td>
                        <td>
                            <?php 
                                $percentual = ($fatura['valor_total_notas'] > 0) ? ($fatura['valor'] / $fatura['valor_total_notas']) * 100 : 0;
                                echo number_format($percentual, 2); 
                            ?>%
                        </td>
                        <td><a href="<?= $fatura['boleto']; ?>" target="_blank">Download</a></td>
                        <td><a href="<?= $fatura['arquivos_cte']; ?>" target="_blank">Download</a></td>
                        <td>
                            <a href="/faturas/editar?id=<?= $fatura['id']; ?>">Editar</a>
                            <a href="/faturas/relatorio?id=<?= $fatura['id']; ?>">Relatório</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="text-align: center;">Nenhuma fatura encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

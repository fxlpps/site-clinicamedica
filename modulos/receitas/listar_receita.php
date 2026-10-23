<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT r.id, r.data_receita, 
               p.nome AS nome_paciente, 
               m.nome AS nome_medico,
               COUNT(rm.id) AS total_itens
        FROM receita r
        JOIN consulta c ON r.id_consulta = c.id
        JOIN paciente p ON c.id_paciente = p.id
        JOIN medico m ON c.id_medico = m.id
        LEFT JOIN receita_medicamento rm ON rm.id_receita = r.id
        GROUP BY r.id, r.data_receita, p.nome, m.nome
        ORDER BY r.data_receita DESC";
$receitas = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Receitas</h1>
                <a href="cadastrar_receita.php" class="btn-novo">+ Nova Receita</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'excluido': echo 'Receita excluída com sucesso!'; break;
                        default:         echo 'Receita emitida com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">Erro ao processar a solicitação.</div>
            <?php endif; ?>

            <?php if (count($receitas) === 0): ?>
                <div class="vazio">
                    <p>Nenhuma receita emitida ainda.</p>
                    <a href="cadastrar_receita.php" class="btn-novo">Emitir a primeira</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Qtd. Medicamentos</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receitas as $r): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($r['data_receita'])); ?></td>
                                <td><?php echo htmlspecialchars($r['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($r['nome_medico']); ?></td>
                                <td><?php echo $r['total_itens']; ?> item(ns)</td>
                                <td class="acoes">
                                    <a href="visualizar_receita.php?id=<?php echo $r['id']; ?>" class="btn-acao" title="Visualizar">
                                        <img src="../../img/visualizar_icon.png" class="iconeacao" alt="Visualizar">
                                    </a>
                                    <a href="excluir_receita.php?id=<?php echo $r['id']; ?>"
                                    class="btn-acao btn-excluir"
                                    title="Excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir esta receita?');">
                                        <img src="../../img/excluir_icon.png" class="iconeacao" alt="Excluir">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
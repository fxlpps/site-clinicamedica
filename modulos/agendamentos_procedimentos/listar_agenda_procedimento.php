<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$hoje = date('Y-m-d');

$sql_agendados = "SELECT a.*, p.nome AS nome_paciente, pr.nome AS nome_procedimento, m.nome AS nome_medico
                  FROM agendamento_procedimento a
                  JOIN paciente p ON a.id_paciente = p.id
                  JOIN procedimento pr ON a.id_procedimento = pr.id
                  LEFT JOIN medico m ON a.id_medico = m.id
                  WHERE a.status = 'AGENDADO' AND a.data_procedimento >= ?
                  ORDER BY a.data_procedimento ASC, a.horario ASC";
$stmt_agendados = $pdo->prepare($sql_agendados);
$stmt_agendados->execute([$hoje]);
$agendados = $stmt_agendados->fetchAll();

$sql_historico = "SELECT a.*, p.nome AS nome_paciente, pr.nome AS nome_procedimento, m.nome AS nome_medico
                  FROM agendamento_procedimento a
                  JOIN paciente p ON a.id_paciente = p.id
                  JOIN procedimento pr ON a.id_procedimento = pr.id
                  LEFT JOIN medico m ON a.id_medico = m.id
                  WHERE a.status != 'AGENDADO' OR a.data_procedimento < ?
                  ORDER BY a.data_procedimento DESC, a.horario DESC
                  LIMIT 50";
$stmt_historico = $pdo->prepare($sql_historico);
$stmt_historico->execute([$hoje]);
$historico = $stmt_historico->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos de Procedimentos</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Agendamentos de Procedimentos</h1>
                <a href="agendar_procedimento.php" class="btn-novo">+ Novo Agendamento</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Agendamento atualizado com sucesso!'; break;
                        case 'excluido': echo 'Agendamento excluído com sucesso!';   break;
                        default:         echo 'Procedimento agendado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso': echo 'Não é possível excluir: erro de vínculo.'; break;
                        default:       echo 'Erro. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <h3 class="form-secao">Próximos Agendamentos</h3>
            <?php if (count($agendados) === 0): ?>
                <p style="color: #666; margin-bottom: 20px;">Nenhum procedimento agendado para os próximos dias.</p>
            <?php else: ?>
                <table class="tabela" style="margin-bottom: 30px;">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Procedimento</th>
                            <th>Profissional</th>
                            <th>Valor</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendados as $a): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($a['data_procedimento'])); ?></td>
                                <td><?php echo substr($a['horario'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_procedimento']); ?></td>
                                <td><?php echo $a['nome_medico'] ? htmlspecialchars($a['nome_medico']) : '—'; ?></td>
                                <td>R$ <?php echo number_format($a['valor'], 2, ',', '.'); ?></td>
                                <td class="acoes">
                                    <a href="remarcar_procedimento.php?id=<?php echo $a['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_procedimento.php?id=<?php echo $a['id']; ?>" class="btn-acao btn-excluir" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?');">
                                        <img src="../../img/excluir_icon.png" class="iconeacao" alt="Excluir">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h3 class="form-secao">Histórico</h3>
            <?php if (count($historico) === 0): ?>
                <p style="color: #666;">Nenhum procedimento no histórico</p>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Paciente</th>
                            <th>Procedimento</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $a): ?>
                            <tr class="<?php echo $a['status'] === 'CANCELADO' ? 'linha-inativa' : ''; ?>">
                                <td><?php echo date('d/m/Y', strtotime($a['data_procedimento'])); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_procedimento']); ?></td>
                                <td>
                                    <?php if ($a['status'] === 'REALIZADO'): ?>
                                        <span class="tag tag-ativo">Realizado</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Cancelado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="remarcar_procedimento.php?id=<?php echo $a['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_procedimento.php?id=<?php echo $a['id']; ?>" class="btn-acao btn-excluir" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?');">
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
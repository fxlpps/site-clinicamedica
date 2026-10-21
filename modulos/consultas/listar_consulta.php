<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$hoje = date('Y-m-d');

$sql_hoje = "SELECT c.*, p.nome AS nome_paciente, m.nome AS nome_medico 
             FROM consulta c
             JOIN paciente p ON c.id_paciente = p.id
             JOIN medico m ON c.id_medico = m.id
             WHERE c.data_consulta = ? 
             ORDER BY c.horario ASC";

$sql_futuras = "SELECT c.*, p.nome AS nome_paciente, m.nome AS nome_medico 
                FROM consulta c
                JOIN paciente p ON c.id_paciente = p.id
                JOIN medico m ON c.id_medico = m.id
                WHERE c.data_consulta > ? 
                ORDER BY c.data_consulta ASC, c.horario ASC";

$sql_passadas = "SELECT c.*, p.nome AS nome_paciente, m.nome AS nome_medico 
                 FROM consulta c
                 JOIN paciente p ON c.id_paciente = p.id
                 JOIN medico m ON c.id_medico = m.id
                 WHERE c.data_consulta < ? 
                 ORDER BY c.data_consulta DESC, c.horario DESC";

$stmt_hoje = $pdo->prepare($sql_hoje);
$stmt_hoje->execute([$hoje]);
$consultas_hoje = $stmt_hoje->fetchAll();

$stmt_futuras = $pdo->prepare($sql_futuras);
$stmt_futuras->execute([$hoje]);
$consultas_futuras = $stmt_futuras->fetchAll();

$stmt_passadas = $pdo->prepare($sql_passadas);
$stmt_passadas->execute([$hoje]);
$consultas_passadas = $stmt_passadas->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Consultas</h1>
                <a href="cadastrar_consulta.php" class="btn-novo">+ Nova Consulta</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Consulta atualizada com sucesso!'; break;
                        case 'excluido': echo 'Consulta excluída com sucesso!';   break;
                        default:         echo 'Consulta agendada com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'nao_encontrado': echo 'Consulta não encontrada.'; break;
                        default:               echo 'Erro. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Hoje -->
            <h3 class="form-secao">Hoje (<?php echo date('d/m/Y'); ?>)</h3>
            <?php if (count($consultas_hoje) === 0): ?>
                <p style="color: #666; margin-bottom: 20px;">Nenhuma consulta agendada para hoje.</p>
            <?php else: ?>
                <table class="tabela" style="margin-bottom: 30px;">
                    <thead>
                        <tr>
                            <th>Horário</th>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultas_hoje as $c): ?>
                            <tr>
                                <td><strong><?php echo substr($c['horario'], 0, 5); ?></strong></td>
                                <td><?php echo htmlspecialchars($c['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($c['nome_medico']); ?></td>
                                <td>
                                    <?php if ($c['prioridade'] === 'Urgente'): ?>
                                        <span class="tag" style="background: #fde2e2; color: #a01a1a;">Urgente</span>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($c['prioridade']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($c['status']); ?></td>
                                <td class="acoes">
                                    <a href="remarcar_consulta.php?id=<?php echo $c['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_consulta.php?id=<?php echo $c['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">
                                        <img src="../../img/excluir_icon.png" class="iconeacao" alt="Excluir">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Próximas -->
            <h3 class="form-secao">Próximas Consultas</h3>
            <?php if (count($consultas_futuras) === 0): ?>
                <p style="color: #666; margin-bottom: 20px;">Nenhuma consulta futura agendada.</p>
            <?php else: ?>
                <table class="tabela" style="margin-bottom: 30px;">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultas_futuras as $c): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($c['data_consulta'])); ?></td>
                                <td><?php echo substr($c['horario'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($c['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($c['nome_medico']); ?></td>
                                <td><?php echo htmlspecialchars($c['status']); ?></td>
                                <td class="acoes">
                                    <a href="editar_consulta.php?id=<?php echo $c['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_consulta.php?id=<?php echo $c['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">
                                        <img src="../../img/excluir_icon.png" class="iconeacao" alt="Excluir">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Passadas -->
            <h3 class="form-secao">Consultas Passadas</h3>
            <?php if (count($consultas_passadas) === 0): ?>
                <p style="color: #666;">Nenhuma consulta no histórico.</p>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultas_passadas as $c): ?>
                            <tr class="<?php echo $c['status'] === 'Cancelada' ? 'linha-inativa' : ''; ?>">
                                <td><?php echo date('d/m/Y', strtotime($c['data_consulta'])); ?></td>
                                <td><?php echo substr($c['horario'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($c['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($c['nome_medico']); ?></td>
                                <td><?php echo htmlspecialchars($c['status']); ?></td>
                                <td class="acoes">
                                    <a href="editar_consulta.php?id=<?php echo $c['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_consulta.php?id=<?php echo $c['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">
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
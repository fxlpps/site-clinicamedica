<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT e.*, 
               t.nome AS nome_tipo, 
               p.nome AS nome_paciente, 
               m.nome AS nome_medico,
               e.data_realizacao, e.hora_realizacao
        FROM exame e
        JOIN tipo_exame t ON e.id_tipo = t.id
        JOIN consulta c ON e.id_consulta = c.id
        JOIN paciente p ON c.id_paciente = p.id
        JOIN medico m ON c.id_medico = m.id
        ORDER BY e.data_realizacao DESC, e.hora_realizacao DESC";
$exames = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exames</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Exames</h1>
                <a href="agendar_exame.php" class="btn-novo">+ Agendar Exame</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Exame atualizado com sucesso!'; break;
                        case 'excluido': echo 'Exame excluído com sucesso!';   break;
                        default:         echo 'Exame agendado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso': echo 'Não é possível excluir: este exame possui registros vinculados.'; break;
                        default:       echo 'Erro. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($exames) === 0): ?>
                <div class="vazio">
                    <p>Nenhum exame agendado ou realizado ainda.</p>
                    <a href="agendar_exame.php" class="btn-novo">Agendar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Exame</th>
                            <th>Médico Solicitante</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exames as $e): ?>
                            <tr class="<?php echo $e['status'] === 'Cancelada' ? 'linha-inativa' : ''; ?>">
                                <td><?php echo date('d/m/Y', strtotime($e['data_realizacao'])); ?></td>
                                <td><?php echo substr($e['hora_realizacao'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($e['nome_paciente']); ?></td>
                                <td><?php echo htmlspecialchars($e['nome_tipo']); ?></td>
                                <td><?php echo htmlspecialchars($e['nome_medico']); ?></td>
                                <td>
                                    <?php if ($e['status'] === 'Agendada'): ?>
                                        <span class="tag" style="background: #e0e7ff; color: #3730a3;">Agendada</span>
                                    <?php elseif ($e['status'] === 'Realizada'): ?>
                                        <span class="tag tag-ativo">Realizada</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Cancelada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="remarcar_exame.php?id=<?php echo $e['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_exame.php?id=<?php echo $e['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este exame?');">
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
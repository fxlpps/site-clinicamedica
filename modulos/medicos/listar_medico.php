<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT * FROM medico ORDER BY status ASC, nome ASC";
$medicos = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Médicos</h1>
                <a href="cadastrar_medico.php" class="btn-novo">+ Novo Médico</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Médico atualizado com sucesso!'; break;
                        case 'excluido': echo 'Médico excluído com sucesso!';   break;
                        default:         echo 'Médico cadastrado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso':
                            echo 'Não é possível excluir: existem consultas ou agendamentos vinculados a este médico.';
                            break;
                        case 'nao_encontrado':
                            echo 'Médico não encontrado.';
                            break;
                        default:
                            echo 'Erro ao excluir. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($medicos) === 0): ?>
                <div class="vazio">
                    <p>Nenhum médico cadastrado ainda.</p>
                    <a href="cadastrar_medico.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CRM</th>
                            <th>Especialidade</th>
                            <th>Telefone</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicos as $m): ?>
                            <tr class="<?php echo $m['status'] === 'ATIVO' ? '' : 'linha-inativa'; ?>">
                                <td><?php echo htmlspecialchars($m['nome']); ?></td>
                                <td><?php echo htmlspecialchars($m['crm']); ?></td>
                                <td>
                                    <?php if ($m['especialidade']): ?>
                                        <?php echo htmlspecialchars($m['especialidade']); ?>
                                    <?php else: ?>
                                        <span style="color: #999;">Generalista</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($m['telefone']); ?></td>
                                <td>
                                    <?php if ($m['status'] === 'ATIVO'): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_medico.php?id=<?php echo $m['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_medico.php?id=<?php echo $m['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este médico?');">
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
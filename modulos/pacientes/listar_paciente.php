<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT p.*, c.nome AS convenio_nome
        FROM paciente p
        LEFT JOIN convenio c ON p.id_convenio = c.id
        ORDER BY p.status ASC, p.nome ASC";
$pacientes = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Pacientes</h1>
                <a href="cadastrar_paciente.php" class="btn-novo">+ Novo Paciente</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Paciente atualizado com sucesso!'; break;
                        case 'excluido': echo 'Paciente excluído com sucesso!';   break;
                        default:         echo 'Paciente cadastrado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso':
                            echo 'Não é possível excluir: existem consultas ou exames vinculados a este paciente.';
                            break;
                        case 'nao_encontrado':
                            echo 'Paciente não encontrado.';
                            break;
                        default:
                            echo 'Erro ao excluir. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($pacientes) === 0): ?>
                <div class="vazio">
                    <p>Nenhum paciente cadastrado ainda.</p>
                    <a href="cadastrar_paciente.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Convênio</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pacientes as $p): ?>
                            <tr class="<?php echo $p['status'] === 'ATIVO' ? '' : 'linha-inativa'; ?>">
                                <td><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td><?php echo htmlspecialchars($p['cpf']); ?></td>
                                <td><?php echo htmlspecialchars($p['telefone']); ?></td>
                                <td>
                                    <?php if ($p['convenio_nome']): ?>
                                        <?php echo htmlspecialchars($p['convenio_nome']); ?>
                                    <?php else: ?>
                                        <span style="color: #999;">Particular</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'ATIVO'): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_paciente.php?id=<?php echo $p['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_paciente.php?id=<?php echo $p['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este paciente?');">
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
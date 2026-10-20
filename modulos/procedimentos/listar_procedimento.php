<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT * FROM procedimento ORDER BY ativo DESC, nome ASC";
$procedimentos = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procedimentos</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Procedimentos</h1>
                <a href="cadastrar_procedimento.php" class="btn-novo">+ Novo Procedimento</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Procedimento atualizado com sucesso!'; break;
                        case 'excluido': echo 'Procedimento excluído com sucesso!';   break;
                        default:         echo 'Procedimento cadastrado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso':
                            echo 'Não é possível excluir: existem agendamentos vinculados a este procedimento.';
                            break;
                        case 'nao_encontrado':
                            echo 'Procedimento não encontrado.';
                            break;
                        default:
                            echo 'Erro ao excluir. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($procedimentos) === 0): ?>
                <div class="vazio">
                    <p>Nenhum procedimento cadastrado ainda.</p>
                    <a href="cadastrar_procedimento.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Duração</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($procedimentos as $p): ?>
                            <tr class="<?php echo $p['ativo'] ? '' : 'linha-inativa'; ?>">
                                <td><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td>
                                    <?php echo $p['duracao'] ? substr($p['duracao'], 0, 5) : '—'; ?>
                                </td>
                                <td>R$ <?php echo number_format($p['valor'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php if ($p['ativo']): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_procedimento.php?id=<?php echo $p['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_procedimento.php?id=<?php echo $p['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este procedimento?');">
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
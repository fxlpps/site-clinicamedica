<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

//Todos os convenios
$sql = "SELECT * FROM convenio ORDER BY ativo DESC, nome ASC";
$convenios = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convênios</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Convênios</h1>
                <a href="cadastrar_convenio.php" class="btn-novo">+ Novo Convênio</a>
            </div>
            <?php if (isset($_GET['sucesso'])): ?>
    <div class="alerta alerta-sucesso">
        <?php
        switch ($_GET['sucesso']) {
            case 'editado':  echo 'Convênio atualizado com sucesso!'; break;
            case 'excluido': echo 'Convênio excluído com sucesso!';   break;
            default:         echo 'Convênio cadastrado com sucesso!';
        }
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['erro'])): ?>
    <div class="alerta alerta-erro">
        <?php
        switch ($_GET['erro']) {
            case 'em_uso':
                echo 'Não é possível excluir: existem pacientes vinculados a este convênio.';
                break;
            case 'nao_encontrado':
                echo 'Convênio não encontrado.';
                break;
            default:
                echo 'Erro ao excluir. Tente novamente.';
        }
        ?>
    </div>
<?php endif; ?>

            <?php if (count($convenios) === 0): ?>
                <div class="vazio">
                    <p>Nenhum convênio cadastrado ainda.</p>
                    <a href="cadastrar_convenio.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($convenios as $c): ?>
                            <tr class="<?php echo $c['ativo'] ? '' : 'linha-inativa'; ?>">
                                <td><?php echo htmlspecialchars($c['nome']); ?></td>
                                <td><?php echo htmlspecialchars($c['cnpj'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($c['telefone'] ?? '—'); ?></td>
                                <td>
                                    <?php if ($c['ativo']): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_convenio.php?id=<?php echo $c['id']; ?>" class="btn-acao" title="Editar"><img src="../../img/editar_icon.png" class="iconeacao"></a>
                                    <a href="excluir_convenio.php?id=<?php echo $c['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este convênio?');"><img src="../../img/excluir_icon.png" class="iconeacao"></a>
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
<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT * FROM tipo_exame ORDER BY ativo DESC, nome ASC";
$tipos = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Exame</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Tipos de Exame</h1>
                <a href="cadastrar_tipoexame.php" class="btn-novo">+ Novo Tipo</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Tipo de exame atualizado com sucesso!'; break;
                        case 'excluido': echo 'Tipo de exame excluído com sucesso!';   break;
                        default:         echo 'Tipo de exame cadastrado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso':
                            echo 'Não é possível excluir: existem exames vinculados a este tipo.';
                            break;
                        case 'nao_encontrado':
                            echo 'Tipo de exame não encontrado.';
                            break;
                        default:
                            echo 'Erro ao excluir. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($tipos) === 0): ?>
                <div class="vazio">
                    <p>Nenhum tipo de exame cadastrado ainda.</p>
                    <a href="cadastrar_tipoexame.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipos as $t): ?>
                            <tr class="<?php echo $t['ativo'] ? '' : 'linha-inativa'; ?>">
                                <td><?php echo htmlspecialchars($t['nome']); ?></td>
                                <td>
                                    <?php if ($t['ativo']): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_tipoexame.php?id=<?php echo $t['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_tipoexame.php?id=<?php echo $t['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este tipo de exame?');">
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
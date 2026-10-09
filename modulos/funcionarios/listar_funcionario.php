<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$sql = "SELECT * FROM funcionario ORDER BY status ASC, nome ASC";
$funcionarios = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Funcionários</h1>
                <a href="cadastrar_funcionario.php" class="btn-novo">+ Novo Funcionário</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Funcionário atualizado com sucesso!'; break;
                        case 'excluido': echo 'Funcionário excluído com sucesso!';   break;
                        default:         echo 'Funcionário cadastrado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso':
                            echo 'Não é possível excluir: este funcionário possui usuário vinculado.';
                            break;
                        case 'nao_encontrado':
                            echo 'Funcionário não encontrado.';
                            break;
                        default:
                            echo 'Erro ao excluir. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($funcionarios) === 0): ?>
                <div class="vazio">
                    <p>Nenhum funcionário cadastrado ainda.</p>
                    <a href="cadastrar_funcionario.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Telefone</th>
                            <th>Salário</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $f): ?>
                            <tr class="<?php echo $f['status'] === 'ATIVO' ? '' : 'linha-inativa'; ?>">
                                <td><?php echo htmlspecialchars($f['nome']); ?></td>
                                <td><?php echo htmlspecialchars($f['cargo']); ?></td>
                                <td><?php echo htmlspecialchars($f['telefone']); ?></td>
                                <td>R$ <?php echo number_format($f['salario'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php if ($f['status'] === 'ATIVO'): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_funcionario.php?id=<?php echo $f['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <a href="excluir_funcionario.php?id=<?php echo $f['id']; ?>"
                                       class="btn-acao btn-excluir"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este funcionário?');">
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
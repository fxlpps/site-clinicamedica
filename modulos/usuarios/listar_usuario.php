<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

// pega o nome do usuario vinculado
$sql = "SELECT u.*, 
               f.nome AS nome_funcionario, 
               m.nome AS nome_medico, 
               p.nome AS nome_paciente
        FROM usuario u
        LEFT JOIN funcionario f ON u.id_funcionario = f.id
        LEFT JOIN medico m ON u.id_medico = m.id
        LEFT JOIN paciente p ON u.id_paciente = p.id
        ORDER BY u.ativo DESC, u.login ASC";
$usuarios = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Usuários</h1>
                <a href="cadastrar_usuario.php" class="btn-novo">+ Novo Usuário</a>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alerta alerta-sucesso">
                    <?php
                    switch ($_GET['sucesso']) {
                        case 'editado':  echo 'Usuário atualizado com sucesso!'; break;
                        case 'excluido': echo 'Usuário excluído com sucesso!';   break;
                        default:         echo 'Usuário cadastrado com sucesso!';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alerta alerta-erro">
                    <?php
                    switch ($_GET['erro']) {
                        case 'em_uso':
                            echo 'Não é possível excluir: existem registros vinculados a este usuário.';
                            break;
                        case 'nao_encontrado':
                            echo 'Usuário não encontrado.';
                        case 'proprio_usuario':
                            echo 'Você não pode excluir o próprio usuário logado.';
                            break;
                        default:
                            echo 'Erro ao excluir. Tente novamente.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (count($usuarios) === 0): ?>
                <div class="vazio">
                    <p>Nenhum usuário cadastrado ainda.</p>
                    <a href="cadastrar_usuario.php" class="btn-novo">Cadastrar o primeiro</a>
                </div>
            <?php else: ?>
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>Login</th>
                            <th>Perfil</th>
                            <th>Vínculo</th>
                            <th>Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr class="<?php echo $u['ativo'] ? '' : 'linha-inativa'; ?>">
                                <td><strong><?php echo htmlspecialchars($u['login']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['perfil']); ?></td>
                                <td>
                                    <?php 
                                    // ve qual nome usa no vinculo
                                    $nome_vinculo = '';
                                    if ($u['id_funcionario']) $nome_vinculo = $u['nome_funcionario'] . ' (Funcionário)';
                                    elseif ($u['id_medico']) $nome_vinculo = $u['nome_medico'] . ' (Médico)';
                                    elseif ($u['id_paciente']) $nome_vinculo = $u['nome_paciente'] . ' (Paciente)';
                                    
                                    if ($nome_vinculo) {
                                        echo htmlspecialchars($nome_vinculo);
                                    } else {
                                        echo '<span style="color: #999;">Nenhum</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($u['ativo']): ?>
                                        <span class="tag tag-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="tag tag-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <a href="editar_usuario.php?id=<?php echo $u['id']; ?>" class="btn-acao" title="Editar">
                                        <img src="../../img/editar_icon.png" class="iconeacao" alt="Editar">
                                    </a>
                                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                        <a href="excluir_usuario.php?id=<?php echo $u['id']; ?>"
                                        class="btn-acao btn-excluir"
                                        title="Excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            <img src="../../img/excluir_icon.png" class="iconeacao" alt="Excluir">
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: #999; margin-left: 5px;" title="Você não pode excluir a si mesmo">(Você)</span>
                                    <?php endif; ?>
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
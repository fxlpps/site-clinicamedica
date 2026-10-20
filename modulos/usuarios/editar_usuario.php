<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$perfis = ['Administrador', 'Recepcionista', 'Financeiro', 'Medico', 'Paciente'];

$funcionarios = $pdo->query("SELECT id, nome FROM funcionario WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();
$medicos      = $pdo->query("SELECT id, nome FROM medico WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();
$pacientes    = $pdo->query("SELECT id, nome FROM paciente WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_usuario.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';
    $perfil   = trim($_POST['perfil'] ?? '');
    $ativo    = isset($_POST['ativo']) ? 1 : 0;

    $id_funcionario = !empty($_POST['id_funcionario']) ? (int) $_POST['id_funcionario'] : null;
    $id_medico      = !empty($_POST['id_medico']) ? (int) $_POST['id_medico'] : null;
    $id_paciente    = !empty($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : null;

    if ($login === '' || $perfil === '') {
        $erro = 'Preencha os campos obrigatórios.';
    } elseif (!in_array($perfil, $perfis, true)) {
        $erro = 'Selecione um perfil válido.';
    } elseif ($senha !== '' && strlen($senha) < 6) {
        $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== '' && $senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        try {
            if ($senha !== '') {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuario SET id_funcionario = ?, id_medico = ?, id_paciente = ?, login = ?, senha_hash = ?, perfil = ?, ativo = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_funcionario, $id_medico, $id_paciente, $login, $hash, $perfil, $ativo, $id]);
            } else {
                $sql = "UPDATE usuario SET id_funcionario = ?, id_medico = ?, id_paciente = ?, login = ?, perfil = ?, ativo = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_funcionario, $id_medico, $id_paciente, $login, $perfil, $ativo, $id]);
            }

            header('Location: listar_usuario.php?sucesso=editado');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Este login já está em uso.';
            } else {
                $erro = 'Erro ao salvar: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: listar_usuario.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Editar Usuário</h1>
                <a href="listar_usuario.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <h3 class="form-secao">Dados de Acesso</h3>

                <div class="campo">
                    <label for="login">Login *</label>
                    <input type="text" id="login" name="login" required
                           value="<?php echo htmlspecialchars($usuario['login']); ?>">
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="senha">Nova senha</label>
                        <input type="password" id="senha" name="senha" minlength="6" placeholder="Deixe em branco para não alterar">
                    </div>
                    <div class="campo">
                        <label for="confirma_senha">Confirmar nova senha</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" minlength="6">
                    </div>
                </div>

                <div class="campo">
                    <label for="perfil">Perfil *</label>
                    <select id="perfil" name="perfil" required>
                        <option value="">Selecione</option>
                        <?php foreach ($perfis as $p): ?>
                            <option value="<?php echo $p; ?>"
                                <?php echo ($usuario['perfil'] === $p) ? 'selected' : ''; ?>>
                                <?php echo $p; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3 class="form-secao">Vínculo (opcional)</h3>

                <div class="campo" id="campo-funcionario" style="display: none;">
                    <label for="id_funcionario">Funcionário vinculado</label>
                    <select id="id_funcionario" name="id_funcionario">
                        <option value="">Nenhum</option>
                        <?php foreach ($funcionarios as $f): ?>
                            <option value="<?php echo $f['id']; ?>"
                                <?php echo ((int)$usuario['id_funcionario'] === (int)$f['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($f['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo" id="campo-medico" style="display: none;">
                    <label for="id_medico">Médico vinculado</label>
                    <select id="id_medico" name="id_medico">
                        <option value="">Nenhum</option>
                        <?php foreach ($medicos as $m): ?>
                            <option value="<?php echo $m['id']; ?>"
                                <?php echo ((int)$usuario['id_medico'] === (int)$m['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo" id="campo-paciente" style="display: none;">
                    <label for="id_paciente">Paciente vinculado</label>
                    <select id="id_paciente" name="id_paciente">
                        <option value="">Nenhum</option>
                        <?php foreach ($pacientes as $p): ?>
                            <option value="<?php echo $p['id']; ?>"
                                <?php echo ((int)$usuario['id_paciente'] === (int)$p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="ativo" value="1" <?php echo $usuario['ativo'] ? 'checked' : ''; ?>>
                        Usuário ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_usuario.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        const perfilSelect = document.getElementById('perfil');
        const campoFuncionario = document.getElementById('campo-funcionario');
        const campoMedico = document.getElementById('campo-medico');
        const campoPaciente = document.getElementById('campo-paciente');

        function atualizarVinculos() {
            campoFuncionario.style.display = 'none';
            campoMedico.style.display = 'none';
            campoPaciente.style.display = 'none';
            
            const valorFuncionario = document.getElementById('id_funcionario').value;
            const valorMedico = document.getElementById('id_medico').value;
            const valorPaciente = document.getElementById('id_paciente').value;

            document.getElementById('id_funcionario').value = '';
            document.getElementById('id_medico').value = '';
            document.getElementById('id_paciente').value = '';

            const perfil = perfilSelect.value;

            if (perfil === 'Administrador' || perfil === 'Financeiro' || perfil === 'Recepcionista') {
                campoFuncionario.style.display = 'flex';
                document.getElementById('id_funcionario').value = valorFuncionario;
            } else if (perfil === 'Medico') {
                campoMedico.style.display = 'flex';
                document.getElementById('id_medico').value = valorMedico;
            } else if (perfil === 'Paciente') {
                campoPaciente.style.display = 'flex';
                document.getElementById('id_paciente').value = valorPaciente;
            }
        }

        perfilSelect.addEventListener('change', atualizarVinculos);
        
        if (!document.querySelector('.alerta-erro')) {
            atualizarVinculos();
        } else {
            perfilSelect.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>
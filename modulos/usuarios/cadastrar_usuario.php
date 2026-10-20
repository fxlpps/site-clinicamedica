<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$perfis = ['Administrador', 'Recepcionista', 'Financeiro', 'Medico', 'Paciente'];

$funcionarios = $pdo->query("SELECT id, nome FROM funcionario WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();
$medicos      = $pdo->query("SELECT id, nome FROM medico WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();
$pacientes    = $pdo->query("SELECT id, nome FROM paciente WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';
    $perfil   = trim($_POST['perfil'] ?? '');
    $ativo    = isset($_POST['ativo']) ? 1 : 0;

    // vínculos
    $id_funcionario = !empty($_POST['id_funcionario']) ? (int) $_POST['id_funcionario'] : null;
    $id_medico      = !empty($_POST['id_medico']) ? (int) $_POST['id_medico'] : null;
    $id_paciente    = !empty($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : null;

    if ($login === '' || $senha === '' || $confirma === '' || $perfil === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!in_array($perfil, $perfis, true)) {
        $erro = 'Selecione um perfil válido.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        try {
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuario 
                    (id_funcionario, id_medico, id_paciente, login, senha_hash, perfil, ativo)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_funcionario,
                $id_medico,
                $id_paciente,
                $login,
                $hash,
                $perfil,
                $ativo
            ]);

            header('Location: listar_usuario.php?sucesso=1');
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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Usuário</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Novo Usuário</h1>
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
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                           placeholder="Ex.: joao.silva">
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" required minlength="6">
                    </div>
                    <div class="campo">
                        <label for="confirma_senha">Confirmar senha *</label>
                        <input type="password" id="confirma_senha" name="confirma_senha" required minlength="6">
                    </div>
                </div>

                <div class="campo">
                    <label for="perfil">Perfil *</label>
                    <select id="perfil" name="perfil" required>
                        <option value="">Selecione</option>
                        <?php foreach ($perfis as $p): ?>
                            <option value="<?php echo $p; ?>"
                                <?php echo (($_POST['perfil'] ?? '') === $p) ? 'selected' : ''; ?>>
                                <?php echo $p; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3 class="form-secao">Vínculo (opcional)</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Vincule este login a um funcionário, médico ou paciente existente.</p>

                
                <div class="campo" id="campo-funcionario" style="display: none;">
                    <label for="id_funcionario">Funcionário vinculado</label>
                    <select id="id_funcionario" name="id_funcionario">
                        <option value="">Nenhum</option>
                        <?php foreach ($funcionarios as $f): ?>
                            <option value="<?php echo $f['id']; ?>"
                                <?php echo ((int)($_POST['id_funcionario'] ?? 0) === (int)$f['id']) ? 'selected' : ''; ?>>
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
                                <?php echo ((int)($_POST['id_medico'] ?? 0) === (int)$m['id']) ? 'selected' : ''; ?>>
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
                                <?php echo ((int)($_POST['id_paciente'] ?? 0) === (int)$p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="ativo" value="1"
                            <?php echo (!isset($_POST['ativo']) || $_POST['ativo']) ? 'checked' : ''; ?>>
                        Usuário ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_usuario.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar</button>
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
            
            document.getElementById('id_funcionario').value = '';
            document.getElementById('id_medico').value = '';
            document.getElementById('id_paciente').value = '';

            const perfil = perfilSelect.value;

            if (perfil === 'Administrador' || perfil === 'Financeiro' || perfil === 'Recepcionista') {
                campoFuncionario.style.display = 'flex';
            } else if (perfil === 'Medico') {
                campoMedico.style.display = 'flex';
            } else if (perfil === 'Paciente') {
                campoPaciente.style.display = 'flex';
            }
        }

        perfilSelect.addEventListener('change', atualizarVinculos);
        atualizarVinculos(); // Roda ao carregar (útil se der erro no POST)
    </script>
</body>
</html>
<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$pacientes = $pdo->query("SELECT id, nome FROM paciente WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();
$medicos = $pdo->query("SELECT id, nome, especialidade FROM medico WHERE status = 'ATIVO' ORDER BY nome ASC")->fetchAll();

$prioridades = ['Agendada', 'Preferencial', 'Espontânea', 'Urgente'];
$status_opcoes = ['Agendada', 'Realizada', 'Cancelada'];
$pagamentos_status = ['PENDENTE', 'PAGO', 'PARCIAL', 'CANCELADO'];
$pagamentos_forma = ['DINHEIRO', 'PIX', 'CARTAO', 'CONVENIO'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_medico = !empty($_POST['id_medico']) ? (int) $_POST['id_medico'] : 0;
    $id_paciente = !empty($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
    $data_consulta = trim($_POST['data_consulta'] ?? '');
    $horario = trim($_POST['horario'] ?? '');
    $prioridade = trim($_POST['prioridade'] ?? 'Agendada');
    $status = trim($_POST['status'] ?? 'Agendada');
    
    $valor = trim($_POST['valor'] ?? '');
    $pagamento_status = trim($_POST['pagamento_status'] ?? 'PENDENTE');
    $pagamento_forma = trim($_POST['pagamento_forma'] ?? '');
    
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($id_medico <= 0 || $id_paciente <= 0 || $data_consulta === '' || $horario === '') {
        $erro = 'Preencha os campos obrigatórios (Paciente, Médico, Data e Hora).';
    } elseif (!in_array($prioridade, $prioridades, true)) {
        $erro = 'Prioridade inválida.';
    } elseif (!in_array($status, $status_opcoes, true)) {
        $erro = 'Status inválido.';
    } else {
        try {
            $sql = "INSERT INTO consulta 
                    (id_medico, id_paciente, valor, pagamento_status, pagamento_forma, 
                     data_consulta, horario, prioridade, diagnostico, status, observacoes)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_medico, 
                $id_paciente, 
                $valor !== '' ? (float)$valor : null, 
                $pagamento_status, 
                $pagamento_forma !== '' ? $pagamento_forma : null,
                $data_consulta, 
                $horario, 
                $prioridade, 
                $diagnostico !== '' ? $diagnostico : null, 
                $status, 
                $observacoes !== '' ? $observacoes : null
            ]);

            header('Location: listar_consulta.php?sucesso=1');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Já existe uma consulta para este médico neste mesmo dia e horário.';
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
    <title>Nova Consulta</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Nova Consulta</h1>
                <a href="listar_consulta.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <h3 class="form-secao">Agendamento</h3>

                <div class="campo">
                    <label for="id_paciente">Paciente *</label>
                    <select id="id_paciente" name="id_paciente" required>
                        <option value="">Selecione</option>
                        <?php foreach ($pacientes as $p): ?>
                            <option value="<?php echo $p['id']; ?>"
                                <?php echo ((int)($_POST['id_paciente'] ?? 0) === (int)$p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="id_medico">Médico *</label>
                    <select id="id_medico" name="id_medico" required>
                        <option value="">Selecione</option>
                        <?php foreach ($medicos as $m): ?>
                            <option value="<?php echo $m['id']; ?>"
                                <?php echo ((int)($_POST['id_medico'] ?? 0) === (int)$m['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['nome'] . ' - ' . ($m['especialidade'] ?? 'Generalista')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="data_consulta">Data *</label>
                        <input type="date" id="data_consulta" name="data_consulta" required
                               value="<?php echo htmlspecialchars($_POST['data_consulta'] ?? date('Y-m-d')); ?>">
                    </div>
                    <div class="campo">
                        <label for="horario">Horário *</label>
                        <input type="time" id="horario" name="horario" required
                               value="<?php echo htmlspecialchars($_POST['horario'] ?? ''); ?>">
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="prioridade">Prioridade *</label>
                        <select id="prioridade" name="prioridade" required>
                            <?php foreach ($prioridades as $p): ?>
                                <option value="<?php echo $p; ?>"
                                    <?php echo (($_POST['prioridade'] ?? 'Agendada') === $p) ? 'selected' : ''; ?>>
                                    <?php echo $p; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <?php foreach ($status_opcoes as $s): ?>
                                <option value="<?php echo $s; ?>"
                                    <?php echo (($_POST['status'] ?? 'Agendada') === $s) ? 'selected' : ''; ?>>
                                    <?php echo $s; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h3 class="form-secao">Pagamento</h3>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="valor">Valor (R$)</label>
                        <input type="number" id="valor" name="valor" min="0" step="0.01"
                               value="<?php echo htmlspecialchars($_POST['valor'] ?? ''); ?>">
                    </div>
                    <div class="campo">
                        <label for="pagamento_forma">Forma de Pagamento</label>
                        <select id="pagamento_forma" name="pagamento_forma">
                            <option value="">Selecione</option>
                            <?php foreach ($pagamentos_forma as $f): ?>
                                <option value="<?php echo $f; ?>"
                                    <?php echo (($_POST['pagamento_forma'] ?? '') === $f) ? 'selected' : ''; ?>>
                                    <?php echo $f; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label for="pagamento_status">Status do Pagamento</label>
                    <select id="pagamento_status" name="pagamento_status">
                        <?php foreach ($pagamentos_status as $ps): ?>
                            <option value="<?php echo $ps; ?>"
                                <?php echo (($_POST['pagamento_status'] ?? 'PENDENTE') === $ps) ? 'selected' : ''; ?>>
                                <?php echo $ps; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3 class="form-secao">Informações Clínicas</h3>

                <div class="campo">
                    <label for="diagnostico">Diagnóstico</label>
                    <textarea id="diagnostico" name="diagnostico" rows="3"><?php echo htmlspecialchars($_POST['diagnostico'] ?? ''); ?></textarea>
                </div>

                <div class="campo">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                </div>

                <div class="acoes-form">
                    <a href="listar_consulta.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
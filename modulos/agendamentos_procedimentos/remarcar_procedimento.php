<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$sql_pacientes = "SELECT id, nome FROM paciente WHERE status = 'ATIVO' ORDER BY nome ASC";
$pacientes = $pdo->query($sql_pacientes)->fetchAll();
$lista_pacientes = [];
foreach ($pacientes as $p) {
    $lista_pacientes[] = ['id' => $p['id'], 'nome' => $p['nome']];
}

$sql_procedimentos = "SELECT id, nome, valor FROM procedimento WHERE ativo = 1 ORDER BY nome ASC";
$procedimentos = $pdo->query($sql_procedimentos)->fetchAll();
$lista_procedimentos = [];
foreach ($procedimentos as $p) {
    $lista_procedimentos[] = ['id' => $p['id'], 'nome' => $p['nome'], 'valor' => $p['valor']];
}

$sql_medicos = "SELECT id, nome, especialidade FROM medico WHERE status = 'ATIVO' ORDER BY nome ASC";
$medicos = $pdo->query($sql_medicos)->fetchAll();
$lista_medicos = [];
foreach ($medicos as $m) {
    $lista_medicos[] = [
        'id' => $m['id'], 
        'nome' => $m['nome'] . ' - ' . ($m['especialidade'] ?? 'Generalista')
    ];
}

$status_opcoes = ['AGENDADO', 'REALIZADO', 'CANCELADO'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_agenda_procedimento.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = !empty($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
    $id_procedimento = !empty($_POST['id_procedimento']) ? (int) $_POST['id_procedimento'] : 0;
    $id_medico = !empty($_POST['id_medico']) ? (int) $_POST['id_medico'] : null;
    $data_procedimento = trim($_POST['data_procedimento'] ?? '');
    $horario = trim($_POST['horario'] ?? '');
    $valor = trim($_POST['valor'] ?? '');
    $status = trim($_POST['status'] ?? 'AGENDADO');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($id_paciente <= 0 || $id_procedimento <= 0 || $data_procedimento === '' || $horario === '' || $valor === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!is_numeric($valor) || (float)$valor < 0) {
        $erro = 'O valor deve ser um número válido.';
    } else {
        try {
            $sql = "UPDATE agendamento_procedimento SET 
                        id_paciente = ?, 
                        id_procedimento = ?, 
                        id_medico = ?, 
                        data_procedimento = ?, 
                        horario = ?, 
                        valor = ?, 
                        status = ?, 
                        observacoes = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_paciente,
                $id_procedimento, 
                $id_medico, 
                $data_procedimento, 
                $horario, 
                (float)$valor, 
                $status, 
                $observacoes !== '' ? $observacoes : null,
                $id
            ]);

            header('Location: listar_agenda_procedimento.php?sucesso=editado');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM agendamento_procedimento WHERE id = ?");
$stmt->execute([$id]);
$agendamento = $stmt->fetch();

if (!$agendamento) {
    header('Location: listar_agenda_procedimento.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
    <style>
        .autocomplete-container { position: relative; width: 100%; }
        .autocomplete-input { width: 100%; padding: 10px 12px; border: 2px solid #d4dcff; border-radius: 8px; font-size: 15px; font-family: Arial, sans-serif; outline: none; transition: 0.2s; }
        .autocomplete-input:focus { border-color: #3d56c4; box-shadow: 0 0 0 3px rgba(61, 86, 196, 0.15); }
        .autocomplete-list { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d4dcff; border-top: none; border-radius: 0 0 8px 8px; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: none; margin: 0; padding: 0; list-style: none; }
        .autocomplete-list li { padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        .autocomplete-list li:hover { background-color: #f5f7fb; color: #3d56c4; }
    </style>
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Editar Agendamento</h1>
                <a href="listar_agenda_procedimento.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <h3 class="form-secao">Dados do Procedimento</h3>

                <div class="campo">
                    <label>Paciente *</label>
                    <div class="autocomplete-container">
                        <input type="text" id="busca_paciente" class="autocomplete-input" 
                               placeholder="Digite o nome do paciente..." autocomplete="off" required>
                        <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo htmlspecialchars($agendamento['id_paciente']); ?>">
                        <ul class="autocomplete-list" id="lista_pacientes"></ul>
                    </div>
                </div>

                <div class="campo">
                    <label>Procedimento *</label>
                    <div class="autocomplete-container">
                        <input type="text" id="busca_procedimento" class="autocomplete-input" 
                               placeholder="Digite o nome do procedimento..." autocomplete="off" required>
                        <input type="hidden" name="id_procedimento" id="id_procedimento" value="<?php echo htmlspecialchars($agendamento['id_procedimento']); ?>">
                        <ul class="autocomplete-list" id="lista_procedimentos"></ul>
                    </div>
                </div>

                <div class="campo">
                    <label>Médico / Profissional (Opcional)</label>
                    <div class="autocomplete-container">
                        <input type="text" id="busca_medico" class="autocomplete-input" 
                               placeholder="Digite o nome do médico..." autocomplete="off">
                        <input type="hidden" name="id_medico" id="id_medico" value="<?php echo htmlspecialchars($agendamento['id_medico'] ?? ''); ?>">
                        <ul class="autocomplete-list" id="lista_medicos"></ul>
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="data_procedimento">Data *</label>
                        <input type="date" id="data_procedimento" name="data_procedimento" required
                               value="<?php echo htmlspecialchars($agendamento['data_procedimento']); ?>">
                    </div>
                    <div class="campo">
                        <label for="horario">Horário *</label>
                        <input type="time" id="horario" name="horario" required
                               value="<?php echo htmlspecialchars(substr($agendamento['horario'], 0, 5)); ?>">
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="valor">Valor (R$) *</label>
                        <input type="number" id="valor" name="valor" required min="0" step="0.01"
                               value="<?php echo htmlspecialchars($agendamento['valor']); ?>">
                    </div>
                    <div class="campo">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <?php foreach ($status_opcoes as $s): ?>
                                <option value="<?php echo $s; ?>"
                                    <?php echo ($agendamento['status'] === $s) ? 'selected' : ''; ?>>
                                    <?php echo $s; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="campo">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($agendamento['observacoes'] ?? ''); ?></textarea>
                </div>

                <div class="acoes-form">
                    <a href="listar_agenda_procedimento.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function configurarAutocomplete(elementoInput, elementoHidden, elementoLista, dados, preencherValor = false) {
            const input = elementoInput;
            const hidden = elementoHidden;
            const listaUl = elementoLista;

            if (hidden.value) {
                const salvo = dados.find(d => d.id == hidden.value);
                if (salvo) input.value = salvo.nome;
            }

            input.addEventListener('input', function() {
                const termo = this.value.toLowerCase();
                listaUl.innerHTML = '';
                
                if (termo.length === 0) {
                    listaUl.style.display = 'none';
                    hidden.value = '';
                    return;
                }

                const filtradas = dados.filter(d => d.nome.toLowerCase().includes(termo));

                if (filtradas.length > 0) {
                    filtradas.forEach(d => {
                        const li = document.createElement('li');
                        li.textContent = d.nome;
                        li.addEventListener('click', function() {
                            input.value = d.nome;
                            hidden.value = d.id;
                            listaUl.style.display = 'none';

                            if (preencherValor && d.valor) {
                                document.getElementById('valor').value = d.valor;
                            }
                        });
                        listaUl.appendChild(li);
                    });
                    listaUl.style.display = 'block';
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'Nenhum resultado encontrado';
                    li.style.color = '#999';
                    li.style.cursor = 'default';
                    listaUl.appendChild(li);
                    listaUl.style.display = 'block';
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.autocomplete-container')) {
                    listaUl.style.display = 'none';
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    hidden.value = '';
                }
            });
        }

        const pacientesData = <?php echo json_encode($lista_pacientes); ?>;
        const procedimentosData = <?php echo json_encode($lista_procedimentos); ?>;
        const medicosData = <?php echo json_encode($lista_medicos); ?>;

        configurarAutocomplete(document.getElementById('busca_paciente'), document.getElementById('id_paciente'), document.getElementById('lista_pacientes'), pacientesData);
        configurarAutocomplete(document.getElementById('busca_procedimento'), document.getElementById('id_procedimento'), document.getElementById('lista_procedimentos'), procedimentosData, true);
        configurarAutocomplete(document.getElementById('busca_medico'), document.getElementById('id_medico'), document.getElementById('lista_medicos'), medicosData);
    </script>
</body>
</html>
<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$sql_tipos = "SELECT id, nome FROM tipo_exame WHERE ativo = 1 ORDER BY nome ASC";
$tipos_exame = $pdo->query($sql_tipos)->fetchAll();

$lista_tipos = [];
foreach ($tipos_exame as $t) {
    $lista_tipos[] = ['id' => $t['id'], 'nome' => $t['nome']];
}

$sql_consultas = "SELECT c.id, c.data_consulta, p.nome AS nome_paciente 
                  FROM consulta c 
                  JOIN paciente p ON c.id_paciente = p.id 
                  ORDER BY p.nome ASC";
$consultas = $pdo->query($sql_consultas)->fetchAll();

$lista_busca = [];
foreach ($consultas as $c) {
    $lista_busca[] = [
        'id' => $c['id'],
        'nome' => $c['nome_paciente'] . ' - ' . date('d/m/Y', strtotime($c['data_consulta']))
    ];
}

$status_opcoes = ['Agendada', 'Realizada', 'Cancelada'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_exame.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta = !empty($_POST['id_consulta']) ? (int) $_POST['id_consulta'] : 0;
    $id_tipo = !empty($_POST['id_tipo']) ? (int) $_POST['id_tipo'] : 0;
    $data_realizacao = trim($_POST['data_realizacao'] ?? '');
    $hora_realizacao = trim($_POST['hora_realizacao'] ?? '');
    $status = trim($_POST['status'] ?? 'Agendada');
    $resultado = trim($_POST['resultado'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($id_consulta <= 0 || $id_tipo <= 0 || $data_realizacao === '' || $hora_realizacao === '') {
        $erro = 'Preencha os campos obrigatórios.';
    } else {
        try {
            $sql = "UPDATE exame SET 
                        id_consulta = ?, 
                        id_tipo = ?, 
                        data_realizacao = ?, 
                        hora_realizacao = ?, 
                        resultado = ?, 
                        status = ?, 
                        observacoes = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_consulta, $id_tipo, $data_realizacao, $hora_realizacao, 
                $resultado !== '' ? $resultado : null, $status, 
                $observacoes !== '' ? $observacoes : null, $id
            ]);

            header('Location: listar_exame.php?sucesso=editado');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM exame WHERE id = ?");
$stmt->execute([$id]);
$exame = $stmt->fetch();

if (!$exame) {
    header('Location: listar_exame.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Exame</title>
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
                <h1>Editar Exame</h1>
                <a href="listar_exame.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <h3 class="form-secao">Dados do Exame</h3>

                <div class="campo">
                    <label>Buscar Consulta (Paciente) *</label>
                    <div class="autocomplete-container">
                        <input type="text" id="busca_consulta" class="autocomplete-input" 
                               placeholder="Digite o nome do paciente..." autocomplete="off" required>
                        <input type="hidden" name="id_consulta" id="id_consulta" value="<?php echo htmlspecialchars($exame['id_consulta']); ?>">
                        <ul class="autocomplete-list" id="lista_consultas"></ul>
                    </div>
                </div>

                <div class="campo">
                    <label>Buscar Tipo de Exame *</label>
                    <div class="autocomplete-container">
                        <input type="text" id="busca_tipo" class="autocomplete-input" 
                               placeholder="Digite o nome do exame..." autocomplete="off" required>
                        <input type="hidden" name="id_tipo" id="id_tipo" value="<?php echo htmlspecialchars($exame['id_tipo']); ?>">
                        <ul class="autocomplete-list" id="lista_tipos"></ul>
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="data_realizacao">Data de Realização *</label>
                        <input type="date" id="data_realizacao" name="data_realizacao" required
                               value="<?php echo htmlspecialchars($exame['data_realizacao']); ?>">
                    </div>
                    <div class="campo">
                        <label for="hora_realizacao">Hora de Realização *</label>
                        <input type="time" id="hora_realizacao" name="hora_realizacao" required
                               value="<?php echo htmlspecialchars(substr($exame['hora_realizacao'], 0, 5)); ?>">
                    </div>
                </div>

                <div class="campo">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <?php foreach ($status_opcoes as $s): ?>
                            <option value="<?php echo $s; ?>"
                                <?php echo ($exame['status'] === $s) ? 'selected' : ''; ?>>
                                <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3 class="form-secao">Resultados e Observações</h3>

                <div class="campo">
                    <label for="resultado">Resultado do Exame</label>
                    <textarea id="resultado" name="resultado" rows="4"><?php echo htmlspecialchars($exame['resultado'] ?? ''); ?></textarea>
                </div>

                <div class="campo">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($exame['observacoes'] ?? ''); ?></textarea>
                </div>

                <div class="acoes-form">
                    <a href="listar_exame.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function configurarAutocomplete(idInput, idHidden, idLista, dados) {
            const input = document.getElementById(idInput);
            const hidden = document.getElementById(idHidden);
            const listaUl = document.getElementById(idLista);

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

        const consultasData = <?php echo json_encode($lista_busca); ?>;
        const tiposData = <?php echo json_encode($lista_tipos); ?>;

        configurarAutocomplete('busca_consulta', 'id_consulta', 'lista_consultas', consultasData);
        configurarAutocomplete('busca_tipo', 'id_tipo', 'lista_tipos', tiposData);
    </script>
</body>
</html>
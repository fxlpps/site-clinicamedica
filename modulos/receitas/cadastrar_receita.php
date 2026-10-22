<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

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

$sql_medicamentos = "SELECT id, nome, forma FROM medicamento WHERE ativo = 1 ORDER BY nome ASC";
$medicamentos = $pdo->query($sql_medicamentos)->fetchAll();

$lista_medicamentos = [];
foreach ($medicamentos as $m) {
    $lista_medicamentos[] = [
        'id' => $m['id'],
        'nome' => $m['nome'] . ' (' . $m['forma'] . ')'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta = !empty($_POST['id_consulta']) ? (int) $_POST['id_consulta'] : 0;
    
    $medicamentos_post = $_POST['medicamento'] ?? [];
    $dosagens = $_POST['dosagem'] ?? [];
    $posologias = $_POST['posologia'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    $unidades = $_POST['unidade'] ?? [];

    if ($id_consulta <= 0) {
        $erro = 'Selecione uma consulta válida.';
    } else {
        $itens_validos = [];
        for ($i = 0; $i < count($medicamentos_post); $i++) {
            $id_med = (int) $medicamentos_post[$i];
            $dos = trim($dosagens[$i] ?? '');
            $pos = trim($posologias[$i] ?? '');
            $qtd = (int) ($quantidades[$i] ?? 0);
            $uni = trim($unidades[$i] ?? '');

            if ($id_med > 0 && $dos !== '' && $pos !== '' && $qtd > 0 && $uni !== '') {
                $itens_validos[] = [
                    'id_medicamento' => $id_med,
                    'dosagem' => $dos,
                    'posologia' => $pos,
                    'quantidade' => $qtd,
                    'unidade' => $uni
                ];
            }
        }

        if (count($itens_validos) === 0) {
            $erro = 'Adicione pelo menos um medicamento com todos os dados preenchidos.';
        } else {
            try {
                $pdo->beginTransaction();

                $stmt_rec = $pdo->prepare("INSERT INTO receita (id_consulta) VALUES (?)");
                $stmt_rec->execute([$id_consulta]);
                $id_receita = $pdo->lastInsertId();

                $stmt_rm = $pdo->prepare("INSERT INTO receita_medicamento 
                    (id_receita, id_medicamento, dosagem, posologia, quantidade, unidade) 
                    VALUES (?, ?, ?, ?, ?, ?)");

                foreach ($itens_validos as $item) {
                    $stmt_rm->execute([
                        $id_receita,
                        $item['id_medicamento'],
                        $item['dosagem'],
                        $item['posologia'],
                        $item['quantidade'],
                        $item['unidade']
                    ]);
                }

                $pdo->commit();
                header('Location: listar_receita.php?sucesso=1');
                exit;

            } catch (PDOException $e) {
                $pdo->rollBack();
                $erro = 'Erro ao salvar receita: ' . $e->getMessage();
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
    <title>Nova Receita</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
    <style>
        .autocomplete-container { position: relative; width: 100%; }
        .autocomplete-input { width: 100%; padding: 10px 12px; border: 2px solid #d4dcff; border-radius: 8px; font-size: 15px; font-family: Arial, sans-serif; outline: none; transition: 0.2s; }
        .autocomplete-input:focus { border-color: #3d56c4; box-shadow: 0 0 0 3px rgba(61, 86, 196, 0.15); }
        .autocomplete-list { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d4dcff; border-top: none; border-radius: 0 0 8px 8px; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: none; margin: 0; padding: 0; list-style: none; }
        .autocomplete-list li { padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        .autocomplete-list li:hover { background-color: #f5f7fb; color: #3d56c4; }

        .item-receita {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            position: relative;
        }
        .btn-remover-item {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #fde2e2;
            color: #dc2626;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-remover-item:hover { background: #fca5a5; }
    </style>
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Nova Receita</h1>
                <a href="listar_receitas.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao" style="max-width: 900px;">
                <h3 class="form-secao">Consulta Vinculada</h3>

                <div class="campo">
                    <label>Buscar Consulta (Paciente) *</label>
                    <div class="autocomplete-container">
                        <input type="text" id="busca_consulta" class="autocomplete-input" 
                               placeholder="Digite o nome do paciente..." autocomplete="off" required>
                        <input type="hidden" name="id_consulta" id="id_consulta" value="<?php echo htmlspecialchars($_POST['id_consulta'] ?? ''); ?>">
                        <ul class="autocomplete-list" id="lista_consultas"></ul>
                    </div>
                </div>

                <h3 class="form-secao">Medicamentos Prescritos</h3>
                
                <div id="container-itens">
                    <div class="item-receita">
                        <div class="campo">
                            <label>Medicamento *</label>
                            <div class="autocomplete-container">
                                <input type="text" class="autocomplete-input busca-medicamento" placeholder="Digite o nome do medicamento..." autocomplete="off" required>
                                <input type="hidden" name="medicamento[]" class="id-medicamento" value="">
                                <ul class="autocomplete-list lista-medicamentos"></ul>
                            </div>
                        </div>

                        <div class="campo-linha">
                            <div class="campo">
                                <label>Dosagem *</label>
                                <input type="text" name="dosagem[]" required placeholder="Ex: 500mg">
                            </div>
                            <div class="campo">
                                <label>Quantidade *</label>
                                <input type="number" name="quantidade[]" required min="1" placeholder="Ex: 20">
                            </div>
                            <div class="campo">
                                <label>Unidade *</label>
                                <input type="text" name="unidade[]" required placeholder="Ex: comprimidos">
                            </div>
                        </div>

                        <div class="campo">
                            <label>Posologia *</label>
                            <input type="text" name="posologia[]" required placeholder="Ex: Tomar 1 comprimido a cada 8 horas">
                        </div>
                    </div>
                </div>

                <button type="button" id="btn-add-med" style="background: #eef2ff; color: #3d56c4; border: 2px dashed #d4dcff; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; margin-bottom: 20px;">
                    + Adicionar outro medicamento
                </button>

                <div class="acoes-form">
                    <a href="listar_receitas.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Emitir Receita</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function configurarAutocomplete(elementoInput, elementoHidden, elementoLista, dados) {
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
        const medicamentosData = <?php echo json_encode($lista_medicamentos); ?>;

        configurarAutocomplete(
            document.getElementById('busca_consulta'), 
            document.getElementById('id_consulta'), 
            document.getElementById('lista_consultas'), 
            consultasData
        );

        function configurarNovoMedicamento(container) {
            const inputBusca = container.querySelector('.busca-medicamento');
            const inputHidden = container.querySelector('.id-medicamento');
            const listaUl = container.querySelector('.lista-medicamentos');

            configurarAutocomplete(inputBusca, inputHidden, listaUl, medicamentosData);
        }

        document.querySelectorAll('.item-receita').forEach(item => {
            configurarNovoMedicamento(item);
        });

        const containerItens = document.getElementById('container-itens');
        const btnAdd = document.getElementById('btn-add-med');

        btnAdd.addEventListener('click', function() {
            const primeiroItem = document.querySelector('.item-receita');
            const novoItem = primeiroItem.cloneNode(true);

            novoItem.querySelector('.busca-medicamento').value = '';
            novoItem.querySelector('.id-medicamento').value = '';
            novoItem.querySelector('.lista-medicamentos').innerHTML = '';
            novoItem.querySelector('input[name="dosagem[]"]').value = '';
            novoItem.querySelector('input[name="quantidade[]"]').value = '';
            novoItem.querySelector('input[name="unidade[]"]').value = '';
            novoItem.querySelector('input[name="posologia[]"]').value = '';

            const btnRemover = document.createElement('button');
            btnRemover.type = 'button';
            btnRemover.className = 'btn-remover-item';
            btnRemover.innerHTML = 'X';
            btnRemover.addEventListener('click', function() {
                novoItem.remove();
            });
            novoItem.appendChild(btnRemover);

            containerItens.appendChild(novoItem);
            configurarNovoMedicamento(novoItem);
        });
    </script>
</body>
</html>
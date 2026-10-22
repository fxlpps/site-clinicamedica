<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_receitas.php');
    exit;
}

$sql_receita = "SELECT r.id, r.data_receita, 
                       p.nome AS nome_paciente, p.cpf AS cpf_paciente, 
                       m.nome AS nome_medico, m.crm AS crm_medico,
                       c.data_consulta
                FROM receita r
                JOIN consulta c ON r.id_consulta = c.id
                JOIN paciente p ON c.id_paciente = p.id
                JOIN medico m ON c.id_medico = m.id
                WHERE r.id = ?";
$stmt = $pdo->prepare($sql_receita);
$stmt->execute([$id]);
$receita = $stmt->fetch();

if (!$receita) {
    header('Location: listar_receitas.php');
    exit;
}

$sql_itens = "SELECT rm.*, med.nome AS nome_medicamento, med.forma 
              FROM receita_medicamento rm
              JOIN medicamento med ON rm.id_medicamento = med.id
              WHERE rm.id_receita = ?";
$stmt_itens = $pdo->prepare($sql_itens);
$stmt_itens->execute([$id]);
$itens = $stmt_itens->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Receita</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
    <style>
        .receita-papel {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
            color: #333;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .receita-header {
            border-bottom: 2px solid #3d56c4;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .receita-header h2 { color: #3d56c4; margin: 0; }
        .receita-info { font-size: 15px; margin-bottom: 25px; }
        .receita-info p { margin: 5px 0; }
        .receita-itens { margin-top: 30px; }
        .receita-item {
            margin-bottom: 20px;
            padding-left: 20px;
            border-left: 3px solid #d4dcff;
        }
        .receita-item strong { font-size: 16px; color: #3d56c4; }
        .receita-assinatura {
            margin-top: 60px;
            text-align: center;
        }
        .receita-assinatura .linha {
            border-top: 1px solid #333;
            width: 300px;
            margin: 0 auto 10px;
        }
        @media print {
            .sidebar, .header, .btn-voltar, .btn-imprimir { display: none !important; }
            .layout { display: block; }
            .main { margin: 0; padding: 0; width: 100%; }
            .receita-papel { box-shadow: none; max-width: 100%; }
        }
    </style>
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Visualizar Receita</h1>
                <div>
                    <button onclick="window.print()" class="btn-salvar btn-imprimir" style="margin-right: 10px;">Imprimir</button>
                    <a href="listar_receitas.php" class="btn-voltar">Voltar</a>
                </div>
            </div>

            <div class="receita-papel">
                <div class="receita-header">
                    <h2>Clínica Nakamura</h2>
                    <span style="color: #666;">Data: <?php echo date('d/m/Y', strtotime($receita['data_receita'])); ?></span>
                </div>

                <div class="receita-info">
                    <p><strong>Paciente:</strong> <?php echo htmlspecialchars($receita['nome_paciente']); ?></p>
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars($receita['cpf_paciente']); ?></p>
                    <p><strong>Data da Consulta:</strong> <?php echo date('d/m/Y', strtotime($receita['data_consulta'])); ?></p>
                </div>

                <div class="receita-itens">
                    <h3 style="color: #3d56c4; margin-bottom: 20px;">Prescrição</h3>
                    <?php foreach ($itens as $index => $item): ?>
                        <div class="receita-item">
                            <strong><?php echo ($index + 1); ?>. <?php echo htmlspecialchars($item['nome_medicamento'] . ' - ' . $item['forma']); ?></strong>
                            <p><strong>Dosagem:</strong> <?php echo htmlspecialchars($item['dosagem']); ?></p>
                            <p><strong>Posologia:</strong> <?php echo htmlspecialchars($item['posologia']); ?></p>
                            <p><strong>Quantidade:</strong> <?php echo $item['quantidade'] . ' ' . htmlspecialchars($item['unidade']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="receita-assinatura">
                    <div class="linha"></div>
                    <p><strong><?php echo htmlspecialchars($receita['nome_medico']); ?></strong></p>
                    <p><?php echo htmlspecialchars($receita['crm_medico']); ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
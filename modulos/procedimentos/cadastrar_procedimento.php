<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$duracoes = [
    '00:15:00' => '15 minutos',
    '00:30:00' => '30 minutos',
    '00:45:00' => '45 minutos',
    '01:00:00' => '1 hora',
    '01:30:00' => '1 hora e 30 minutos',
    '02:00:00' => '2 horas',
    '03:00:00' => '3 horas',
    '04:00:00' => '4 horas',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $valor     = trim($_POST['valor'] ?? '');
    $duracao   = trim($_POST['duracao'] ?? '');
    $ativo     = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '' || $descricao === '' || $valor === '' || $duracao === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!is_numeric($valor) || (float)$valor < 0) {
        $erro = 'O valor deve ser um número válido.';
    } elseif (!array_key_exists($duracao, $duracoes)) {
        $erro = 'Selecione uma duração válida.';
    } else {
        try {
            $sql = "INSERT INTO procedimento (nome, descricao, valor, duracao, ativo)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nome,
                $descricao,
                (float)$valor,
                $duracao,
                $ativo
            ]);

            header('Location: listar_procedimento.php?sucesso=1');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Procedimento</title>
    <link rel="icon" type="image/png" href="<?php echo $base; ?>/img/logo1simbolo.png">
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Novo Procedimento</h1>
                <a href="listar_procedimento.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <div class="campo">
                    <label for="nome">Nome do procedimento *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                           placeholder="Ex.: Limpeza de pele">
                </div>

                <div class="campo">
                    <label for="descricao">Descrição *</label>
                    <textarea id="descricao" name="descricao" rows="4" required
                              placeholder="Descreva o procedimento..."><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="valor">Valor (R$) *</label>
                        <input type="number" id="valor" name="valor" required min="0" step="0.01"
                               value="<?php echo htmlspecialchars($_POST['valor'] ?? ''); ?>"
                               placeholder="150.00">
                    </div>
                    <div class="campo">
                        <label for="duracao">Duração estimada *</label>
                        <select id="duracao" name="duracao" required>
                            <option value="">Selecione</option>
                            <?php foreach ($duracoes as $valorTime => $label): ?>
                                <option value="<?php echo $valorTime; ?>"
                                    <?php echo (($_POST['duracao'] ?? '') === $valorTime) ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="ativo" value="1" checked>
                        Procedimento ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_procedimento.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
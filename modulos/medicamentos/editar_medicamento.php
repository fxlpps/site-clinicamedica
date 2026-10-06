<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$formas = ['Comprimido', 'Cápsula', 'Xarope', 'Gotas', 'Pomada', 'Creme', 'Injeção', 'Suspensão', 'Spray', 'Outro'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_medicamento.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $forma = trim($_POST['forma'] ?? '');
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '') {
        $erro = 'O campo Nome é obrigatório.';
    } elseif (!in_array($forma, $formas, true)) {
        $erro = 'Selecione uma forma válida.';
    } else {
        try {
            $sql = "UPDATE medicamento SET nome = ?, forma = ?, ativo = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $forma, $ativo, $id]);

            header('Location: listar_medicamento.php?sucesso=editado');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM medicamento WHERE id = ?");
$stmt->execute([$id]);
$medicamento = $stmt->fetch();

if (!$medicamento) {
    header('Location: listar_medicamento.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Editar Medicamento</h1>
                <a href="listar_medicamento.php" class="btn-voltar">← Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <div class="campo">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($medicamento['nome']); ?>">
                </div>

                <div class="campo">
                    <label for="forma">Forma *</label>
                    <select id="forma" name="forma" required>
                        <option value="">— Selecione —</option>
                        <?php foreach ($formas as $f): ?>
                            <option value="<?php echo htmlspecialchars($f); ?>"
                                <?php echo ($medicamento['forma'] === $f) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($f); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="ativo" value="1" <?php echo $medicamento['ativo'] ? 'checked' : ''; ?>>
                        Ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_medicamento.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
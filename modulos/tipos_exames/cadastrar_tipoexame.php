<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '') {
        $erro = 'O campo Nome é obrigatório.';
    } else {
        try {
            $sql = "INSERT INTO tipo_exame (nome, ativo) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $ativo]);

            header('Location: listar_tipoexame.php?sucesso=1');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Já existe um tipo de exame com esse nome.';
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
    <title>Novo Tipo de Exame</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Novo Tipo de Exame</h1>
                <a href="listar_tipoexame.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <div class="campo">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                           placeholder="Ex.: Hemograma completo">
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="ativo" value="1" checked>
                        Ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_tipoexame.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
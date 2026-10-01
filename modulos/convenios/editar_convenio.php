<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

// ID - url
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_convenio.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $cnpj     = trim($_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $ativo    = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '') {
        $erro = 'O campo Nome é obrigatório.';
    } else {
        try {
            $sql = "UPDATE convenio
                    SET nome = ?, cnpj = ?, telefone = ?, ativo = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nome,
                $cnpj !== '' ? $cnpj : null,
                $telefone !== '' ? $telefone : null,
                $ativo,
                $id
            ]);

            header('Location: listar_convenio.php?sucesso=editado');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Já existe um convênio com esse nome.';
            } else {
                $erro = 'Erro ao salvar: ' . $e->getMessage();
            }
        }
    }
}

// busca convenio
$stmt = $pdo->prepare("SELECT * FROM convenio WHERE id = ?");
$stmt->execute([$id]);
$convenio = $stmt->fetch();

if (!$convenio) {
    header('Location: listar_convenio.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Convênio</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Editar Convênio</h1>
                <a href="listar_convenio.php" class="btn-voltar">← Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <div class="campo">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($convenio['nome']); ?>">
                </div>

                <div class="campo">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" maxlength="18"
                           value="<?php echo htmlspecialchars($convenio['cnpj'] ?? ''); ?>"
                           placeholder="00.000.000/0000-00">
                </div>

                <div class="campo">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" maxlength="15"
                           value="<?php echo htmlspecialchars($convenio['telefone'] ?? ''); ?>"
                           placeholder="(00) 00000-0000">
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="ativo" value="1" <?php echo $convenio['ativo'] ? 'checked' : ''; ?>>
                        Ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_convenio.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        
        const cnpjInput = document.getElementById('cnpj');
        cnpjInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 14);
            v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
            v = v.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = v;
        });

        
        const telInput = document.getElementById('telefone');
        telInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) {
                v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            } else if (v.length > 6) {
                v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
            } else if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
            } else if (v.length > 0) {
                v = v.replace(/^(\d{0,2})$/, '($1');
            }
            e.target.value = v;
        });
    </script>
</body>
</html>
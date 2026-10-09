<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$base = '/site-clinicamedica';
$erro = '';

$ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

$especialidades = [
    'Clínico Geral',
    'Cardiologia',
    'Dermatologia',
    'Endocrinologia',
    'Gastroenterologia',
    'Ginecologia',
    'Neurologia',
    'Oftalmologia',
    'Ortopedia',
    'Otorrinolaringologia',
    'Pediatria',
    'Psiquiatria',
    'Urologia',
    'Outra'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome            = trim($_POST['nome'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $crm             = trim($_POST['crm'] ?? '');
    $especialidade   = trim($_POST['especialidade'] ?? '');
    $cpf             = trim($_POST['cpf'] ?? '');
    $rg              = trim($_POST['rg'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $telefone        = trim($_POST['telefone'] ?? '');
    $cep             = trim($_POST['cep'] ?? '');
    $estado          = trim($_POST['estado'] ?? '');
    $cidade          = trim($_POST['cidade'] ?? '');
    $bairro          = trim($_POST['bairro'] ?? '');
    $rua             = trim($_POST['rua'] ?? '');
    $numero          = trim($_POST['numero'] ?? '');
    $salario         = trim($_POST['salario'] ?? '');
    $status          = isset($_POST['status']) ? 'ATIVO' : 'INATIVO';

    if ($nome === '' || $email === '' || $crm === '' || $cpf === '' || $rg === '' ||
        $data_nascimento === '' || $telefone === '' || $cep === '' || $estado === '' ||
        $cidade === '' || $bairro === '' || $rua === '' || $numero === '' || $salario === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!in_array($estado, $ufs, true)) {
        $erro = 'Selecione um estado válido.';
    } elseif (!is_numeric($salario) || (float)$salario < 0) {
        $erro = 'Salário deve ser um número válido.';
    } elseif ($especialidade !== '' && !in_array($especialidade, $especialidades, true)) {
        $erro = 'Selecione uma especialidade válida.';
    } else {
        try {
            $sql = "INSERT INTO medico 
                    (nome, email, crm, especialidade, cpf, rg, data_nascimento, telefone,
                     cep, estado, cidade, bairro, rua, numero, salario, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nome, $email, $crm,
                $especialidade !== '' ? $especialidade : null,
                $cpf, $rg, $data_nascimento, $telefone,
                $cep, $estado, $cidade, $bairro, $rua, $numero,
                (float)$salario,
                $status
            ]);

            header('Location: listar_medico.php?sucesso=1');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Já existe um médico com esse CRM, CPF ou RG.';
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
    <title>Novo Médico</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/css/style.css">
</head>
<body class="dashbody">
    <?php include '../../includes/header.php'; ?>

    <div class="layout">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main">
            <div class="pagina-topo">
                <h1>Novo Médico</h1>
                <a href="listar_medico.php" class="btn-voltar">Voltar</a>
            </div>

            <?php if ($erro): ?>
                <div class="alerta alerta-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-padrao">
                <h3 class="form-secao">Dados Pessoais</h3>

                <div class="campo">
                    <label for="nome">Nome completo *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                </div>

                <div class="campo">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="cpf">CPF *</label>
                        <input type="text" id="cpf" name="cpf" required maxlength="14"
                               value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>"
                               placeholder="000.000.000-00">
                    </div>
                    <div class="campo">
                        <label for="rg">RG *</label>
                        <input type="text" id="rg" name="rg" required maxlength="20"
                               value="<?php echo htmlspecialchars($_POST['rg'] ?? ''); ?>">
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="data_nascimento">Data de nascimento *</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required
                               value="<?php echo htmlspecialchars($_POST['data_nascimento'] ?? ''); ?>">
                    </div>
                    <div class="campo">
                        <label for="telefone">Telefone *</label>
                        <input type="text" id="telefone" name="telefone" required maxlength="15"
                               value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>"
                               placeholder="(00) 00000-0000">
                    </div>
                </div>

                <h3 class="form-secao">Dados Profissionais</h3>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="crm">CRM *</label>
                        <input type="text" id="crm" name="crm" required maxlength="20"
                               value="<?php echo htmlspecialchars($_POST['crm'] ?? ''); ?>"
                               placeholder="Ex.: CRM/SP 123456">
                    </div>
                    <div class="campo">
                        <label for="salario">Salário *</label>
                        <input type="number" id="salario" name="salario" required min="0" step="0.01"
                               value="<?php echo htmlspecialchars($_POST['salario'] ?? ''); ?>"
                               placeholder="10000.00">
                    </div>
                </div>

                <div class="campo">
                    <label for="especialidade">Especialidade</label>
                    <select id="especialidade" name="especialidade">
                        <option value="">Generalista (sem especialidade)</option>
                        <?php foreach ($especialidades as $e): ?>
                            <option value="<?php echo htmlspecialchars($e); ?>"
                                <?php echo (($_POST['especialidade'] ?? '') === $e) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3 class="form-secao">Endereço</h3>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="cep">CEP *</label>
                        <input type="text" id="cep" name="cep" required maxlength="9"
                               value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>"
                               placeholder="00000-000">
                    </div>
                    <div class="campo">
                        <label for="estado">Estado *</label>
                        <select id="estado" name="estado" required>
                            <option value="">— Selecione —</option>
                            <?php foreach ($ufs as $uf): ?>
                                <option value="<?php echo $uf; ?>"
                                    <?php echo (($_POST['estado'] ?? '') === $uf) ? 'selected' : ''; ?>>
                                    <?php echo $uf; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo">
                        <label for="cidade">Cidade *</label>
                        <input type="text" id="cidade" name="cidade" required
                               value="<?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>">
                    </div>
                    <div class="campo">
                        <label for="bairro">Bairro *</label>
                        <input type="text" id="bairro" name="bairro" required
                               value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>">
                    </div>
                </div>

                <div class="campo-linha">
                    <div class="campo campo-grande">
                        <label for="rua">Rua *</label>
                        <input type="text" id="rua" name="rua" required
                               value="<?php echo htmlspecialchars($_POST['rua'] ?? ''); ?>">
                    </div>
                    <div class="campo campo-pequeno">
                        <label for="numero">Número *</label>
                        <input type="text" id="numero" name="numero" required
                               value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>">
                    </div>
                </div>

                <div class="campo campo-check">
                    <label>
                        <input type="checkbox" name="status" value="1"
                            <?php echo (!isset($_POST['status']) || $_POST['status']) ? 'checked' : ''; ?>>
                        Médico ativo
                    </label>
                </div>

                <div class="acoes-form">
                    <a href="listar_medico.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar">Salvar</button>
                </div>
            </form>
        </main>
    </div>
 <!-- máscaras -->
    <script>
        document.getElementById('cpf').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = v;
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
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

        document.getElementById('cep').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 8);
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = v;
        });
    </script>
</body>
</html>
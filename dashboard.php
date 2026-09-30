<?php
// require 'includes/verifica_sessao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashbody">
    <?php include 'includes/header.php'; ?>

    <div class="layout">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main">
            <h1>Dashboard</h1>
            <h2>Bem vindo, <?php echo htmlspecialchars($_SESSION['usuario_login'] ?? 'Visitante'); ?>!</h2>
        </main>
    </div>
</body>
</html>
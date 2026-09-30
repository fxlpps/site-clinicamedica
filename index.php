<?php
session_start();
$erro = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="body1">
    <div class="backgroundlogo">
        <img src="img/logo2sf.png" alt="Logo">
    </div>

    <div class="contlogin">
        <div>
            <h1 class="titulo1">Login</h1>
        </div>

        <div>
            <form class="formlogin" method="POST" action="auth/valida_login.php">
                <div class="contlogin2">
                    <label for="usuario" class="label1">Usuário:</label>
                    <input type="text" id="usuario" name="usuario" class="input1" required>
                </div>

                <div class="contlogin2">
                    <label for="senha" class="label1">Senha:</label>
                    <input type="password" id="senha" name="senha" class="input1" required>
                </div>

                <button type="submit" class="button1">Entrar</button>

                <?php if ($erro): ?>
                    <p style="color: #ff6b6b; font-family: Arial; font-size: 14px; text-align: center;">
                        Usuário ou senha incorretos.
                    </p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
<?php
require 'config/conexao.php';

$login = 'admin';
$nova_senha = '123456';

$hash = password_hash($nova_senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE usuario SET senha_hash = ? WHERE login = ?");
$stmt->execute([$hash, $login]);

if ($stmt->rowCount() > 0) {
    echo "Senha resetada!<br>Login: <strong>$login</strong><br>Senha: <strong>$nova_senha</strong>";
} else {
    echo "Usuário '$login' não existe. Verifique no banco.";
}
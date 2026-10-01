<?php
require 'config/conexao.php';
// ARQUIVO PROVISÓRIO ---------------------
$login = 'admin';
$senha = '123456';
$perfil = 'Administrador';

$hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario (login, senha_hash, perfil, ativo)
        VALUES (?, ?, ?, 1)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login, $hash, $perfil]);
    echo "<h2>Admin criado com sucesso!</h2>";
    echo "<p>Login: <strong>admin</strong></p>";
    echo "<p>Senha: <strong>123456</strong></p>";
    echo "<p><strong>Apague este arquivo agora.</strong></p>";
} catch (PDOException $e) {
    echo "<h2>Erro ao criar:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
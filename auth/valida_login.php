<?php
session_start();
require __DIR__ . '/../config/conexao.php';

$login = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($login === '' || $senha === '') {
    header('Location: ../index.php?erro=1');
    exit;
}

$sql = "SELECT * FROM usuario WHERE login = ? AND ativo = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$login]);
$user = $stmt->fetch();

if ($user && password_verify($senha, $user['senha_hash'])) {
    $_SESSION['usuario_id']     = $user['id'];
    $_SESSION['usuario_login']  = $user['login'];
    $_SESSION['usuario_perfil'] = $user['perfil'];

    header('Location: ../dashboard.php');
    exit;
}

header('Location: ../index.php?erro=1');
exit;
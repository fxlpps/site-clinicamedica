<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_usuario.php');
    exit;
}

if ($id === (int) $_SESSION['usuario_id']) {
    header('Location: listar_usuario.php?erro=proprio_usuario');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        header('Location: listar_usuario.php?sucesso=excluido');
    } else {
        header('Location: listar_usuario.php?erro=nao_encontrado');
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        header('Location: listar_usuario.php?erro=em_uso');
    } else {
        header('Location: listar_usuario.php?erro=desconhecido');
    }
}
exit;
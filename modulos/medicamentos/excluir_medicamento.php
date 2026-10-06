<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_medicamento.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM medicamento WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        header('Location: listar_medicamento.php?sucesso=excluido');
    } else {
        header('Location: listar_medicamento.php?erro=nao_encontrado');
    }
} catch (PDOException $e) {
    // Código 23000 = violação de FK (medicamento em uso em receita)
    if ($e->getCode() === '23000') {
        header('Location: listar_medicamento.php?erro=em_uso');
    } else {
        header('Location: listar_medicamento.php?erro=desconhecido');
    }
}
exit;
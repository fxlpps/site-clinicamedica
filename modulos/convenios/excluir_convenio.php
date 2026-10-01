<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_convenio.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM convenio WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        header('Location: listar_convenio.php?sucesso=excluido');
    } else {
        header('Location: listar_convenio.php?erro=nao_encontrado');
    }
} catch (PDOException $e) {
    //Código 23000 é: violação de FK (convênio em uso)
    if ($e->getCode() === '23000') {
        header('Location: listar_convenio.php?erro=em_uso');
    } else {
        header('Location: listar_convenio.php?erro=desconhecido');
    }
}
exit;
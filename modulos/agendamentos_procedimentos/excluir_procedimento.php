<?php
require '../../includes/verifica_sessao.php';
require '../../config/conexao.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: listar_agenda_procedimento.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM agendamento_procedimento WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        header('Location: listar_agenda_procedimento.php?sucesso=excluido');
    } else {
        header('Location: listar_agenda_procedimento.php?erro=nao_encontrado');
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        header('Location: listar_agenda_procedimento.php?erro=em_uso');
    } else {
        header('Location: listar_agenda_procedimento.php?erro=desconhecido');
    }
}
exit;
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nome_usuario = $_SESSION['usuario_login'] ?? 'Visitante';
?>
<header class="header">
    <div class="logo">
        <img src="img/logo2simbolo.png" class="logodashboard" alt="Logo">
    </div>
    <div class="headerright">
        <span class="notif">
            <img src="img/sinobranco.png" class="icondashboard" alt="Notificações">
        </span>
        <div class="user">
            <span class="usericon">
                <img src="img/userbranco.png" class="icondashboard" id="usericon" alt="Usuário">
            </span>
            <span class="usertxt"><?php echo htmlspecialchars($nome_usuario); ?></span>
        </div>
    </div>
</header>
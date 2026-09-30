<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$perfil = $_SESSION['usuario_perfil'] ?? '';

//Vê se o perfil atual está na lista de permitidos
function podeVer(array $perfis_permitidos, string $perfil_atual): bool {
    return in_array($perfil_atual, $perfis_permitidos, true);
}
?>
<aside class="sidebar">
    <nav>
        <a href="dashboard.php" class="menuitem">
            <span>Início</span>
        </a>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Medico'], $perfil)): ?>
            <a href="modulos/pacientes/listar_paciente.php" class="menuitem">
                <span>Pacientes</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Financeiro'], $perfil)): ?>
            <a href="modulos/medicos/listar_medico.php" class="menuitem">
                <span>Médicos</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Financeiro'], $perfil)): ?>
            <a href="modulos/funcionarios/listar_funcionario.php" class="menuitem">
                <span>Funcionários</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Financeiro'], $perfil)): ?>
            <a href="modulos/convenios/listar_convenio.php" class="menuitem">
                <span>Convênios</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Medico'], $perfil)): ?>
            <a href="modulos/medicamentos/listar_medicamento.php" class="menuitem">
                <span>Medicamentos</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Medico'], $perfil)): ?>
            <a href="modulos/tipos_exames/listar_tipoexame.php" class="menuitem">
                <span>Tipos de Exame</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Medico'], $perfil)): ?>
            <a href="modulos/procedimentos/listar_procedimento.php" class="menuitem">
                <span>Procedimentos</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Medico', 'Financeiro', 'Paciente'], $perfil)): ?>
            <a href="modulos/consultas/listar_consulta.php" class="menuitem">
                <span>Consultas</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Medico', 'Financeiro', 'Paciente'], $perfil)): ?>
            <a href="modulos/exames/listar_exame.php" class="menuitem">
                <span>Exames</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Recepcionista', 'Medico', 'Financeiro', 'Paciente'], $perfil)): ?>
            <a href="modulos/agendamentos_procedimentos/listar_agenda_procedimento.php" class="menuitem">
                <span>Agendamentos</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador', 'Medico', 'Paciente'], $perfil)): ?>
            <a href="modulos/receitas/listar_receitas.php" class="menuitem">
                <span>Receitas</span>
            </a>
        <?php endif; ?>

        <?php if (podeVer(['Administrador'], $perfil)): ?>
            <a href="modulos/usuarios/listar_usuario.php" class="menuitem">
                <span>Usuários</span>
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-bottom">
        <a href="#" class="menuitem">
            <span>Configurações</span>
        </a>
        <a href="logout.php" class="menuitem logout">
            <span>Sair</span>
        </a>
    </div>
</aside>
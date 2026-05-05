<ul class="account-nav-list">
    <li>
        <a href="<?= URL ?>account" class="<?= ($this->title == 'Mi Cuenta') ? 'active' : '' ?>">
            <i class="fas fa-id-card"></i> Datos
        </a>
    </li>
    <li>
        <a href="<?= URL ?>inscripcion">
            <i class="fas fa-running"></i> Mis Carreras
        </a>
    </li>
    <li>
        <a href="<?= URL ?>account/password">
            <i class="fas fa-key"></i> Seguridad
        </a>
    </li>
    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] < 3): ?>
        <li>
            <a href="<?= URL ?>carrera/gestion">
                <i class="fas fa-users-cog"></i> Panel de Eventos
            </a>
        </li>
    <?php endif; ?>
    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
        <li>
            <a href="<?= URL ?>user">
                <i class="fas fa-users-cog"></i> Panel de Usuarios
            </a>
        </li>
    <?php endif; ?>
    
    <?php if ($_SESSION['role_id'] == 3): ?>
        <?php if ($this->perfil_completo): ?>
            <li>
                <a href="<?= URL ?>account/request_upgrade">
                    <i class="fas fa-rocket"></i> Solicitar ser Organizador
                </a>
            </li>
        <?php else: ?>
            <li class="disabled" title="Completa tu perfil para activar esta opción">
                <span style="color: gray; cursor: not-allowed;">
                    <i class="fas fa-lock"></i> Ser Organizador (Perfil incompleto)
                </span>
            </li>
        <?php endif; ?>
    <?php endif; ?>
    
    <li>
        <a href="<?= URL ?>auth/logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </li>
</ul>
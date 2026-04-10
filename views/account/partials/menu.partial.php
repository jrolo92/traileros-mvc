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
        <a href="<?= URL ?>account/resultados">
            <i class="fas fa-trophy"></i> Resultados
        </a>
    </li>
    <li>
        <a href="<?= URL ?>account/password">
            <i class="fas fa-key"></i> Seguridad
        </a>
    </li>
    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
        <li>
            <a href="<?= URL ?>user" class="<?= (isset($_GET['url']) && strpos($_GET['url'], 'user') !== false) ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i> Panel de Usuarios
            </a>
        </li>
    <?php endif; ?>
    
    <li>
        <a href="<?= URL ?>auth/logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </li>
</ul>
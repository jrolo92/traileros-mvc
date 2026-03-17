<header class="main-header">
    <div class="header-container">
        <div class="header-left">
            <div class="logo">
                <a href="<?= URL ?>main">
                    <i class="fas fa-mountain"></i>
                    <span>TRAILEROS</span>
                </a>
            </div>
        </div>

        <div class="header-right">
            <nav class="header-nav">
                <a href="<?= URL ?>carrera" class="<?= (isset($_GET['url']) && strpos($_GET['url'], 'carrera') !== false) ? 'active' : '' ?>">
                    Carreras
                </a>
            </nav>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <button class="btn-user">
                        <i class="fas fa-user-circle"></i> 
                        <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Mi Perfil') ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; margin-left: 5px;"></i>
                    </button>
                    
                    <div class="dropdown-content">
                        <a href="<?= URL ?>account">
                            <i class="fas fa-id-card"></i> Mi Perfil
                        </a>
                        <a href="<?= URL ?>account/edit">
                            <i class="fas fa-user-edit"></i> Editar Datos
                        </a>
                        <a class="nav-link" href="<?= URL ?>ayuda">
                            <i class="fas fa-question-circle"></i> Acerca de
                        </a>
                        <hr>
                        <a href="<?= URL ?>auth/logout" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= URL ?>auth/login" class="btn-login-header">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>
</header>
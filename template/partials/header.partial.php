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
            <!-- Acceso a panel de control -->
            <nav class="header-nav">
                <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)): ?>
                    <a href="<?= URL ?>carrera/gestion" title="Gestionar Carreras"> Gestión de Eventos</a>
                <?php endif; ?>
            </nav>

            <nav class ="header-nav">
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                    <a href="<?= URL ?>user" title="Administrar Usuarios">Gestión de Usuarios</a>
                <?php endif; ?>
            </nav>

            <nav class="header-nav">
                <a href="<?= URL ?>carrera" class="<?= (isset($_GET['url']) && strpos($_GET['url'], 'carrera') !== false) ? 'active' : '' ?>">
                    Carreras
                </a>
            </nav>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <button class="btn-user">
                        <div class="avatar-circle size-sm">
                            <?php if (!empty($_SESSION['user_avatar']) && file_exists($_SESSION['user_avatar'])): ?>
                                <img src="<?= URL . $_SESSION['user_avatar'] . '?t=' . time() ?>" alt="Avatar">
                            <?php else: ?>
                                <i class="fas fa-user-circle" style="font-size: 1.2rem;"></i>
                            <?php endif; ?>
                        </div>

                        <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Mi Perfil') ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; margin-left: 5px;"></i>
                    </button>
                    
                    <div class="dropdown-content">
                        <a href="<?= URL ?>account">
                            <i class="fas fa-id-card"></i> Mi Perfil
                        </a>
                        <a class="nav-link" href="<?= URL ?>ayuda">
                            <i class="fas fa-question-circle"></i> Acerca de
                        </a>
                        <a class="nav-link" href="<?= URL ?>contact ">
                            <i class="fas fa-paper-plane"></i> Contacto
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

            <!-- Cambiar modo claro/oscuro -->
            <button id="theme-toggle-btn" class="theme-toggle-btn" title="Cambiar tema">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
        </div>
    </div>

</header>
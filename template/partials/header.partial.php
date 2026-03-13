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
                <a href="<?= URL ?>account" class="btn-user">
                    <i class="fas fa-user-circle"></i> 
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Mi Perfil') ?></span>
                </a>
            <?php else: ?>
                <a href="<?= URL ?>auth/login" class="btn-login-header">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>
</header>
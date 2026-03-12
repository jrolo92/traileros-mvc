

    <header class="main-header">
        <div class="header-container">
            <div class="header-left" id="menuToggle">
                <div class="logo">
                    <a href="<?= URL?>main">
                        <i class="fas fa-mountain"></i><span>TRAILEROS</span>
                    </a>
                </div>
            </div>

            <div class="header-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= URL ?>account" class="btn-user">
                        <i class="fas fa-user"></i> <?= $_SESSION['user_name'] ?? 'Mi Perfil' ?>
                    </a>
                <?php else: ?>
                    <a href="<?= URL ?>auth/login" class="btn-login-header">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php include 'template/partials/sidebar.partial.php'; ?>

    <main class="content-wrapper">

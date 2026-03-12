<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->title ?? 'Traileros' ?></title>
    <link rel="stylesheet" href="<?= URL ?>public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-header">
        <div class="header-container">
            <div class="header-left" id="menuToggle">
                <div class="logo">
                    <i class="fas fa-mountain"></i> <span>TRAILEROS</span>
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

    <?php include 'views/layout/sidebar.php'; ?>

    <main class="content-wrapper">

<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
    <script src="<?= URL?>public/js/delete-confirm.js" defer></script>
</head>

<body>
    <!-- Menú fijo superior -->
    <?php require_once 'template/partials/header.partial.php' ?>

    <main class="account-container">

        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <div class="avatar-circle size-md">
                        <?php if (!empty($_SESSION['user_avatar']) && file_exists($_SESSION['user_avatar'])): ?>
                            <img src="<?= URL . $_SESSION['user_avatar'] ?>" alt="Perfil">
                        <?php else: ?>
                            <i class="fas fa-user-circle" style="font-size: 4rem;"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2><?= $this->title ?></h2>
                        <p>Esta acción es irreversible. Se borrarán todos tus datos.</p>
                    </div>
                </div>

                <nav class="account-menu">
                    <?php require_once "views/account/partials/menu.partial.php" ?>
                </nav>
            </header>

            <section class="account-main-content">

                <?php require_once "template/partials/mensaje.partial.php" ?>
                <?php require_once "template/partials/error.partial.php" ?>

                <div class="account-content">
                    <form action="<?= URL ?>account/delete_confirmed" method="post" id="deleteAccountForm" class="account-form">

                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="form-grid">
                            <h3 class="form-section-title">Confirmar eliminación de cuenta:</h3>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <div class="alert-danger-custom">
                                    <i class="fas fa-info-circle"></i>
                                    Al eliminar tu cuenta, perderás el acceso a todas tus participaciones y resultados de carreras de forma permanente.
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Nombre de usuario</label>
                                <input type="text" value="<?= htmlspecialchars($this->account->nombre); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label>Email asociado</label>
                                <input type="email" value="<?= htmlspecialchars($this->account->email); ?>" disabled>
                            </div>
                                
                        </div>

                        <div class="form-actions">
                            <div class="btns-left">
                                <a class="btn-account-cancel" href="<?= URL ?>account">Cancelar y volver</a>
                            </div>

                            <button type="submit" class="btn-account-delete">Eliminar Cuenta</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>


    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>

</body>

</html>
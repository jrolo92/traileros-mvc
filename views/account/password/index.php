<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
</head>

<body>

    <?php require_once 'template/partials/header.partial.php'?>

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
                        <p>Configuración de Seguridad</p>
                    </div>
                </div>

                <nav class="account-menu">
                    <?php require_once "views/account/partials/menu.partial.php"?>
                </nav>
            </header>

            <section class="account-main-content">

                <?php require_once "template/partials/mensaje.partial.php"?>
                <?php require_once "template/partials/error.partial.php"?>

                <div class="account-content">
                    <form action="<?= URL ?>account/update_password" method="post" class="account-form">

                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="form-grid">
                            <h3 class="form-section-title">Cambiar contraseña: </h3>
                            <div class="form-group">
                                <label for="password">Contraseña actual</label>
                                <input id="password" type="password"
                                    class="<?= (isset($this->errors['password'])) ? 'input-error' : '' ?>"
                                    name="password" required autocomplete="current-password" autofocus>
                                
                                <span class="error-msg">
                                    <?= $this->errors['password'] ?? '' ?>
                                </span>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="new_password">Nueva Contraseña</label>
                                <input id="new_password" type="password"
                                    class="<?= (isset($this->errors['new_password'])) ? 'input-error' : '' ?>"
                                    name="new_password" required autocomplete="new-password">
                                
                                <span class="error-msg" >
                                    <?= $this->errors['new_password'] ?? '' ?>
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirmar Contraseña</label>
                                <input id="confirm_password" type="password" 
                                    class="<?= (isset($this->errors['confirm_password'])) ? 'input-error' : '' ?>"
                                    name="confirm_password" required autocomplete="new-password">
                                
                                <span class="error-msg">
                                    <?= $this->errors['confirm_password'] ?? '' ?>
                                </span>
                            </div>
                        </div> <div class="form-actions">
                            <div class="btns-left">
                                <a class="btn-account-cancel" href="<?= URL ?>account">Cancelar</a>
                                <button type="reset" class="btn-account-cancel">Limpiar</button>
                            </div>

                            <button type="submit" class="btn-account-save">Actualizar Contraseña</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>

</body>

</html>
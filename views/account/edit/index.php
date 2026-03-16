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
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <h2><?= $this->title ?></h2>
                        <p>Actualiza la información de tu cuenta personal</p>
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
                    <form action="<?= URL ?>account/update" method="post" class="account-form">

                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="form-grid">
                            <h3 class="form-section-title">Datos personales</h3>
                            <div class="form-group">
                                <label for="name">Nombre Completo</label>
                                <input id="name" type="text" name="name"
                                       class="<?= (isset($this->errors['name'])) ? 'input-error' : '' ?>"
                                       value="<?= htmlspecialchars($this->account->name); ?>"
                                       required autofocus>
                                <?php if (isset($this->errors['name'])): ?>
                                    <span class="error-msg" role="alert">
                                        <?= $this->errors['name'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input id="email" type="email" name="email"
                                       class="<?= (isset($this->errors['email'])) ? 'input-error' : '' ?>"
                                       value="<?= htmlspecialchars($this->account->email); ?>"
                                       required>
                                <?php if (isset($this->errors['email'])): ?>
                                    <span class="error-msg" role="alert">
                                        <?= $this->errors['email'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="btns-left">
                                <a class="btn-account-cancel" href="<?= URL ?>account" role="button">Cancelar</a>
                            </div>
                            <button type="submit" class="btn-account-save">
                                Guardar Cambios
                            </button>
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
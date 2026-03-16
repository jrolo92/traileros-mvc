<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php' ?>

    <main class="account-container">
        <div class="account-card">
            
            <header class="account-header">
                <div class="user-info-main">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <h2><?= $this->title ?></h2>
                        <p>Gestiona los datos de tu cuenta y participaciones</p>
                    </div>
                </div>
                
                <nav class="account-menu">
                    <?php require_once("views/account/partials/menu.partial.php") ?>
                </nav>
            </header>

            <div class="account-content">
                <?php require_once("template/partials/mensaje.partial.php") ?>
                <?php require_once("template/partials/error.partial.php") ?>

                <div class="form-grid">
                    <div class="section-header-flex">
                        <h3 class="form-section-title">Datos Personales</h3>
                        <a href="<?= URL ?>account/edit" class="btn-edit-inline">
                            <i class="fas fa-user-edit"></i> Editar Perfil
                        </a>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" value="<?= htmlspecialchars($this->account->name); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?= htmlspecialchars($this->account->email); ?>" disabled>
                    </div>
                </div>

                <div class="form-grid">
                    <h3 class="form-section-title">Eliminar Cuenta</h3>
                    <div class="danger-zone-inline">
                        <p>Si eliminas tu cuenta, todos tus datos y participaciones se borrarán permanentemente.</p>
                        <a href="<?= URL ?>account/delete/<?= $_SESSION['csrf_token'] ?>" class="btn-account-delete">
                            <i class="fas fa-trash-alt"></i> Eliminar Cuenta
                        </a>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="btn-secondary" href="<?= URL ?>index" role="button">Volver al inicio</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
</body>

</html>
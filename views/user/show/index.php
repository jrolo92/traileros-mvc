<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?> </title>
</head>
<body>
    <?php require_once("template/partials/menu.auth.partial.php") ?>

    <div class="container">
        <br><br><br><br>
        <main>
            <legend>Detalles del Usuario</legend>
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control" value="<?= $this->user->nombre ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="text" class="form-control" value="<?= $this->user->email ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <input type="text" class="form-control" value="<?= $this->user->rol ?>" disabled>
            </div>
            <a class="btn btn-secondary" href="<?= URL ?>user">Volver</a>
        </main>
    </div>
    <?php require_once("template/partials/footer.partial.php") ?>
</body>
</html>
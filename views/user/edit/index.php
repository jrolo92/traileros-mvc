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
        <?php require_once("template/partials/mensaje.partial.php") ?>
        <?php require_once("template/partials/error.partial.php") ?>

        <main>
            <legend>Editar Usuario</legend>
            <form action="<?= URL ?>user/update/<?= $this->id ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre:</label>
                    <input type="text" class="form-control <?= (isset($this->errors['nombre'])) ? 'is-invalid' : null ?>" 
                        name="nombre" value="<?= htmlspecialchars($this->user->nombre) ?>">
                    <span class="form-text text-danger"><?= $this->errors['nombre'] ??= null ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control <?= (isset($this->errors['email'])) ? 'is-invalid' : null ?>" 
                        name="email" value="<?= htmlspecialchars($this->user->email) ?>">
                    <span class="form-text text-danger"><?= $this->errors['email'] ??= null ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol:</label>
                    <select class="form-select <?= isset($this->errors['role_id']) ? 'is-invalid' : '' ?>" name="role_id">
                        <?php foreach ($this->roles as $id => $rol): ?>
                            <option value="<?= $id ?>" <?= ($this->user->role_id == $id) ? 'selected' : '' ?>><?= $rol ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-text text-danger"><?= $this->errors['role_id'] ??= null ?></span>
                </div>

                <a class="btn btn-secondary" href="<?= URL ?>user">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </main>
    </div>
    <?php require_once("template/partials/footer.partial.php") ?>
</body>
</html>
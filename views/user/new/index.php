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
            <legend>Nuevo Usuario</legend>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= URL ?>user/create">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control <?= (isset($this->errors['nombre'])) ? 'is-invalid' : null ?>" 
                                name="nombre" value="<?= htmlspecialchars($this->user->nombre) ?>">
                            <span class="form-text text-danger"><?= $this->errors['nombre'] ??= null ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= (isset($this->errors['email'])) ? 'is-invalid' : null ?>" 
                                name="email" value="<?= htmlspecialchars($this->user->email) ?>">
                            <span class="form-text text-danger"><?= $this->errors['email'] ??= null ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?= (isset($this->errors['password'])) ? 'is-invalid' : null ?>" 
                                name="password">
                            <span class="form-text text-danger"><?= $this->errors['password'] ??= null ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" name="password_confirm">
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Rol</label>
                            <select class="form-select <?= (isset($this->errors['role_id'])) ? 'is-invalid' : null ?>" name="role_id">
                                <option value="" selected disabled>Seleccionar Rol</option>
                                <?php foreach ($this->roles as $id => $nombre_rol): ?>
                                    <option value="<?= $id ?>" 
                                        <?= (($this->user->role_id ?? null) == $id) ? 'selected' : null ?>>
                                        <?= $nombre_rol ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="form-text text-danger"><?= $this->errors['role_id'] ??= null ?></span>
                        </div>

                        <a class="btn btn-secondary" href="<?= URL ?>user" role="button">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php require_once("template/partials/footer.partial.php") ?>
</body>
</html>
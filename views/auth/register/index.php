<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
</head>

<body>

    <!-- Menú fijo superior -->
    <?php require_once 'template/partials/menu.principal.partial.php' ?>

    <!-- Capa Principal -->
    <div class="container">
        <br><br><br><br><br>

        <div class="row justify-content-center">
            <div class="col-md-8">

                <?php require_once("template/partials/mensaje.partial.php") ?>
                <?php require_once("template/partials/error.partial.php") ?>

                <div class="card">
                    <div class="card-header">REGISTRO</div>

                    <div class="card-body">

                        <form method="POST" action="<?= URL ?>auth/validate_register">

                            <!-- token csrf -->
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">

                            <!-- Nombre -->
                            <div class="mb-3 row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Nombre</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control <?= isset($this->errors['name']) ? 'is-invalid' : '' ?>"
                                           name="name"
                                           value="<?= htmlspecialchars($this->name) ?>"
                                           required autofocus>

                                    <span class="form-text text-danger">
                                        <?= $this->errors['name'] ?? '' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control <?= isset($this->errors['email']) ? 'is-invalid' : '' ?>"
                                           name="email"
                                           value="<?= htmlspecialchars($this->email) ?>"
                                           required>

                                    <span class="form-text text-danger">
                                        <?= $this->errors['email'] ?? '' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3 row">
                                <label for="pass" class="col-md-4 col-form-label text-md-right">Contraseña</label>

                                <div class="col-md-6">
                                    <input id="pass" type="password"
                                           class="form-control <?= isset($this->errors['password']) ? 'is-invalid' : '' ?>"
                                           name="password"
                                           value="<?= htmlspecialchars($this->password) ?>"
                                           required>

                                    <span class="form-text text-danger">
                                        <?= $this->errors['password'] ?? '' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirmación Password -->
                            <div class="mb-3 row">
                                <label for="password2" class="col-md-4 col-form-label text-md-right">Repetir contraseña</label>

                                <div class="col-md-6">
                                    <input id="password2" type="password"
                                           class="form-control <?= isset($this->errors['password2']) ? 'is-invalid' : '' ?>"
                                           name="password2"
                                           value="<?= htmlspecialchars($this->password2) ?>"
                                           required>

                                    <span class="form-text text-danger">
                                        <?= $this->errors['password2'] ?? '' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="mb-3 row mb-0">
                                <div class="col-md-8 offset-md-4">

                                    <a class="btn btn-secondary" href="<?= URL ?>index" role="button">
                                        Volver
                                    </a>

                                    <button type="submit" class="btn btn-primary">
                                        Crear cuenta
                                    </button>

                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <?php require_once 'template/partials/footer.partial.php' ?>
    <?php require_once 'template/layouts/javascript.layout.php' ?>

</body>

</html>

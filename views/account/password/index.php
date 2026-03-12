<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
</head>

<body>
    <!-- Menú fijo superior -->
    <?php require_once 'template/partials/menu.auth.partial.php' ?>

    <!-- Capa Principal -->
    <div class="container">
        <br><br><br><br><br>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php require_once("template/partials/mensaje.partial.php") ?>
                <?php require_once("template/partials/error.partial.php") ?>
                <div class="card">
                    <div class="card-header"><?= $this->title ?></div>
                    <div class="card-header">
                        <?php require_once("views/account/partials/menu.partial.php") ?>
                    </div>
                    <div class="card-body">
                        <form action="<?= URL ?>account/update_password" method="post">

                            <!-- token crsf -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <!-- campo password actual -->
                            <div class="mb-3 row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password
                                    actual</label>
                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control <?= (isset($this->errors['password'])) ? 'is-invalid' : null ?>"
                                        name="password" required autocomplete="password" autofocus>
                                    <!-- control de errores -->
                                    <span class="form-text text-danger" role="alert">
                                        <?= $this->errors['password'] ??= '' ?>
                                    </span>
                                </div>
                            </div>
                            <!-- campo nuevo password -->
                            <div class="mb-3 row">
                                <label for="new_password" class="col-md-4 col-form-label text-md-right">Nuevo
                                    password</label>
                                <div class="col-md-6">
                                    <input id="new_password" type="password"
                                        class="form-control <?= (isset($this->errors['new_password'])) ? 'is-invalid' : null ?>"
                                        name="new_password" required autocomplete="new_password" autofocus>
                                    <!-- control de errores -->
                                    <span class="form-text text-danger" role="alert">
                                        <?= $this->errors['new_password'] ??= '' ?>
                                    </span>
                                </div>
                            </div>
                            <!-- campo confirmación password  -->
                            <div class="mb-3 row">
                                <label for="confirm_password" class="col-md-4 col-form-label text-md-right">Confirmar
                                    password</label>
                                <div class="col-md-6">
                                    <input id="confirm_password" type="password" class="form-control <?= (isset($this->errors['confirm_password'])) ? 'is-invalid' : null ?>"
                                        name="confirm_password" required autocomplete="confirm_password" autofocus>
                                </div>
                            </div>
                            <!-- botones de acción -->
                            <div class="mb-3 row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <a class="btn btn-secondary" href="<?= URL ?>account" role="button">Cancelar</a>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary">Actualizar Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- /.container -->

    <?php require_once 'template/partials/footer.partial.php' ?>
    <?php require_once 'template/layouts/javascript.layout.php' ?>

</body>

</html>
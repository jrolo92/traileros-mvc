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
                    <div class="card-header">LOGIN</div>

                    <div class="card-body">
                        <form method="POST" action="<?= URL ?>auth/validate_login">
                            
                            <!-- token csrf -->
                            <input type="hidden" name="csrf_token"
                                value="<?= $_SESSION['csrf_token'] ?>">

                            <!-- campo email -->
                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control <?= (isset($this->errors['email'])) ? 'is-invalid' : null ?>"
                                        id="email" name="email" value="<?= htmlspecialchars($this->email); ?>" required
                                        autocomplete="email" autofocus>

                                    <!-- control de errores -->
                                    <span class="form-text text-danger" role="alert">
                                        <?= $this->errors['email']  ??= '' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- password -->
                            <div class="mb-3 row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control <?= (isset($this->errors['password'])) ? 'is-invalid' : null ?>"
                                        id="password" 
                                        name="password" 
                                        value="<?= htmlspecialchars($this->password)  ?>" 
                                        required
                                        autocomplete="current-password">

                                    <!-- control de errores -->
                                    <span class="form-text text-danger" role="alert">
                                        <?= $this->errors['password']  ??= null ?>
                                    </span>
                                </div>
                            </div>

                            <!-- botones de acción -->
                            <div class="mb-3 row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Recordar
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <a class="btn btn-secondary" href="<?= URL ?>auth/register" role="button">Registrar</a>
                                    <button type="submit" class="btn btn-primary">Acceder</button>

                                    <a class="btn btn-link" href="#">
                                        ¿Olvidó su contraseña?
                                    </a>

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
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
                        <!-- formulario de consulta de datos de la cuenta -->
                        <form>

                            <!-- campo nombre -->
                            <div class="mb-3 row">
                                <label class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name"
                                        value="<?= htmlspecialchars($this->account->name); ?>" disabled>
                                </div>
                            </div>

                            <!-- campo email -->
                            <div class="mb-3 row">
                                <label class="col-md-4 col-form-label text-md-right">Email</label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email"
                                        value="<?= htmlspecialchars($this->account->email); ?>" disabled>

                                </div>
                            </div>

                            <!-- campo rol -->
                            <div class="mb-3 row">
                                <label class="col-md-4 col-form-label text-md-right">Rol</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="role_name"
                                        value="<?= htmlspecialchars($_SESSION['role_name']); ?>" disabled>
                                </div>
                            </div>

                            <!-- botones acción -->
                            <div class="mb-3 row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <a class="btn btn-secondary" href="<?= URL ?>libro"
                                        role="button">Salir</a>
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
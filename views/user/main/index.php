<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?> </title>
</head>

<body>
    <!-- Menú fijo superior -->
    <?php require_once("template/partials/menu.auth.partial.php") ?>

    <!-- Capa Principal -->
    <div class="container">
        <br><br><br><br>

        <!-- capa de mensajes -->
        <?php require_once("template/partials/mensaje.partial.php") ?>

        <!-- capa de errores -->
        <?php require_once("template/partials/error.partial.php") ?>

        <!-- Mostrar tabla de  user -->
        <!-- contenido principal -->
        <main>
            <legend>Tabla de users - Geslibros</legend>
            <!-- Menú principal de gestión de user de FP -->
            <?php require_once("views/user/partials/menu.user.partial.php") ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <!-- cabecera tabla user -->
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Email</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Acciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <!-- $user es un objeto mysqli_result, se puede usar foreach directamente  -->
                        <!-- solo cuando cada iteración devuelve un array asociativo -->
                        <?php while ($user = $this->users->fetch()): ?>
                            <tr class="">
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['nombre']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $user['rol'] ?></td>

                                <!-- botones de acción -->
                                <td>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <!-- boton eliminar -->
                                        <form method="POST" action="<?= URL ?>user/delete/<?= $user['id'] ?>" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm
                                            <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['delete'])? 'disabled' : null ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        <!-- boton editar -->
                                        <a href="<?=  URL ?>user/edit/<?= $user['id'] ?>" class="btn btn-warning btn-sm
                                        <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['edit']) ? 'disabled' : null ?>" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <!-- boton ver -->
                                        <a href="<?=  URL ?>user/show/<?= $user['id'] ?>" class="btn btn-primary btn-sm
                                        <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['show']) ? 'disabled' : null ?>" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>


                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total users: <?= $this->users->rowCount() ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <br><br><br>

        </main>

    </div>

    <!-- /.container -->

    <?php require_once("template/partials/footer.partial.php") ?>
    <?php require_once("template/layouts/javascript.layout.php") ?>

</body>
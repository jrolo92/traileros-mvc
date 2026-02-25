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
            <legend><?= $this->title ?></legend>

            <form>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Evento:</label>
                    <input type="text" class="form-control" value="<?= $this->carrera->nombre ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha de celebración:</label>
                    <input type="date" class="form-control" value="<?= $this->carrera->fecha ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación:</label>
                    <input type="text" class="form-control" value="<?= $this->carrera->ubicacion ?>" disabled>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="distancia" class="form-label">Distancia (km):</label>
                        <input type="number" step="0.01" class="form-control" value="<?= $this->carrera->distancia ?>" disabled>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="desnivel" class="form-label">Desnivel (m+):</label>
                        <input type="number" class="form-control" value="<?= $this->carrera->desnivel ?>" disabled>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dificultad" class="form-label">Dificultad:</label>
                        <input type="text" class="form-control" value="<?= $this->carrera->dificultad ?>" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" rows="3" disabled><?= $this->carrera->descripcion ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="imagenUrl" class="form-label">URL de la Imagen:</label>
                    <input type="text" class="form-control" value="<?= $this->carrera->imagenUrl ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="organizador_id" class="form-label">ID Organizador:</label>
                    <input type="number" class="form-control" value="<?= $this->carrera->organizador_id ?>" disabled>
                </div>

                <a class="btn btn-secondary" href="<?= URL ?>carrera" role="button">Volver</a>
            </form>

            <br><br><br>
        </main>
    </div>

    <?php require_once("template/partials/footer.partial.php") ?>
    <?php require_once("template/layouts/javascript.layout.php") ?>

</body>
</html>
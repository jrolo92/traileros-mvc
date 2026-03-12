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

            <form action="<?= URL ?>carrera/update/<?= $this->id ?>" method="POST">

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Carrera:</label>
                    <input type="text" class="form-control <?= (isset($this->errors['nombre'])) ? 'is-invalid' : null ?>" 
                        name="nombre" value="<?= htmlspecialchars($this->carrera->nombre) ?>">
                    <span class="form-text text-danger">
                        <?= $this->errors['nombre'] ?? null ?>
                    </span>
                </div>

                <div class="mb-3">
                    <label for="ubicacion" class="form-label">Ubicación:</label>
                    <input type="text" class="form-control <?= (isset($this->errors['ubicacion'])) ? 'is-invalid' : null ?>" 
                        name="ubicacion" value="<?= htmlspecialchars($this->carrera->ubicacion) ?>">
                    <span class="form-text text-danger">
                        <?= $this->errors['ubicacion'] ?? null ?>
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="distancia" class="form-label">Distancia (km):</label>
                        <input type="number" step="0.01" class="form-control <?= (isset($this->errors['distancia'])) ? 'is-invalid' : null ?>" 
                            name="distancia" value="<?= htmlspecialchars($this->carrera->distancia) ?>">
                        <span class="form-text text-danger">
                            <?= $this->errors['distancia'] ?? null ?>
                        </span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="desnivel" class="form-label">Desnivel (m+):</label>
                        <input type="number" class="form-control <?= (isset($this->errors['desnivel'])) ? 'is-invalid' : null ?>" 
                            name="desnivel" value="<?= htmlspecialchars($this->carrera->desnivel) ?>">
                        <span class="form-text text-danger">
                            <?= $this->errors['desnivel'] ?? null ?>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" class="form-control <?= (isset($this->errors['fecha'])) ? 'is-invalid' : null ?>" 
                        name="fecha" value="<?= htmlspecialchars($this->carrera->fecha) ?>">
                    <span class="form-text text-danger">
                        <?= $this->errors['fecha'] ?? null ?>
                    </span>
                </div>

                <div class="mb-3">
                    <label for="dificultad" class="form-label">Dificultad:</label>
                    <select class="form-select <?= (isset($this->errors['dificultad'])) ? 'is-invalid' : null ?>" name="dificultad">
                        <?php 
                            $niveles = ['Baja', 'Media', 'Alta', 'Muy Alta'];
                            foreach ($niveles as $nivel): 
                        ?>
                            <option value="<?= $nivel ?>" <?= ($this->carrera->dificultad == $nivel) ? 'selected' : '' ?>>
                                <?= $nivel ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-text text-danger">
                        <?= $this->errors['dificultad'] ?? null ?>
                    </span>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control <?= (isset($this->errors['descripcion'])) ? 'is-invalid' : null ?>" 
                        name="descripcion" rows="3"><?= htmlspecialchars($this->carrera->descripcion) ?></textarea>
                    <span class="form-text text-danger">
                        <?= $this->errors['descripcion'] ?? null ?>
                    </span>
                </div>

                <div class="mb-3">
                    <label for="imagenUrl" class="form-label">URL Imagen:</label>
                    <input type="text" class="form-control <?= (isset($this->errors['imagenUrl'])) ? 'is-invalid' : null ?>" 
                        name="imagenUrl" value="<?= htmlspecialchars($this->carrera->imagenUrl) ?>">
                    <span class="form-text text-danger">
                        <?= $this->errors['imagenUrl'] ?? null ?>
                    </span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Organizador (Solo lectura):</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" disabled>
                    <input type="hidden" name="organizador_id" value="<?= $this->carrera->organizador_id ?>">
                </div>

                <a class="btn btn-secondary" href="<?= URL ?>carrera" role="button"
                    onclick="return confirm('Confirma cancelar actualización')">Cancelar</a>
                <button type="reset" class="btn btn-secondary" onclick="return confirm('Confirma reseteo artículo')">Limpiar</button>
                <button type="submit" class="btn btn-primary">Actualizar Carrera</button>
            </form>

            <br><br><br>
        </main>
    </div>

    <?php require_once("template/partials/footer.partial.php") ?>
    <?php require_once("template/layouts/javascript.layout.php") ?>

</body>
</html>
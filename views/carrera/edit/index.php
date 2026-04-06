<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?> </title>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'; ?>

    <main class="form-page-container">
        <div class="form-card">

            <header class="form-card-header">
                <i class="fas fa-edit"></i>
                <h2><?= $this->title ?></h2>
                <p>Modifica los datos de la carrera seleccionada</p>
            </header>

            <?php require_once 'template/partials/mensaje.partial.php'; ?>
            <?php require_once 'template/partials/error.partial.php'; ?>

            <form action="<?= URL ?>carrera/update/<?= $this->id ?>" method="POST" enctype="multipart/form-data" class="custom-form">
                
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <!-- Campo oculto por si no se modifica la imagen que se envíe la que ya estaba -->
                <input type="hidden" name="imagen_actual" value="<?= $this->carrera['imagen'] ?>">

                <div class="form-group">
                    <label for="nombre">Nombre de la Carrera</label>
                    <input type="text" name="nombre" 
                           class="<?= (isset($this->errors['nombre'])) ? 'input-error' : '' ?>"
                           value="<?= htmlspecialchars($this->carrera['nombre'] ?? '') ?>">
                    <?php if (isset($this->errors['nombre'])): ?>
                        <small class="error-text"><?= $this->errors['nombre'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="ubicacion">Ciudad / Ubicación</label>
                    <input type="text" name="ubicacion" 
                           class="<?= (isset($this->errors['ubicacion'])) ? 'input-error' : '' ?>"
                           value="<?= htmlspecialchars($this->carrera['ubicacion'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="distancia">Distancia (km)</label>
                        <input type="number" step="0.01" name="distancia" 
                               value="<?= htmlspecialchars($this->carrera['distancia'] ?? '') ?>">
                    </div>
                    <div class="form-group col">
                        <label for="desnivel">Desnivel (m+)</label>
                        <input type="number" name="desnivel" 
                               value="<?= htmlspecialchars($this->carrera['desnivel'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="fecha">Fecha del Evento</label>
                        <input type="date" name="fecha" value="<?= htmlspecialchars($this->carrera['fecha'] ?? '') ?>">
                    </div>
                    <div class="form-group col">
                        <label for="dificultad">Dificultad</label>
                        <select name="dificultad">
                            <?php foreach (['Baja', 'Media', 'Alta', 'Muy Alta'] as $nivel): ?>
                                <option value="<?= $nivel ?>" <?= (($this->carrera['dificultad'] ?? '') == $nivel) ? 'selected' : '' ?>>
                                    <?= $nivel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" rows="4"><?= htmlspecialchars($this->carrera['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Imagen actual</label>
                    <div class="current-image-preview mb-3">
                        <img src="<?= URL . 'public/assets/img/carreras/' . $this->carrera['imagen'] ?>" 
                            alt="Vista previa" 
                            style="width: 150px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                    </div>

                    <label for="imagen">Cambiar imagen</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="imagen" id="imagen" accept="image/*" class="custom-file-input">
                        <small class="text-muted">Deja este campo vacío si no quieres cambiar la imagen actual.</small>
                    </div>
                </div>

                <div class="form-group readonly-group">
                    <label>Organizador</label>
                    <div class="fake-input"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                    <input type="hidden" name="organizador_id" value="<?= $this->carrera['organizador_id'] ?? '' ?>">
                </div>

                <div class="form-actions">
                    <a href="<?= URL ?>carrera" class="btn-account-cancel" onclick="return confirm('¿Cancelar actualización?')">
                        Cancelar
                    </a>
                    <div class="main-buttons">
                        <button type="reset" class="btn-account-cancel" onclick="return confirm('¿Limpiar cambios?')">
                            Limpiar
                        </button>
                        <button type="submit" class="btn-account-save">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'; ?>
    </footer>
</body>
</html>
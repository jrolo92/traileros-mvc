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
                <i class="fas fa-mountain"></i>
                <h2>Nueva Carrera</h2>
                <p>Completa los detalles para publicar el evento</p>
            </header>

            <?php require_once 'template/partials/mensaje.partial.php'; ?>
            <?php require_once 'template/partials/error.partial.php'; ?>

            <form action="<?= URL ?>carrera/create" method="POST" class="custom-form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label for="nombre">Nombre de la Carrera</label>
                    <input type="text" name="nombre" 
                           class="<?= (isset($this->errors['nombre'])) ? 'input-error' : '' ?>"
                           value="<?= htmlspecialchars($this->carrera->nombre) ?>" placeholder="Ej: Ultra Trail Mont Blanc">
                    <?php if (isset($this->errors['nombre'])): ?>
                        <small class="error-text"><?= $this->errors['nombre'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="ubicacion">Ciudad / Ubicación</label>
                    <input type="text" name="ubicacion" 
                           value="<?= htmlspecialchars($this->carrera->ubicacion) ?>" placeholder="Ej: Chamonix, Francia">
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="distancia">Distancia (km)</label>
                        <input type="number" step="0.01" name="distancia" 
                               value="<?= htmlspecialchars($this->carrera->distancia) ?>">
                    </div>
                    <div class="form-group col">
                        <label for="desnivel">Desnivel (m+)</label>
                        <input type="number" name="desnivel" 
                               value="<?= htmlspecialchars($this->carrera->desnivel) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="fecha">Fecha del Evento</label>
                        <input type="date" name="fecha" value="<?= htmlspecialchars($this->carrera->fecha) ?>">
                    </div>
                    <div class="form-group col">
                        <label for="dificultad">Dificultad</label>
                        <select name="dificultad">
                            <option selected disabled>Seleccionar...</option>
                            <?php foreach (['Baja', 'Media', 'Alta', 'Muy Alta'] as $nivel): ?>
                                <option value="<?= $nivel ?>" <?= ($this->carrera->dificultad == $nivel) ? 'selected' : '' ?>>
                                    <?= $nivel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" rows="4" placeholder="Describe la ruta, el terreno..."><?= htmlspecialchars($this->carrera->descripcion) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="cupo_maximo">Cupo Máximo de Corredores</label>
                    <input type="number" 
                        name="cupo_maximo" 
                        id="cupo_maximo" 
                        placeholder="Número total de dorsales disponibles."
                        value="<?= $this->carrera->cupo_maximo ?>" 
                        min="1" 
                        required>
                </div>

                <div class="form-group">
                    <label for="precio">Precio de Inscripción (€)</label>
                    <input type="number" 
                        name="precio" 
                        id="precio" 
                        placeholder="Precio base para la inscripción."
                        value="<?= $this->carrera->precio ?>" 
                        step="0.01" 
                        min="0" 
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="edad_minima">Edad mínima</label>
                        <input type="number" step="1" name="edad_minima" min="0" max="99"
                            value="<?= htmlspecialchars($this->carrera->edad_minima) ?? 18 ?>" required>
                        <small class="text-muted">Edad mínima el día del evento.</small>
                    </div>
                    <div class="form-group col">
                        <label for="edad_maxima">Edad máxima</label>
                        <input type="number" step="1" name="edad_maxima" min="0" max="99"
                            value="<?= htmlspecialchars($this->carrera->edad_maxima) ?? 99 ?>" required>
                        <small class="text-muted">Edad máxima el día del evento</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen del Evento</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="imagen" id="imagen" accept="image/*" class="custom-file-input">
                        <small class="form-text text-muted">Formatos admitidos: JPG, PNG. Máx 5MB.</small>
                    </div>
                    <?php if (isset($this->errors['imagen'])): ?>
                        <small class="error-text"><?= $this->errors['imagen'] ?></small>
                    <?php endif; ?>
                </div>

                

                <div class="form-group readonly-group">
                    <label>Organizador</label>
                    <div class="fake-input"><?= $_SESSION['user_name'] ?></div>
                    <input type="hidden" name="organizador_id" value="<?= $_SESSION['user_id'] ?>">
                </div>

                <div class="form-actions">
                    <a href="<?= URL ?>carrera" class="btn-account-cancel" onclick="return confirm('¿Cancelar creación?')">Cancelar</a>
                    <div class="main-buttons">
                        <button type="reset" class="btn-account-cancel">Limpiar</button>
                        <button type="submit" class="btn-account-save">
                            <i class="fas fa-save"></i> Guardar Carrera
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
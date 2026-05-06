<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
    <script src="<?php echo URL ?>public/js/modalidades.js" defer></script>
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

            <form action="<?php echo URL ?>carrera/create" method="POST" class="custom-form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label for="nombre">Nombre de la Carrera</label>
                    <input type="text" name="nombre"
                        class="<?php echo (isset($this->errors['nombre'])) ? 'input-error' : '' ?>"
                        value="<?php echo htmlspecialchars($this->carrera['nombre']) ?>" placeholder="Ej: Ultra Trail Mont Blanc">
                    <?php if (isset($this->errors['nombre'])): ?>
                        <small class="error-text"><?php echo $this->errors['nombre'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="ubicacion">Ciudad / Ubicación</label>
                    <input type="text" name="ubicacion"
                        value="<?php echo htmlspecialchars($this->carrera['ubicacion']) ?>" placeholder="Ej: Chamonix, Francia">
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="fecha">Fecha del Evento</label>
                        <input type="date" name="fecha" value="<?php echo htmlspecialchars($this->carrera['fecha']) ?>" required>
                    </div>
                    <div class="form-group col">
                        <label for="fecha_cierre_inscripcion">Cierre de Inscripciones</label>
                        <input type="date" name="fecha_cierre_inscripcion" 
                            value="<?php echo htmlspecialchars($this->carrera->fecha_cierre_inscripcion ?? '') ?>">
                        <small class="text-muted">Por defecto, el mismo día del evento.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="dificultad">Dificultad</label>
                        <select name="dificultad">
                            <option selected disabled>Seleccionar...</option>
                            <?php foreach (['Baja', 'Media', 'Alta', 'Muy Alta'] as $nivel): ?>
                                <option value="<?php echo $nivel ?>" <?php echo ($this->carrera['dificultad'] == $nivel) ? 'selected' : '' ?>>
                                    <?php echo $nivel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="estado">Estado Inicial</label>
                        <select name="estado">
                            <option selected disabled>Seleccionar...</option>
                            <option value="borrador" <?php echo ($this->carrera['estado'] == 'borrador') ? 'selected' : '' ?>>Borrador (Oculto)</option>
                            <option value="abierto" <?php echo ($this->carrera['estado'] == 'abierto') ? 'selected' : '' ?>>Abierto (Publicado)</option>
                        </select>
                        <small class="text-muted">"Borrador": podrás publicarlo en cualquier momento mas adelante</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" rows="4" placeholder="Describe la ruta, el terreno..."><?php echo htmlspecialchars($this->carrera['descripcion']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="imagen">Imagen del Evento</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="imagen" id="imagen" accept="image/*" class="custom-file-input">
                        <small class="form-text text-muted">Formatos admitidos: JPG, PNG. Máx 5MB.</small>
                    </div>
                    <?php if (isset($this->errors['imagen'])): ?>
                        <small class="error-text"><?php echo $this->errors['imagen'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="modalidades-section">
                    <div class="modalidad-header">
                        <h3><i class="fas fa-list-ol"></i> Modalidades del Evento</h3>
                        <button type="button" id="add-modalidad" class="add-modalidad" title="Añadir Modalidad">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div id="modalidades-container">
                        <div class="modalidad-block card">
                            <div class="modalidad-header">
                                <span class="mod-number" style="padding-bottom:10px;">Modalidad #1</span>
                                <button type="button" class="remove-mod btn-account-delete" style="display:none;" title="Eliminar Modalidad">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label>Nombre Modalidad</label>
                                    <input type="text" name="mod_nombre[]" placeholder="Ej: Mini Trail, Maratón..." required>
                                </div>
                                <div class="form-group col">
                                    <label>Precio (€)</label>
                                    <input type="number" step="0.01" name="mod_precio[]" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label>Distancia (Km)</label>
                                    <input type="number" step="0.01" name="mod_distancia[]" required>
                                </div>
                                <div class="form-group col">
                                    <label>Desnivel (m+)</label>
                                    <input type="number" name="mod_desnivel[]" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="cupo_maximo">Cupo Max.</label>
                                    <input type="number" step="1" name="cupo_maximo" 
                                        value="<?php echo htmlspecialchars($this->carrera['cupo_maximo'])?>" required>
                                </div>
                                <div class="form-group col">
                                    <label for="edad_minima">Edad mín.</label>
                                    <input type="number" step="1" name="edad_minima" min="0" max="99"
                                        value="<?php echo htmlspecialchars($this->carrera['edad_minima']) ?? 18 ?>" required>
                                    <small class="text-muted">Edad mínima el día del evento.</small>
                                </div>
                                <div class="form-group col">
                                    <label for="edad_maxima">Edad máx.</label>
                                    <input type="number" step="1" name="edad_maxima" min="0" max="99"
                                        value="<?php echo htmlspecialchars($this->carrera['edad_maxima']) ?? 99 ?>" required>
                                    <small class="text-muted">Edad máxima el día del evento</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="track_url">URL de Wikiloc (Opcional)</label>
                                <input type="url" name="mod_track_url[]"
                                    value="<?= $this->carrera['track_url'] ?? '' ?>" 
                                    placeholder="https://es.wikiloc.com/...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group readonly-group">
                    <label>Organizador</label>
                    <div class="fake-input"><?php echo $_SESSION['user_name'] ?></div>
                    <input type="hidden" name="organizador_id" value="<?php echo $_SESSION['user_id'] ?>">
                </div>

                <div class="form-actions">
                    <a href="<?php echo URL ?>carrera" class="btn-account-cancel" onclick="return confirm('¿Cancelar creación?')">Cancelar</a>
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
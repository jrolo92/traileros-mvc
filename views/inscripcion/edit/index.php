<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="form-page-container">
        <div class="form-card">

            <header class="form-card-header">
                <i class="fas fa-edit"></i>
                <h2><?= $this->title ?></h2>
                <p>Modifica los detalles de la inscripción del corredor</p>
            </header>

            <?php require_once 'template/partials/mensaje.partial.php'; ?>
            <?php require_once 'template/partials/error.partial.php'; ?>

            <form action="<?= URL ?>inscripcion/update" method="POST" class="custom-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="user_id" value="<?= $this->inscripcion['user_id'] ?>">
            <input type="hidden" name="evento_id" value="<?= $this->inscripcion['evento_id'] ?>">

            <div class="form-section-header">
                <h3>Información del Participante</h3>
            </div>

            <div class="form-row">
                <div class="col form-group readonly-group">
                    <label>Corredor</label>
                    <div class="fake-input">
                        <?= $this->inscripcion['usuario_nombre'] . ' ' . ($this->inscripcion['usuario_apellidos'] ?? '') ?>
                    </div>
                </div>
                <div class="col form-group readonly-group">
                    <label>DNI / NIE</label>
                    <div class="fake-input"><?= $this->inscripcion['usuario_dni'] ?? 'No aportado' ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="col form-group readonly-group">
                    <label>Email</label>
                    <div class="fake-input"><?= $this->inscripcion['usuario_email'] ?></div>
                </div>
                <div class="col form-group readonly-group">
                    <label>Teléfono</label>
                    <div class="fake-input"><?= $this->inscripcion['usuario_telefono'] ?? '---' ?></div>
                </div>
            </div>

            <div class="form-section-header">
                <h3>Datos del Evento</h3>
            </div>

            <div class="form-row">
                <div class="col form-group readonly-group">
                    <label>Carrera</label>
                    <div class="fake-input"><?= $this->inscripcion['evento_nombre'] ?></div>
                </div>
                <div class="col form-group readonly-group">
                    <label>Fecha y Lugar</label>
                    <div class="fake-input">
                        <?= date('d/m/Y', strtotime($this->inscripcion['evento_fecha'])) ?> - <?= $this->inscripcion['evento_lugar'] ?? 'Ubicación pendiente' ?>
                    </div>
                </div>
            </div>

            <div class="form-section-header">
                <h3>Gestión de Dorsal y Pago</h3>
            </div>

            <div class="form-row">
                <div class="col form-group">
                    <label for="categoria_id">Categoría</label>
                    <select name="categoria_id" id="categoria_id" class="form-select">
                        <?php foreach ($this->categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $this->inscripcion['categoria_id']) ? 'selected' : '' ?>>
                                <?= $cat['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col form-group">
                    <label for="dorsal">Dorsal Asignado</label>
                    <input type="number" name="dorsal" id="dorsal" class="form-control" value="<?= $this->inscripcion['dorsal'] ?>" placeholder="Ej: 101">
                </div>
            </div>

            <div class="form-row">
                <div class="col form-group">
                    <label for="metodo_pago">Método de Pago</label>
                    <select name="metodo_pago" id="metodo_pago" class="form-select">
                        <?php foreach ($this->metodos_pago as $mp): ?>
                            <option value="<?= $mp ?>" <?= ($this->inscripcion['metodo_pago'] == $mp) ? 'selected' : '' ?>>
                                <?= ucfirst($mp) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col form-group">
                    <label for="estado_pago">Estado del Pago</label>
                    <select name="estado_pago" id="estado_pago" class="form-select">
                        <option value="pendiente" <?= ($this->inscripcion['estado_pago'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="completado" <?= ($this->inscripcion['estado_pago'] == 'completado') ? 'selected' : '' ?>>Completado</option>
                        <option value="fallido" <?= ($this->inscripcion['estado_pago'] == 'fallido') ? 'selected' : '' ?>>Fallido</option>
                        <option value="cancelado" <?= ($this->inscripcion['estado_pago'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="precio_final">Precio Final (€)</label>
                <input type="number" step="0.01" name="precio_final" id="precio_final" class="form-control" value="<?= $this->inscripcion['precio_final'] ?>">
            </div>

            <div class="form-actions">
               <a href="<?= URL ?>inscripcion" class="btn-account-cancel" 
                    onclick="return confirm('¿Cancelar actualización?')">
                    Cancelar
                </a>
                <div class="main-buttons">
                    <button type="submit" class="btn-account-save">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                </div>
            </div>
        </form>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>
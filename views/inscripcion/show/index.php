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
                <i class="fas fa-id-card"></i>
                <h2><?= $this->title ?></h2>
                <p>Resumen detallado de la inscripción</p>
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
                    <div class="col form-group readonly-group">
                        <label>Categoría</label>
                        <div class="fake-input"><?= $this->inscripcion['categoria_nombre'] ?? 'Sin asignar' ?></div>
                    </div>
                    <div class="col form-group readonly-group">
                        <label>Dorsal Asignado</label>
                        <div class="fake-input"><?= $this->inscripcion['dorsal'] ?? 'Pendiente' ?></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col form-group readonly-group">
                        <label>Método de Pago</label>
                        <div class="fake-input"><?= ucfirst($this->inscripcion['metodo_pago']) ?></div>
                    </div>
                    <div class="col form-group readonly-group">
                        <label>Estado del Pago</label>
                        <div class="fake-input status-text-<?= $this->inscripcion['estado_pago'] ?>">
                            <?= ucfirst($this->inscripcion['estado_pago']) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group readonly-group">
                    <label>Precio Final</label>
                    <div class="fake-input"><?= number_format($this->inscripcion['precio_final'], 2, ',', '.') ?> €</div>
                </div>
            <div class="form-actions">
               <a href="<?= URL ?>inscripcion" class="btn-account-cancel">
                    Volver
                </a>
            </div>
        </form>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>
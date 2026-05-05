<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <?php require_once 'template/partials/mensaje.partial.php'; ?>
    <?php require_once 'template/partials/error.partial.php'; ?>

    <div class="form-page-container">
        <div class="success-card">
            <div class="success-header">
                <div class="icon-circle">
                    <i class="fas fa-check"></i>
                </div>
                <h2><?= $this->title ?></h2>
                <h1>¡Ya tienes tu dorsal!</h1>
                <p>Todo listo para la <strong><?= htmlspecialchars($this->datos['evento_nombre']) ?></strong></p>
            </div>

            <div class="ticket-body">
                <!-- Sección Dorsal -->
                <div class="dorsal-section">
                    <span class="label">TU DORSAL</span>
                    <div class="number">#<?= $this->datos['dorsal'] ?></div>
                </div>

                <!-- Datos de la carrera -->
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= date('d/m/Y', strtotime($this->datos['evento_fecha'])) ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <span><?= htmlspecialchars($this->datos['usuario_nombre']) ?></span>
                    </div>
                </div>

                <!-- Código QR para Check-in -->
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=INS-<?= $this->datos['id'] ?>" alt="QR Check-in">
                    <p>Presenta este código el día de la carrera</p>
                </div>
            </div>

            <div class="success-footer">
                <button onclick="window.print()" class="btn-secondary">
                    <i class="fas fa-print"></i> Imprimir Resguardo
                </button>
                <a href="<?= URL ?>inscripcion" class="btn-primary">Ir a mis inscripciones</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>
<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
    <script src="<?= URL ?>/public/js/precio-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'; ?>

    <main class="form-page-container">
        <div class="form-card">
            <header class="form-card-header">
                <i class="fas fa-file-invoice-dollar"></i>
                <h2>Confirmar Inscripción</h2>
                <p>Estás a un paso de participar en: <strong><?php echo htmlspecialchars($this->evento['nombre']) ?></strong></p>
            </header>

            <?php require_once 'template/partials/mensaje.partial.php'; ?>
            <?php require_once 'template/partials/error.partial.php'; ?>

            <form action="<?php echo URL ?>inscripcion/create" method="POST" class="custom-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="evento_id" value="<?php echo $this->evento['id'] ?>">

                <div class="form-group">
                    <label for="modalidad_id">Selecciona Modalidad</label>
                    <select name="modalidad_id" id="modalidad_id" required>
                        <option disabled selected>Elegir modalidad...</option>
                        <?php foreach ($this->modalidades as $modalidad): ?>
                            <option value="<?= $modalidad['id'] ?>">
                                <?= $modalidad['nombre'] ?> (<?= $modalidad['precio'] ?>€)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Precio de Inscripción</label>
                        <div class="fake-input" id="precio"> -- €</div>
                    </div>
                    <div class="form-group">
                        <label>Fecha del Evento</label>
                        <div class="fake-input"><?php echo date('d/m/Y', strtotime($this->evento['fecha'])) ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Selecciona Método de Pago</label>
                    <select name="metodo_pago" required>
                        <option value="" disabled selected>Elegir método...</option>
                        <option value="stripe">Stripe</option>
                    </select>
                    <small class="text-muted">El pago se procesará de forma segura en el siguiente paso.</small>
                </div>

                <div class="form-group">
                    <label>Información del Corredor</label>
                    <div class="fake-input">
                        <?php echo $_SESSION['user_name'] ?>
                        <small>(La categoría se asignará automáticamente por edad y sexo)</small>
                    </div>
                    <div class="form-section-header">
                        <h3> Verifica tus datos</h3>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tlf">Teléfono de contacto</label>
                            <input type="text" name="tlf" value="<?php echo $this->usuario->tlf ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="talla">Talla de Camiseta</label>
                            <select name="talla">
                                <option value="S" <?php echo $this->usuario->talla == 'S' ? 'selected' : '' ?>>S</option>
                                <option value="M" <?php echo $this->usuario->talla == 'M' ? 'selected' : '' ?>>M</option>
                                <option value="L" <?php echo $this->usuario->talla == 'L' ? 'selected' : '' ?>>L</option>
                                <option value="XL" <?php echo $this->usuario->talla == 'XL' ? 'selected' : '' ?>>XL</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="club">Club (opcional)</label>
                            <input type="text" name="club" value="<?php echo $this->usuario->club ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="file-input-wrapper">
                        <p class="text-muted" style="font-size: 0.85rem;">
                            Al confirmar la inscripción, declaras estar en condiciones físicas óptimas para la práctica de trail running y aceptas el reglamento de la carrera.
                        </p>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?php echo URL ?>carrera/show/<?php echo $this->evento['id'] ?>" class="btn-account-cancel">Volver</a>
                    <div class="main-buttons">
                        <button type="submit" class="btn-account-save">
                            <i class="fas fa-check-circle"></i> Confirmar y Pagar
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
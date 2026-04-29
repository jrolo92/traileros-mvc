<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/menu-order.js" defer></script>
    <script src="<?php echo URL ?>public/js/search-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">
                <header class="account-header">
                    <h2>Inscritos: <?= htmlspecialchars($this->carrera['nombre']) ?></h2>
                    <a href="<?= URL ?>carrera/gestion" class="btn-secondary">Volver a gestión</a>
                </header>

                <section class="account-main-content">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Corredor</th>
                                <th>Email</th>
                                <th>Modalidad</th>
                                <th>Fecha Inscripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->inscritos as $corredor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($corredor['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($corredor['email']) ?></td>
                                    <td><?= htmlspecialchars($this->carrera['modalidad']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($corredor['fecha_inscripcion'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($this->inscritos)): ?>
                                <tr><td colspan="3">Aún no hay valientes inscritos en esta carrera.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>

                <footer class="admin-table-footer">
                    <p>Total de inscripciones localizadas: <strong><?= count($this->inscritos) ?></strong></p>
                </footer>
            </section>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>

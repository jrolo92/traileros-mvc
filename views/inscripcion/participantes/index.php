<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/search-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">
            <header class="account-header">
                <h2>Inscritos: <?php echo htmlspecialchars($this->carrera['nombre']) ?></h2>
                <a href="<?php echo URL ?>carrera/gestion" class="btn-secondary">Volver a gestión</a>
            </header>

            <section class="account-main-content">
                <?php require_once "template/partials/mensaje.partial.php" ?>
                
                <div class="carreras-container">
                    <div class="carreras-toolbar">
                        <div id="subtitle-container">
                            <div class="subtitle-badge">
                                <i class="fas fa-calendar-alt"></i> <?= $this->title ?>
                            </div>
                            <div class="subtitle-badge">
                                <i class="fa-solid fa-flag-checkered"></i> <?= $this->carrera['nombre'] ?>
                            </div>
                        </div>

                        <form class="carreras-search" action="<?php echo URL ?>inscripcion/participantes/<?php echo $this->id_carrera ?>" method="GET">
                            <div class="search-wrapper">
                                <i class="fas fa-search"></i>
                                <input type="search" id="search" name="term" placeholder="Buscar..." value="<?php echo htmlspecialchars($this->term ?? '') ?>" autocomplete="off">
                                <input type="hidden" name="order" value="<?php echo $this->order ?? 1 ?>">
                            </div>
                        </form>
                    </div>

                    <div class="account-content">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th><a href="?order=1&term=<?= $this->term ?>">Id</a></th>
                                        <th><a href="?order=2&term=<?= $this->term ?>">Corredor</a></th>
                                        <th><a href="?order=3&term=<?= $this->term ?>">Email</a></th>
                                        <th><a href="?order=4&term=<?= $this->term ?>">Modalidad</a></th>
                                        <th><a href="?order=5&term=<?= $this->term ?>">Dorsal</a></th>
                                        <th><a href="?order=6&term=<?= $this->term ?>">Fecha</a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->inscritos as $corredor): ?>
                                        <tr>
                                            <td><?= $corredor['inscripcion_id'] ?></td>
                                            <td><?= htmlspecialchars($corredor['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($corredor['email']) ?></td>
                                            <td><?= htmlspecialchars($corredor['modalidad']) ?></td>
                                            <td><?= htmlspecialchars($corredor['dorsal']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($corredor['fecha_inscripcion'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <footer class="admin-table-footer">
                        <p>Total de inscripciones localizadas: <strong><?php echo count($this->inscritos) ?></strong></p>
                    </footer>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title . ": " . $this->carrera['nombre'] ?> </title>
    <script src="<?php echo URL ?>public/js/modal-image.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'; ?>

    <main class="content-wrapper">
        <div class="container">
            <div class="carrera-detail-grid">

                <div class="carrera-media">
                    <img src="<?php echo URL . 'public/assets/img/carreras/' . $this->carrera['imagen'] ?>" alt="<?php echo $this->carrera['nombre'] ?>" class="img-fluid detail-img">
                </div>

                <div class="carrera-info-panel">
                    <div class="back-navigation">
                        <a href="<?= URL ?>carrera" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <header class="detail-header">
                        <span class="dificultad-badge <?php echo strtolower($this->carrera['dificultad']) ?>">
                            <?php echo $this->carrera['dificultad'] ?>
                        </span>
                        <h1><?php echo $this->carrera['nombre'] ?></h1>
                        <p class="ubicacion"><i class="fas fa-map-marker-alt"></i> <?php echo $this->carrera['ubicacion'] ?></p>
                    </header>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <i class="fas fa-route"></i>
                            <div class="stat-text">
                                <span class="label">Distancia</span>
                                <span class="value"><?php echo $this->carrera['distancia'] ?> Km</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-mountain"></i>
                            <div class="stat-text">
                                <span class="label">Desnivel</span>
                                <span class="value">+<?php echo $this->carrera['desnivel'] ?> m</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="stat-text">
                                <span class="label">Fecha</span>
                                <span class="value"><?php echo date('d/m/Y', strtotime($this->carrera['fecha'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="description">
                        <h3>Sobre la carrera</h3>
                        <p><?php echo nl2br($this->carrera['descripcion']) ?></p>
                    </div>

                    <div class="carrera-specs-minimal">
                        <div class="spec-item">
                            <div class="spec-content">
                            <span class="spec-label">Requisito de Edad: </span>
                            <span class="spec-value">
                                <?= $this->carrera['edad_minima'] ?> – <?= $this->carrera['edad_maxima'] ?> <small>años</small>
                            </span>
                        </div>
                        </div>
                    </div>

                    <?php if (isset($this->usuario) && $this->edad_usuario < $this->carrera['edad_minima']): ?>
                        <div class="alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No cumples con la edad mínima requerida para este evento.
                        </div>
                    <?php endif; ?>

                    <div class="organizador-info">
                        <p>Organizado por: <strong><?php echo $this->carrera['organizador'] ?></strong></p>
                    </div>

                    <div class="disponibilidad-container">
                        <div class="disponibilidad-header">
                            <span><i class="fas fa-users"></i> Plazas disponibles</span>
                            <span class="plazas-cuenta">
                                <strong><?php echo $this->plazas_libres ?></strong> de <?php echo $this->carrera['cupo_maximo'] ?>
                            </span>
                        </div>
                        <div class="progress-bar-container">
                            <?php
                                // Calculamos el porcentaje de plazas ocupadas
                                $porcentaje_ocupado = (($this->carrera['cupo_maximo'] - $this->plazas_libres) / $this->carrera['cupo_maximo']) * 100;
                                // Lógica para decidir la clase
                                $clase_disponibilidad = '';
                                if ($this->plazas_libres <= 0) {
                                    $clase_disponibilidad = 'full';
                                } elseif ($this->plazas_libres <= 10) {
                                    $clase_disponibilidad = 'danger';
                                } elseif ($this->plazas_libres <= 25) {
                                    $clase_disponibilidad = 'warning';
                                }

                            ?>
                            <div class="progress-bar-fill <?php echo $clase_disponibilidad ?>" style="width: <?php echo $porcentaje_ocupado ?>%"></div>
                        </div>

                        <?php if ($this->plazas_libres <= 20 && $this->plazas_libres > 0): ?>
                            <p class="alert-ultimas-plazas">¡Últimas <?php echo $this->plazas_libres ?> plazas disponibles!</p>
                        <?php endif; ?>
                    </div>

                    <section class="carrera-actions">
                        <?php if (isset($_SESSION['role_id'])): ?>
                            <?php if ($this->plazas_libres > 0): ?>
                                <a href="<?php echo URL ?>inscripcion/new/<?php echo $this->carrera['id'] ?>" class="btn-primary">
                                    <i class="fas fa-edit"></i> Inscribirme ahora
                                </a>
                            <?php else: ?>
                                <div class="sold-out-badge-minimal">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>Inscripciones agotadas para este evento</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="login-alert">
                                <p>Para participar en este evento es necesario estar registrado.</p><br>
                                <a href="<?php echo URL ?>auth/login" class="btn-primary">Iniciar Sesión / Registro</a>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $GLOBALS['carrera']['edit'])): ?>
                            <div class="admin-controls">
                                <h4>Gestión de Carrera</h4>
                                <div class="admin-buttons">
                                    <a href="<?php echo URL ?>carrera/edit/<?php echo $this->carrera['id'] ?>" class="btn-secondary">
                                        <i class="fas fa-tools"></i> Editar
                                    </a>
                                    <div class="admin-buttons">
                                        <?php if ($_SESSION['role_id'] < 3): ?>
                                            <a href="<?= URL ?>inscripcion/export/<?= $this->carrera['id'] ?>" class="btn-secondary" title="Exportar CSV para cronometraje">
                                                <i class="fas fa-file-csv"></i> Exportar CSV
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?php echo URL ?>carrera/delete/<?php echo $this->carrera['id'] ?>" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">

                                        <button type="submit" class="btn-account-delete"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar la carrera <?php echo htmlspecialchars($this->carrera['nombre']) ?>?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

            </div>
        </div>
    </main>

    <div id="imageModal" class="modal-lightbox">
        <span class="close-modal">&times;</span>

        <img class="modal-content" id="modalImage">

        <div id="modalCaption"></div>
    </div>

    <footer class="footer">
        <?php require_once "template/partials/footer.partial.php"?>
    </footer>

</body>
</html>
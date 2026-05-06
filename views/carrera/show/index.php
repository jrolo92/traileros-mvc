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
                    <!-- Notificaciones -->
                    <?php require_once "template/partials/mensaje.partial.php" ?>
                    <?php require_once "template/partials/error.partial.php" ?>

                    <!-- Volver atrás -->
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
                        <div class="header-meta">
                            <p class="ubicacion"><i class="fas fa-map-marker-alt"></i> <?php echo $this->carrera['ubicacion'] ?></p>
                            <p class="fecha-header"><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($this->carrera['fecha'])) ?></p>
                        </div>
                    </header>
                    <div class="description"><h3>Modalidades</h3></div>
                    <?php foreach ($this->modalidades as $mod): ?>
                        <h3 style="text-align:center;"><?php echo $mod['nombre'] ?></h3>
                        <div class="stats-grid">
                            
                            <div class="stat-item">
                                <i class="fas fa-route"></i>
                                <div class="stat-text">
                                    <span class="label">Distancia</span>
                                    <span class="value"><?php echo $mod['distancia'] ?> Km</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <i class="fas fa-mountain"></i>
                                <div class="stat-text">
                                    <span class="label">Desnivel</span>
                                    <span class="value">+<?php echo $mod['desnivel'] ?> m</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <i class="fas fa-user-clock"></i>
                                <div class="stat-text">
                                    <span class="label">Edad</span>
                                    <span class="value"><?= $mod['edad_minima'] ?> - <?= $mod['edad_maxima'] ?> <small>años</small></span>
                                </div>
                            </div>

                            <!-- Bloque del Track / Mapa -->
                            <?php if ($mod['track_embed']): ?>
                                <div class="perfil-elevacion-container">
                                    <h4><i class="fas fa-mountain"></i> Perfil de Elevación</h4>

                                    <div class="perfil-wrapper">
                                        <iframe 
                                            src="<?= $mod['track_embed'] ?>" 
                                            scrolling="no">
                                        </iframe>
                                    </div>
                                    
                                    <div class="perfil-footer">
                                        <span class="perfil-info"></span>
                                        <a href="<?= $mod['track_url'] ?>" target="_blank" class="perfil-link">
                                            Ver track completo en Wikiloc <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="disponibilidad-container" style="grid-column: 1 / -1;">
                                <?php 
                                    $libres = $mod['plazas_libres'];
                                    $cupo = $mod['cupo_maximo'];
                                    $porcentaje_ocupado = (($cupo - $libres) / $cupo) * 100;
                                    
                                    $clase_disponibilidad = '';
                                    if ($libres <= 0) $clase_disponibilidad = 'full';
                                    elseif ($libres <= 10) $clase_disponibilidad = 'danger';
                                    elseif ($libres <= 25) $clase_disponibilidad = 'warning';
                                ?>
                                
                                <div class="disponibilidad-header">
                                    <small><i class="fas fa-users"></i> Plazas: <strong><?= $libres ?></strong> de <?= $cupo ?></small>

                                </div>
                                <div class="progress-bar-container" style="height: 8px;">
                                    <div class="progress-bar-fill <?php echo $clase_disponibilidad ?>" style="width: <?php echo $porcentaje_ocupado ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="description">
                        <h3>Sobre la carrera</h3>
                        <p><?php echo nl2br($this->carrera['descripcion']) ?></p>
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

                    <section class="carrera-actions">
                        <?php if ($this->finalizada): ?>
                            <?php if ($this->tiene_resultados): ?>
                                <a href="<?= URL ?>resultado/render/<?= $this->carrera['id'] ?>" class="btn-primary" style="background-color: #27ae60;">
                                    <i class="fas fa-trophy"></i> Ver Clasificaciones Oficiales
                                </a>
                            <?php else: ?>
                                <div class="sold-out-badge-minimal">
                                    <i class="fas fa-clock"></i>
                                    <span>Evento finalizado. Resultados próximamente.</span>
                                </div>
                            <?php endif; ?>
                        <?php elseif($this->fuera_plazo_ins): ?>
                            <div class="sold-out-badge-minimal">
                                <i class="fas fa-calendar-times"></i>
                                <span>El plazo de inscripciones ha finalizado.</span>
                            </div>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <div class="login-alert">
                                <p>Para participar en este evento es necesario estar registrado.</p><br>
                                <a href="<?= URL ?>auth/login" class="btn-primary">Iniciar Sesión / Registro</a>
                            </div>
                        <?php elseif (!$this->hay_plazas): ?>
                            <div class="sold-out-badge-minimal">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>Inscripciones agotadas para este evento</span>
                            </div>

                        <?php else: ?>
                            <a href="<?= URL ?>inscripcion/new/<?= $this->carrera['id'] ?>" class="btn-primary">
                                <i class="fas fa-edit"></i> Inscribirme ahora
                            </a>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $GLOBALS['carrera']['edit'])): ?>
                            <div class="admin-controls">
                                <h4>Gestión de Carrera</h4>
                                <div class="admin-buttons">
                                    <a href="<?php echo URL ?>carrera/edit/<?php echo $this->carrera['id'] ?>" class="btn-secondary">
                                        <i class="fas fa-tools"></i> Editar
                                    </a>

                                    <?php if ($_SESSION['role_id'] < 3): ?>
                                        <a href="<?= URL ?>inscripcion/export/<?= $this->carrera['id'] ?>" class="btn-secondary" title="Exportar CSV">
                                            <i class="fas fa-file-csv"></i> Exportar CSV
                                        </a>
                                        <a href="<?= URL ?>inscripcion/exportPdf/<?= $this->carrera['id'] ?>" class="btn-secondary" title="Exportar PDF">
                                            <i class="fas fa-file-csv"></i> Exportar PDF
                                        </a>
                                    <?php endif; ?>


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
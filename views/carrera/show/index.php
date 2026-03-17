<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title . ": " . $this->carrera['nombre'] ?> </title>
    <script src="<?= URL ?>public/js/modal-image.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'; ?>

    <main class="content-wrapper">
        <div class="container">
            <div class="carrera-detail-grid">
                
                <div class="carrera-media">
                    <img src="<?= URL . 'public/assets/img/carreras/' . $this->carrera['imagen'] ?>" alt="<?= $this->carrera['nombre'] ?>" class="img-fluid detail-img">
                </div>

                <div class="carrera-info-panel">
                    <div class="back-navigation">
                        <a href="<?= $_SERVER['HTTP_REFERER'] ?? URL . 'carrera' ?>" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <header class="detail-header">
                        <span class="dificultad-badge <?= strtolower($this->carrera['dificultad']) ?>">
                            <?= $this->carrera['dificultad'] ?>
                        </span>
                        <h1><?= $this->carrera['nombre'] ?></h1>
                        <p class="ubicacion"><i class="fas fa-map-marker-alt"></i> <?= $this->carrera['ubicacion'] ?></p>
                    </header>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <i class="fas fa-route"></i>
                            <div class="stat-text">
                                <span class="label">Distancia</span>
                                <span class="value"><?= $this->carrera['distancia'] ?> Km</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-mountain"></i>
                            <div class="stat-text">
                                <span class="label">Desnivel</span>
                                <span class="value">+<?= $this->carrera['desnivel'] ?> m</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="stat-text">
                                <span class="label">Fecha</span>
                                <span class="value"><?= date('d/m/Y', strtotime($this->carrera['fecha'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="description">
                        <h3>Sobre la carrera</h3>
                        <p><?= nl2br($this->carrera['descripcion']) ?></p>
                    </div>

                    <div class="organizador-info">
                        <p>Organizado por: <strong><?= $this->carrera['organizador'] ?></strong></p>
                    </div>

                    <section class="carrera-actions">
                        <?php if(isset($_SESSION['role_id'])): ?>
                            <a href="<?= URL ?>inscripcion/form/<?= $this->carrera['id'] ?>" class="btn-primary">
                                <i class="fas fa-edit"></i> Inscribirme ahora
                            </a>
                        <?php else: ?>
                            <div class="login-alert">
                                <p>Para participar en este evento es necesario estar registrado.</p><br>
                                <a href="<?= URL ?>login" class="btn-primary">Iniciar Sesión / Registro</a>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $GLOBALS['carrera']['edit'])): ?>
                            <div class="admin-controls">
                                <h4>Gestión de Carrera</h4>
                                <div class="admin-buttons">
                                    <a href="<?= URL ?>carrera/edit/<?= $this->carrera['id'] ?>" class="btn-secondary">
                                        <i class="fas fa-tools"></i> Editar
                                    </a>
                                    <form method="POST" action="<?= URL ?>carrera/delete/<?= $this->carrera['id'] ?>" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        
                                        <button type="submit" class="btn-account-delete" 
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar la carrera <?= htmlspecialchars($this->carrera['nombre']) ?>?')">
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
        <?php require_once("template/partials/footer.partial.php") ?> 
    </footer>

</body>
</html>
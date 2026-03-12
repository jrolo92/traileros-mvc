<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?> </title>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'; ?>

    <main class="content-wrapper">
        <div class="container">
            <div class="carrera-detail-grid">
                
                <div class="carrera-media">
                    <img src="<?= URL . $this->carrera['imagen_url'] ?>" alt="<?= $this->carrera['nombre'] ?>" class="img-fluid detail-img">
                </div>

                <div class="carrera-info-panel">
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
                            <a href="<?= URL ?>inscripcion/form/<?= $this->carrera['id'] ?>" class="btn-action btn-enroll">
                                <i class="fas fa-edit"></i> Inscribirme ahora
                            </a>
                        <?php else: ?>
                            <div class="login-alert">
                                <p>Para participar en este evento es necesario estar registrado.</p>
                                <a href="<?= URL ?>login" class="btn-outline">Iniciar Sesión / Registro</a>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $GLOBALS['carrera']['edit'])): ?>
                            <div class="admin-controls">
                                <h4>Gestión de Carrera</h4>
                                <div class="admin-buttons">
                                    <a href="<?= URL ?>carrera/edit/<?= $this->carrera['id'] ?>" class="btn-action btn-edit">
                                        <i class="fas fa-tools"></i> Editar
                                    </a>
                                    <button onclick="confirmDelete(<?= $this->carrera['id'] ?>)" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

            </div>
        </div>
    </main>

    <?php require_once("template/partials/footer.partial.php") ?>
    <?php require_once("template/layouts/javascript.layout.php") ?>

</body>
</html>
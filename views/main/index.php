<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
    <script src="<?= URL ?>public/js/close-toast.js" defer></script>    
</head>
<body>

    <main>

        <?php require_once 'template/partials/header.partial.php'; ?>
       
        <!-- Comprobar notificaciones y mostrarlas en un toast -->
        <?php if (isset($_SESSION['notify']) || isset($_SESSION['error'])): ?>
            <div id="toast-container" class="toast-container">
                <?php if (isset($_SESSION['notify'])): ?>
                    <div class="toast success">
                        <i class="bi bi-check-circle"></i>
                        <span><?= $_SESSION['notify'] ?></span>
                    </div>
                    <?php unset($_SESSION['notify']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="toast error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span><?= $_SESSION['error'] ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <section class="hero">
            <div class="hero-content">
                <h1>DESAFÍA TUS LÍMITES</h1>
                <p>La plataforma definitiva para amantes del Trail Running</p>
                <a href="<?php echo URL ?>carrera/render" class="btn-primary">Explorar Carreras</a>
            </div>
        </section>

        <section id="carreras" class="carreras-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Próximos Eventos</h2>
                    <!-- <p>No te pierdas las competiciones más exigentes de la temporada</p> -->
                    <p>Siente la montaña bajo tus pies</p>
                </div>

                <div class="grid-carreras">
                    <?php foreach ($this->carreras as $carrera): ?>
                        <article class="carrera-card" onclick="window.location='<?php echo URL ?>carrera/show/<?php echo $carrera['id'] ?>'">
                            <div class="card-image" style="background-image: url('<?php echo URL . 'public/assets/img/carreras/' . ($carrera['imagen'] ?? 'public/img/default.jpg') ?>');">
                                <span class="badge <?php echo ($carrera['distancia'] > 42) ? 'ultra' : 'trail' ?>">
                                    <?php echo ($carrera['distancia'] > 42) ? 'Ultra' : 'Trail' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <span class="card-date"><?php echo date('d M, Y', strtotime($carrera['fecha'])) ?></span>
                                <h3><?php echo $carrera['nombre'] ?></h3>
                                <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo $carrera['ubicacion'] ?></p>

                                <div class="stats">
                                    <span><i class="fas fa-route"></i> <?php echo $carrera['distancia'] ?>km</span>
                                    <span><i class="fas fa-mountain"></i> +<?php echo $carrera['desnivel'] ?? '0' ?>m</span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>

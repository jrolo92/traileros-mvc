<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
</head>
<body>

    <main>

        <?php require_once 'template/partials/header.partial.php'; ?>

        <!-- Alert primera vez inicio sesión -->
        <?php if (isset($_SESSION['profile_incomplete'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>¡Hola <?= $_SESSION['user_name'] ?>!</strong> Para poder inscribirte en carreras, necesitamos que completes tu perfil deportivo.
                <a href="<?= URL ?>user/edit" class="btn btn-sm btn-outline-dark ms-3">Completar ahora</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['profile_incomplete']); // Para que no salga cada vez que refresque ?>
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

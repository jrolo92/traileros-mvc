<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
</head>
<body>

    <main>

        <?php require_once 'template/partials/header.partial.php'; ?>

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
                    <p>No te pierdas las competiciones más exigentes de la temporada</p>
                </div>

                <div class="grid-carreras">
                    <?php foreach ($this->carreras as $carrera): ?>
                        <article class="carrera-card" onclick="window.location='<?php echo URL ?>carrera/show/<?php echo $carrera['id'] ?>'">
                            <div class="card-image" style="background-image: url('<?php echo URL . ($carrera['imagenUrl'] ?? 'public/img/default.jpg') ?>');">
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

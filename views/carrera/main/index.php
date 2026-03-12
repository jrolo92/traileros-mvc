<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?> </title>
</head>
<body> 

    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <?php require_once 'template/partials/header.partial.php'; ?>
    <section class="carreras-container">
        <div class="toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar carrera..." id="searchInput">
            </div>
            <div class="filter-box">
                <select id="orderSelect">
                    <option value="defecto">Ordenar por</option>
                    <option value="recientes">Recientes</option>
                    <option value="az">A - Z</option>
                </select>
            </div>
        </div>

        <div class="carreras-grid">
            <?php foreach ($this->carreras as $carrera): ?>
                <article class="carrera-card" onclick="window.location='<?= URL ?>carrera/show/<?= $carrera['id'] ?>'">
                    <div class="card-horizontal">
                        <div class="card-img-container" 
                            style="background-image: url('<?= URL . $carrera['imagenUrl'] ?>');">
                            <span class="dificultad-tag <?= strtolower($carrera['dificultad']) ?>">
                                <?= $carrera['dificultad'] ?>
                            </span>
                        </div>

                        <div class="card-info">
                            <h3><?= $carrera['nombre'] ?></h3>
                            <p class="ubicacion"><i class="fas fa-map-marker-alt"></i> <?= $carrera['ubicacion'] ?></p>
                            
                            <div class="tech-specs">
                                <div class="spec">
                                    <i class="fas fa-route"></i>
                                    <span><?= $carrera['distancia'] ?> km</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-mountain"></i>
                                    <span>+<?= $carrera['desnivel'] ?> m</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?= date('d/m/Y', strtotime($carrera['fecha'])) ?></span>
                                </div>
                            </div>
                            
                            <p class="organizador">Organiza: <strong><?= $carrera['organizador'] ?></strong></p>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>


        <?php
            // 1. Comprobamos si hay alguien logueado
            // 2. Comprobamos si su rol está en la lista de permitidos para "new"
            if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $GLOBALS['carrera']['new'])): 
            ?>
                <a href="<?= URL ?>carrera/new" class="btn-float-add">
                    <i class="fas fa-plus"></i> Añadir carrera
                </a>
        <?php endif; ?>
    </section>
    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
    </body>
</html>
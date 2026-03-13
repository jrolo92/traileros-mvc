<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
    <script src="<?= URL ?>public/js/menu-order.js" defer></script>
</head>
<body>

    <?php require_once 'template/partials/header.partial.php'; ?>
    <section class="carreras-container">
        <div class="carreras-toolbar">
            <form class="carreras-search" action="<?= URL ?>carrera/search" method="GET">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input 
                        type="search" 
                        name="term" 
                        placeholder="Buscar carrera..." 
                        value="<?= htmlspecialchars($this->term ?? '') ?>"
                        autocomplete="off"
                    >
                </div>
            </form>

            <div class="carreras-dropdown" id="orderDropdown">
                <button type="button" class="dropdown-button" id="dropdownBtn">
                    <span>Ordenar por</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <ul class="dropdown-list">
                    <li><a href="<?= URL ?>carrera/order/2">Nombre (A-Z)</a></li>
                    <li><a href="<?= URL ?>carrera/order/3">Ciudad</a></li>
                    <li><a href="<?= URL ?>carrera/order/4">Distancia</a></li>
                    <li><a href="<?= URL ?>carrera/order/5">Fecha</a></li>
                </ul>
            </div>
        </div>

        <div class="carreras-grid">
            <?php foreach ($this->carreras as $carrera): ?>
                <article class="carrera-card" onclick="window.location='<?php echo URL ?>carrera/show/<?php echo $carrera['id'] ?>'">
                    <div class="card-horizontal">
                        <div class="card-img-container"
                            style="background-image: url('<?php echo URL . $carrera['imagenUrl'] ?>');">
                            <span class="dificultad-tag <?php echo strtolower($carrera['dificultad']) ?>">
                                <?php echo $carrera['dificultad'] ?>
                            </span>
                        </div>

                        <div class="card-info">
                            <h3><?php echo $carrera['nombre'] ?></h3>
                            <p class="ubicacion"><i class="fas fa-map-marker-alt"></i> <?php echo $carrera['ubicacion'] ?></p>

                            <div class="tech-specs">
                                <div class="spec">
                                    <i class="fas fa-route"></i>
                                    <span><?php echo $carrera['distancia'] ?> km</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-mountain"></i>
                                    <span>+<?php echo $carrera['desnivel'] ?> m</span>
                                </div>
                                <div class="spec">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo date('d/m/Y', strtotime($carrera['fecha'])) ?></span>
                                </div>
                            </div>

                            <p class="organizador">Organiza: <strong><?php echo $carrera['organizador'] ?></strong></p>
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
                <a href="<?php echo URL ?>carrera/new" class="btn-float-add">
                    <i class="fas fa-plus"></i> Añadir carrera
                </a>
        <?php endif; ?>
    </section>
    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
    </body>
</html>

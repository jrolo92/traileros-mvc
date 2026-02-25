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

    <?php if (in_array($_SESSION['role_id'], $GLOBALS['carrera']['new'])): ?>
        <a href="<?= URL ?>carrera/new" class="btn-float-add">
            <i class="fas fa-plus"></i> Añadir carrera
        </a>
    <?php endif; ?>
</section>
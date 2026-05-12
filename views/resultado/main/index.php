<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/search-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>
    <!-- <?php var_dump($this->rol) ?> -->

    <main class="account-container">
        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <div class="avatar-circle size-md" style="background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trophy" style="font-size: 2rem;"></i>
                    </div>
                    
                    <div>
                        <h2><?php echo $this->title ?></h2>
                        <p>Clasificación oficial de la carrera</p>
                    </div>
                </div>

                <div class="back-navigation">
                        <a href="<?= $this->back_url ?>" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                </div>
            </header>

            <section class="account-main-content">
                <?php require_once "template/partials/mensaje.partial.php" ?>
                <?php require_once "template/partials/error.partial.php" ?>

                <div class="account-content">
                    
                    <div class="carreras-toolbar">
                        <div id="subtitle-container">
                            <div class="subtitle-badge">
                                <i class="fas fa-calendar-alt"></i> <?= $this->title ?>
                            </div>
                            <div class="subtitle-badge">
                                <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($this->carrera['fecha'])) ?>
                            </div>
                        </div>

                        <form class="carreras-search" action="<?= URL ?>resultado/search/<?= $this->carrera['id'] ?>" method="GET">
                                <div class="search-wrapper">
                                    <i class="fas fa-search"></i>
                                    <input
                                        type="search"
                                        id="search" 
                                        name="term"
                                        placeholder="Buscar carrera..."
                                        value="<?php echo htmlspecialchars($this->term ?? '') ?>"
                                        autocomplete="off"
                                    >
                                </div>
                        </form>
                    </div>

                    <?php if (empty($this->resultados)): ?>
                        <div class="aviso-vacio" style="text-align: center; padding: 40px;">
                            <i class="fas fa-stopwatch" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            <p>Los resultados aún no han sido publicados para esta carrera.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/id">Id</th>
                                        <th class="text-center"><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/posicion_general">Pos. General</th>
                                        <th><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/modalidad">Modalidad</th>
                                        <th class="text-center"><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/posicion_categoria">Pos. Categoría</th>  
                                        <th><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/categoria">Categoría</th>                                  
                                        <th class="text-center"><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/dorsal">Dorsal</th>
                                        <th><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/nombre">Corredor</th>                          
                                        <th><a href="<?= URL ?>resultado/order/<?= $this->carrera['id'] ?>/tiempo">Tiempo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->resultados as $r): ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="club-text"><?= htmlspecialchars($r['id']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <strong style="font-size: 1.1rem;">
                                                    <?= ($r['estado'] == 'FINISHER') ? $r['posicion_general'] . 'º' : '-' ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <span class="club-text"><?= htmlspecialchars($r['modalidad']) ?></span>
                                            </td>

                                            <td class="text-center">
                                                <strong style="font-size: 1.1rem;">
                                                    <?= ($r['estado'] == 'FINISHER') ? $r['posicion_categoria'] . 'º' : '-' ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <span class="club-text"><?= htmlspecialchars($r['categoria']) ?></span>
                                            </td>
                                            
                                            <td class="text-center">
                                                <span class="role-badge" style="background: #e9ecef; color: #495057;">
                                                    #<?= $r['dorsal'] ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <strong><?= htmlspecialchars($r['nombre']) ?></strong>
                                            </td>

                                            <td>
                                                <span style="font-family: monospace; font-weight: bold; font-size: 1rem;">
                                                    <?= ($r['estado'] == 'FINISHER') ? $r['tiempo'] : '--:--:--' ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php
                                                    // Lógica de colores según el estado
                                                    $clase_badge = 'editor'; 
                                                    if ($r['estado'] == 'FINISHER') $clase_badge = 'admin'; 
                                                    if ($r['estado'] == 'DNF' || $r['estado'] == 'DSQ') $clase_badge = 'delete-btn';
                                                ?>
                                                <span class="role-badge <?= $clase_badge ?>" style="padding: 4px 8px; border-radius: 4px;">
                                                    <?= $r['estado'] ?>
                                                </span>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <footer class="admin-table-footer" style="display: flex; justify-content: space-between; align-items: center;">
                    <p>Corredores en meta: <strong><?= count($this->resultados) ?></strong></p>
                    
                    <a href="<?= URL ?>resultado/exportPdf/<?= $this->carrera['id'] ?>" class="btn-primary" style="text-decoration: none;">
                        <i class="fas fa-file-pdf"></i> Exportar Clasificación
                    </a>
                </footer>
            </section>
        </div>
    </main>

    <footer class="footer">
            <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>
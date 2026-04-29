<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/menu-order.js" defer></script>
    <script src="<?php echo URL ?>public/js/search-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>

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
                        <a href="javascript:window.history.back();" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                </div>
            </header>

            <section class="account-main-content">
                <?php require_once "template/partials/mensaje.partial.php" ?>
                <?php require_once "template/partials/error.partial.php" ?>

                <div class="account-content">
                    
                    <div id="subtitle-container">
                        <div class="subtitle-badge">
                            <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($this->carrera['fecha'])) ?>
                        </div>
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
                                        <th class="text-center">Pos.</th>
                                        <th class="text-center">Dorsal</th>
                                        <th>Corredor</th>
                                        <th>Modalidad</th>
                                        <th>Tiempo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->resultados as $r): ?>
                                        <tr>
                                            <td class="text-center">
                                                <strong style="font-size: 1.1rem;">
                                                    <?= ($r['estado'] == 'FINISHER') ? $r['posicion_general'] . 'º' : '-' ?>
                                                </strong>
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
                                                <span class="club-text"><?= htmlspecialchars($r['modalidad']) ?></span>
                                            </td>

                                            <td>
                                                <span style="font-family: monospace; font-weight: bold; font-size: 1rem;">
                                                    <?= ($r['estado'] == 'FINISHER') ? $r['tiempo'] : '--:--:--' ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php
                                                    // Lógica de colores según el estado
                                                    $clase_badge = 'editor'; // Por defecto (gris/azul)
                                                    if ($r['estado'] == 'FINISHER') $clase_badge = 'admin'; // Verde
                                                    if ($r['estado'] == 'DNF' || $r['estado'] == 'DSQ') $clase_badge = 'delete-btn'; // Rojo/Naranja
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
                    
                    <a href="<?= URL ?>resultado/pdf/<?= $this->carrera['id'] ?>" class="btn-primary" style="text-decoration: none;">
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
<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/search-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <div class="avatar-circle size-md">
                        <?php if (!empty($_SESSION['user_avatar']) && file_exists($_SESSION['user_avatar'])): ?>
                            <img src="<?= URL . $_SESSION['user_avatar'] ?>" alt="Perfil">
                        <?php else: ?>
                            <i class="fas fa-user-circle" style="font-size: 4rem;"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h2><?php echo $this->title ?></h2>
                        <p>Gestiona tus datos personales y deportivos</p>
                    </div>
                </div>

                <nav class="account-menu">
                    <?php require_once "views/account/partials/menu.partial.php"?>
                </nav>
                
            </header>

            <section class="account-main-content">
                <?php require_once "template/partials/mensaje.partial.php" ?>
                <?php require_once "template/partials/error.partial.php" ?>

                <section class="carreras-container">
                    <div class="carreras-toolbar">
                        <div id="subtitle-container">
                            <div class="subtitle-badge">
                                <i class="fas fa-calendar-alt"></i> <?= $this->title ?>
                            </div>
                            
                        </div>

                        <form class="carreras-search" action="<?php echo URL ?>inscripcion/search" method="GET">
                            <div class="search-wrapper">
                                <i class="fas fa-search"></i>
                                <input
                                    type="search"
                                    id="search" 
                                    name="term"
                                    placeholder="Buscar inscripción..."
                                    value="<?php echo htmlspecialchars($this->term ?? '') ?>"
                                    autocomplete="off"
                                >
                            </div>
                        </form>
                    </div>

                <div class="account-content">
                    
                    <div id="subtitle-container">
                        <?php if(isset($this->subtitle)): ?>
                            <div class="subtitle-badge"><?= htmlspecialchars($this->subtitle) ?></div>
                        <?php endif ?>
                    </div>
                    <?php if (empty($this->inscripciones)): ?>
                        <div class="aviso-vacio" style="text-align: center; padding: 40px;">
                            <i class="fas fa-info-circle" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            <p>No se han encontrado registros de inscripciones.</p>
                            <a href="<?= URL ?>carrera" class="btn-primary" style="display: inline-block; margin-top: 15px; text-decoration: none; padding: 10px 20px; border-radius: 5px;">
                                Ver carreras disponibles
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th><a href="<?php echo URL ?>inscripcion/order/id">Id</th>
                                        <?php if ($_SESSION['role_id'] < 3): ?>
                                            <th><a href="<?php echo URL ?>inscripcion/order/usuario">Participante</th>
                                        <?php endif; ?>
                                        <th><a href="<?php echo URL ?>inscripcion/order/evento">Evento / Carrera</th>
                                        <th class="text-center"><a href="<?php echo URL ?>inscripcion/order/dorsal">Dorsal</th>
                                        <th><a href="<?php echo URL ?>inscripcion/order/fecha">Fecha Evento</th>
                                        <th><a href="<?php echo URL ?>inscripcion/order/estado">Estado</th>
                                        <th class="text-right">Importe</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->inscripciones as $i): ?>
                                        <tr>
                                            <td data-label="Id:"><?= $i['id'] ?></td>
                                            <?php if ($_SESSION['role_id'] < 3): ?>
                                                <td data-label="Usuario:">
                                                    <strong><?= htmlspecialchars($i['usuario_nombre']) ?></strong>
                                                </td>
                                            <?php endif; ?>

                                            <td data-label="Carrera:">
                                                <span class="club-text" style="font-weight: 600;"><?= htmlspecialchars($i['evento_nombre']) ?></span>
                                            </td>

                                            <td data-label="Dorsal:" class="text-center">
                                                <span class="role-badge" style="background: #e9ecef; color: #495057;">
                                                    #<?= $i['dorsal'] ?>
                                                </span>
                                            </td>

                                            <td data-label="Fecha:" class="text-nowrap"><?= date('d/m/Y', strtotime($i['evento_fecha'])) ?></td>

                                            <td data-label="Pago:">
                                                <span class="role-badge <?= ($i['estado_pago'] == 'completado') ? 'admin' : 'editor' ?>">
                                                    <?= strtoupper($i['estado_pago']) ?>
                                                </span>
                                            </td>

                                            <td data-label="Precio:" class="text-right"><strong><?= number_format($i['precio_final'], 2) ?>€</strong></td>

                                            <td class="actions-column">
                                                <div class="admin-actions">
                                                    <a href="<?= URL ?>inscripcion/show/<?= $i['user_id'] ?>/<?= $i['evento_id'] ?>" 
                                                       class="action-btn view-btn" 
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= URL ?>resultado/render/<?= $i['evento_id'] ?>" 
                                                        class="action-btn result-btn" 
                                                        title="Ver Clasificación">
                                                        <i class="fas fa-trophy"></i>
                                                    </a>
                                                    <?php if (in_array($_SESSION['role_id'], $GLOBALS['inscripcion']['edit'])): ?>
                                                        <a href="<?= URL ?>inscripcion/edit/<?= $i['user_id'] ?>/<?= $i['evento_id'] ?>" 
                                                        class="action-btn edit-btn <?= !in_array($_SESSION['role_id'], $GLOBALS['inscripcion']['edit']) ? 'disabled' : '' ?>"
                                                        title="Editar">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (in_array($_SESSION['role_id'], $GLOBALS['inscripcion']['cancel'])): ?>
                                                        <form method="POST" action="<?= URL ?>inscripcion/cancel/<?= $i['id'] ?>" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <button type="submit" class="action-btn delete-btn" 
                                                                    onclick="return confirm('¿Desea cancelar esta inscripción?')"
                                                                    title="Cancelar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <footer class="admin-table-footer">
                    <p>Total de inscripciones localizadas: <strong><?= count($this->inscripciones) ?></strong></p>
                </footer>
            </section>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>

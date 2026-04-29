<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/menu-order.js" defer></script>
    <script src="<?php echo URL ?>public/js/search-ges-eventos.js" defer></script>
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
                        <form class="carreras-search" action="<?php echo URL ?>carrera/search_gestion" method="GET">
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

                        <div class="carreras-dropdown" id="orderDropdown">
                            <button type="button" class="dropdown-button" id="dropdownBtn">
                                <span>Ordenar por</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>

                            <ul class="dropdown-list">
                                <li><a href="<?php echo URL ?>carrera/order_gestion/1">Id</a></li>
                                <li><a href="<?php echo URL ?>carrera/order_gestion/2">Nombre</a></li>
                                <li><a href="<?php echo URL ?>carrera/order_gestion/7">Fecha</a></li>
                                <li><a href="<?php echo URL ?>carrera/order_gestion/8">Estado</a></li>
                            </ul>
                        </div>
                    </div>

                <div class="account-content">
                    <?php if (empty($this->eventos)): ?>
                        <div class="aviso-vacio" style="text-align: center; padding: 40px;">
                            <i class="fas fa-calendar-times" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            <p>No tienes carreras asignadas para gestionar.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Nombre del Evento</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-end">Nº Inscritos</th>
                                        <th class="text-center">Acciones</th>
                                        <th class="text-center">Exportar Inscritos</th>
                                        <th class="text-center">Importar resultados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->eventos as $e): ?>
                                        <tr>
                                            <td><?= $e['id'] ?></td>
                                            <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
                                            <td class="text-nowrap"><?= date('d/m/Y', strtotime($e['fecha'])) ?></td>
                                            <td class="text-center">
                                                <span class="role-badge <?= strtolower($e['estado']) ?>">
                                                    <?= $e['estado'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= URL ?>inscripcion/participantes/<?= $e['id'] ?>" class="table-link" title="Ver listado de inscritos">
                                                    <strong><?= $e['total_inscritos']?></strong>
                                                </a>
                                            </td>
                                            
                                            <td class="action-columns">
                                                <div class="admin-actions" style="justify-content: center;">

                                                    <a href="<?= URL ?>carrera/show/<?= $e['id'] ?>" class="action-btn view-btn" title="Ver Carrera">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= URL ?>carrera/edit/<?= $e['id'] ?>" class="action-btn edit-btn" title="Editar Carrera">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                
                                                    <a href="<?= URL ?>resultado/render/<?= $e['id'] ?>" class="action-btn result-btn" title="Ver Clasificación">
                                                        <i class="fas fa-trophy"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="admin-actions" style="justify-content: center;">
                                                    <a href="<?= URL ?>inscripcion/export/<?= $this->carrera['id'] ?>" class="action-btn view-btn" title="Exportar CSV">
                                                        <i class="fas fa-file-csv"></i>
                                                    </a>
                                                    <a href="<?= URL ?>inscripcion/exportPdf/<?= $this->carrera['id'] ?>" class="action-btn edit-btn" title="Exportar PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="admin-actions" style="justify-content: center;">
                                                    <button type="button" class="action-btn"
                                                            onclick="document.getElementById('csv_<?= $e['id'] ?>').click()" title="Importar Tiempos CSV">
                                                        <i class="fas fa-file-upload"></i>
                                                    </button>
                                                    
                                                    <form id="form_csv_<?= $e['id'] ?>" action="<?= URL ?>resultado/pre_import/<?= $e['id'] ?>" 
                                                            method="POST" enctype="multipart/form-data" style="display:none;">
                                                        <input type="file" id="csv_<?= $e['id'] ?>" name="csv_file" accept=".csv" 
                                                            onchange="document.getElementById('form_csv_<?= $e['id'] ?>').submit()">
                                                    </form>
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
                    <p>Total de inscripciones localizadas: <strong><?= count($this->eventos) ?></strong></p>
                </footer>
            </section>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>

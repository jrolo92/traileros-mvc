<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
    <script src="<?= URL ?>public/js/search-user.js" defer></script>
    <script src="<?= URL ?>public/js/menu-order.js" defer></script>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php' ?>

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
                        <p>Panel de administración de corredores y privilegios</p>
                    </div>
                </div>

                <nav class="account-menu">
                    <?php require_once "views/account/partials/menu.partial.php"?>
                </nav>
                
            </header>

            <section class="account-main-content">
                <?php require_once("template/partials/mensaje.partial.php") ?>
                <?php require_once("template/partials/error.partial.php") ?>

                <?php if ($this->peticiones->rowCount() > 0): ?>
                    <div class="requests-alert-box" style="background: #fff3cd; border: 1px solid #ffeeba; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                        <h3 style="color: #856404; margin-bottom: 1rem;">
                            <i class="fas fa-exclamation-circle"></i> Solicitudes de Organizador pendientes
                        </h3>
                        <div class="table-responsive">
                            <table class="admin-table" style="background: white;">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Fecha Solicitud</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($req = $this->peticiones->fetch()): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($req['name'] . ' ' . $req['apellidos']) ?></strong></td>
                                            <td><?= htmlspecialchars($req['email']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                                            <td class="text-center">
                                                <a href="<?= URL ?>user/approve_role/<?= $req['id'] ?>" class="btn-account-save" style="background: #28a745; padding: 5px 10px; font-size: 0.8rem;">Aprobar</a>
                                                <a href="<?= URL ?>user/deny_role/<?= $req['id'] ?>" class="btn-secondary" style="background: #dc3545; padding: 5px 10px; font-size: 0.8rem; color: white;">Denegar</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <section class="carreras-container">
                    <div class="carreras-toolbar">
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
                        <div class="carreras-dropdown" id="orderDropdown">
                            <button type="button" class="dropdown-button" id="dropdownBtn">
                                <span>Ordenar por</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>

                            <ul class="dropdown-list">
                                <li><a href="<?= URL ?>user/order/1">ID</a></li>
                                <li><a href="<?= URL ?>user/order/2">Nombre</a></li>
                                <li><a href="<?= URL ?>user/order/3">Email</a></li>
                                <li><a href="<?= URL ?>user/order/4">Rol</a></li>
                            </ul>
                        </div>
                    </div>
                </section>

                <div class="account-content">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Email</th>
                                    <th>Club</th>
                                    <th>Fed.</th>
                                    <th>Rol</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $this->users->fetch()): ?>
                                    <tr>
                                        <td class="id-column">#<?= $user['id'] ?></td>
                                        
                                        <td>
                                            <strong><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']) ?></strong>
                                        </td>
                                        
                                        <td class="text-nowrap"><?= htmlspecialchars($user['dni'] ?? '-') ?></td>
                                        
                                        <td><small><?= htmlspecialchars($user['email']) ?></small></td>
                                        
                                        <td>
                                            <span class="club-text"><?= htmlspecialchars($user['club'] ?: 'Independiente') ?></span>
                                        </td>

                                        <td class="text-center">
                                            <?= $user['es_federado'] ? '<i class="fas fa-check-circle" title="Federado"></i>' : '<i class="fas fa-times-circle" title="No federado"></i>' ?>
                                        </td>

                                        <td>
                                            <span class="role-badge <?= strtolower($user['rol']) ?>">
                                                <?= $user['rol'] ?>
                                            </span>
                                        </td>

                                        <td class="actions-column">
                                            <div class="admin-actions">
                                                <a href="<?= URL ?>user/show/<?= $user['id'] ?>" 
                                                   class="action-btn view-btn <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['show']) ? 'disabled' : '' ?>"
                                                   title="Ver detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= URL ?>user/edit/<?= $user['id'] ?>" 
                                                   class="action-btn edit-btn <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['edit']) ? 'disabled' : '' ?>"
                                                   title="Editar">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form method="POST" action="<?= URL ?>user/delete/<?= $user['id'] ?>" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <button type="submit" class="action-btn delete-btn <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['delete']) ? 'disabled' : '' ?>" 
                                                            onclick="return confirm('¿Eliminar definitivamente a este usuario?')"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <footer class="admin-table-footer">
                    <p>Total de traileros registrados: <strong><?= $this->users->rowCount() ?></strong></p>
                </footer>

                <div class="back-navigation">
                <a href="<?= URL . 'account' ?>" class="btn-secondary" style="margin-left: 40px;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver</span>
                </a>
                <a class="btn-account-save" href="<?= URL ?>user/new">
                    <i class="fas fa-plus-circle"></i>Nuevo Usuario
                </a>
                </div>
                <div>
                    
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
</body>
</html>
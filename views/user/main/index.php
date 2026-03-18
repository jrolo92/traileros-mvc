<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php' ?>

    <main class="account-container">
        <div class="account-card">
            
            <header class="account-header">
                <div class="user-info-main">
                    <i class="fas fa-users-cog"></i>
                    <div>
                        <h2>Gestión de Usuarios</h2>
                        <p>Panel de administración de corredores y privilegios</p>
                    </div>
                </div>
                <nav class="admin-sub-menu">
                    <?php require_once("views/user/partials/menu.user.partial.php") ?>
                </nav>
            </header>

            <section class="account-main-content">
                <?php require_once("template/partials/mensaje.partial.php") ?>
                <?php require_once("template/partials/error.partial.php") ?>

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
                                            <?= $user['es_federado'] ? '<i class="fas fa-check-circle text-success" title="Federado"></i>' : '<i class="fas fa-times-circle text-muted" title="No federado"></i>' ?>
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
            </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
</body>
</html>
<nav class="admin-nav-bar">
    <div class="nav-actions-left">
        <a href="<?= URL ?>user" class="nav-brand-link">
            <i class="fas fa-list"></i> <span>Lista Usuarios</span>
        </a>
        
        <a href="<?= URL ?>user/new" class="nav-action-link">
            <i class="fas fa-plus-circle"></i> Nuevo Usuario
        </a>

        <div class="nav-dropdown">
            <button class="nav-dropbtn <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['order']) ? 'disabled' : '' ?>">
                <i class="fas fa-sort-amount-down"></i> Ordenar por <i class="fas fa-chevron-down"></i>
            </button>
            <div class="nav-dropdown-content">
                <a href="<?= URL ?>user/order/1">ID</a>
                <a href="<?= URL ?>user/order/2">Nombre</a>
                <a href="<?= URL ?>user/order/3">Email</a>
                <a href="<?= URL ?>user/order/4">Rol</a>
            </div>
        </div>
    </div>

    <form class="nav-search-form" method="GET" action="<?= URL ?>user/search">
        <div class="search-wrapper">
            <input type="search" placeholder="Buscar trailero..." name="term" autocomplete="off">
            <button type="submit" <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['search']) ? 'disabled' : '' ?>>
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</nav>
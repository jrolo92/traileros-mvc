<ul class="account-nav-list">
    <li>
        <a href="<?= URL ?>user" class="<?= ($this->title == 'Panel de Usuarios') ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Lista Usuarios
        </a>
    </li>

    <li>
        <a href="<?= URL ?>user/new">
            <i class="fas fa-plus-circle"></i> Nuevo Usuario
        </a>
    </li>

    <li class="nav-item-dropdown">
        <div class="dropdown-wrapper">
            <button class="dropdown-trigger <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['order']) ? 'disabled' : '' ?>">
                <i class="fas fa-sort-amount-down"></i> Ordenar por...
            </button>
            <ul class="dropdown-menu-list">
                <li><a href="<?= URL ?>user/order/1">ID</a></li>
                <li><a href="<?= URL ?>user/order/2">Nombre</a></li>
                <li><a href="<?= URL ?>user/order/3">Email</a></li>
                <li><a href="<?= URL ?>user/order/4">Rol</a></li>
            </ul>
        </div>
    </li>

    <li class="nav-item-search">
        <form class="nav-search-form" method="GET" action="<?= URL ?>user/search">
            <div class="search-wrapper">
                <input type="search" placeholder="Buscar trailero..." name="term" autocomplete="off">
                <button type="submit" <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['search']) ? 'disabled' : '' ?>>
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </li>
</ul>
<nav class="navbar navbar-expand-lg bg-body-tertiary primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= URL ?>user">Usuarios</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?= URL ?>user/new">Nuevo</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                    <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['order']) ? 'disabled' : 'active' ?>"
                     href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Ordenar por
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= URL ?>user/order/1">Id</a></li>
                        <li><a class="dropdown-item" href="<?= URL ?>user/order/2">Nombre</a></li>
                        <li><a class="dropdown-item" href="<?= URL ?>user/order/3">Email</a></li>
                        <li><a class="dropdown-item" href="<?= URL ?>user/order/4">Rol</a></li>
                       
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
               
            </ul>
            <form class="d-flex" method="GET" action="<?=  URL ?>user/search">
                <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search" name="term">
                <button class="btn btn-outline-secondary
                <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['search']) ? 'disabled' : null ?>" type="submit">Buscar</button>
            </form>
        </div>
    </div>
</nav>
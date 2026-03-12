<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link " href="<?= URL ?>account">Mostrar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="<?= URL ?>account/edit">Editar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="<?= URL ?>account/password">Cambiar Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="<?= URL ?>account/delete/<?= $_SESSION['csrf_token'] ?>" onclick="return confirm('Confimar eliminación de su cuenta')">Eliminar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
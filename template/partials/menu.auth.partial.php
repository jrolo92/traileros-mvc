<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= URL ?>index">Traileros</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= URL ?>index">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="<?= URL ?>carrera">Carreras</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active
          <?= !in_array($_SESSION['role_id'], $GLOBALS['user']['render']) ? 'hidden' : null ?>"
           href="<?= URL ?>/user">Usuarios</a>
        </li>
      </ul>
      <div class="d-flex">
        <div class="collapse navbar-collapse" id="exCollapsingNavbar">
          <ul class="nav navbar-nav flex-row justify-content-between ml-auto">
            <li class="nav-item"><a href="<?= URL ?>account" class="nav-link active"><?= $_SESSION['user_name'] . ' |' ?></a></li>
            <li class="nav-item"><a href="<?= URL ?>auth/logout" class="nav-link active">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  </div>
</nav>
<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?= URL ?>public/js/mostrar-lic-federativa.js" defer></script>
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

            <div class="account-content custom-form">
                <?php require_once "template/partials/mensaje.partial.php"?>
                <?php require_once "template/partials/error.partial.php"?>

                <div class="form-grid">
                    <div class="section-header-flex">
                        <h3 class="form-section-title">Datos Personales</h3>
                        <a href="<?php echo URL ?>account/edit" class="btn-edit-inline">
                            <i class="fas fa-user-edit"></i> Editar Perfil
                        </a>
                    </div>

                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->nombre); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->apellidos); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($this->account->email); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->dni ?? '-'); ?>" disabled>
                    </div>

                    <?php $opciones_sexo = ['H' => 'Masculino', 'M' => 'Femenino', 'Otro' => 'Otro'];?>
                    <div class="form-group">
                        <label>Sexo</label>
                        <input type="text" value="<?= $opciones_sexo[$this->account->sexo] ?? 'No definido' ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <input type="text" value="<?php echo $this->account->fecha_nac ?? '-'; ?>" disabled>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Dirección</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->direccion ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Población</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->poblacion ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Provincia</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->provincia ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Código Postal</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->cp ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>País</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->pais ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->tlf ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Teléfono 2</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->tlf_emg ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Club</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->club ?? 'Independiente'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Talla Camiseta</label>
                        <input type="text" value="<?php echo strtoupper($this->account->talla ?? '-'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Federado</label>
                        <input type="text" value="<?php echo ($this->account->es_federado) ? 'Sí' : 'No'; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Nº Licencia Federativa</label>
                        <input type="text" value="<?php echo htmlspecialchars($this->account->num_licencia ?? '-'); ?>" disabled>
                    </div>
                </div>


                <div class="form-grid">
                    <h3 class="form-section-title">Eliminar Cuenta</h3>
                    <div class="danger-zone-inline">
                        <p>Si eliminas tu cuenta, todos tus datos y participaciones se borrarán permanentemente.</p>
                        <a href="<?php echo URL ?>account/delete/<?php echo $_SESSION['csrf_token'] ?>" class="btn-account-delete">
                            <i class="fas fa-trash-alt"></i> Eliminar Cuenta
                        </a>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="btn-secondary" href="<?php echo URL ?>index" role="button">Volver al inicio</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>
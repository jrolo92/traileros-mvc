<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <i class="fas fa-id-badge"></i>
                    <div>
                        <h2>Ficha del Trailero</h2>
                        <p>Expediente de usuario: #<?php echo $this->user->id ?></p>
                    </div>
                </div>
                <nav class="admin-sub-menu">
                    <?php require_once "views/user/partials/menu.user.partial.php"?>
                </nav>
            </header>

            <section class="account-content">

                <div class="profile-details-wrapper" style="display: grid; grid-template-columns: 250px 1fr; gap: 40px;">

                    <aside class="profile-sidebar text-center">
                        <div class="avatar-container" style="margin-bottom: 20px;">
                             <?php 
                                // Definimos la imagen: si existe en el objeto user, la usamos; si no, la de por defecto
                                $rutaAvatar = !empty($this->user->avatar) ? $this->user->avatar : 'public/assets/img/avatars/default-avatar.png';
                            ?>
                            <img src="<?php echo URL . $rutaAvatar . '?t' . time() ?>"
                                 alt="Avatar"
                                 style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid #f4f7f6;">
                        </div>
                        <div class="sidebar-meta">
                            <h3 style="margin-bottom: 10px;"><?php echo htmlspecialchars($this->user->nombre . ' ' . $this->user->apellidos) ?></h3>
                            <span class="role-badge <?php echo strtolower($this->user->rol) ?>">
                                <?php echo $this->user->rol ?>
                            </span>
                        </div>
                    </aside>

                    <article class="profile-info-content">
    
    <div class="info-group" style="margin-bottom: 35px;">
        <h4 class="form-section-title"><i class="fas fa-user"></i> Datos Personales</h4><br>
        <div class="form-grid">
            <div class="form-group">
                <label>Nombre</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->nombre) ?></div>
            </div>
            <div class="form-group">
                <label>Apellidos</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->apellidos) ?></div>
            </div>
            <div class="form-group">
                <label>DNI</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->dni ?? '-') ?></div>
            </div>
            <?php $opciones_sexo = ['H'    => 'Masculino', 'M'    => 'Femenino', 'Otro' => 'Otro'];?>
            <div class="form-group">
                <label>Sexo</label>
                <div class="info-value-box"><?= ucfirst($opciones_sexo[$this->user->sexo] ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Fecha Nacimiento</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->fecha_nac ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->email) ?></div>
            </div>
        </div>
    </div>

    <div class="info-group" style="margin-bottom: 35px;">
        <h4 class="form-section-title"><i class="fas fa-map-marker-alt"></i> Contacto y Ubicación</h4><br>
        <div class="form-grid">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Dirección</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->direccion ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Población</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->poblacion ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Provincia / Estado</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->provincia ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Código Postal</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->cp ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>País</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->pais ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Teléfono Principal</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->tlf ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>Teléfono Emergencia</label>
                <div class="info-value-box" >
                    <?= htmlspecialchars($this->user->tlf_emg ?? '-') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="info-group" style="margin-bottom: 35px;">
        <h4 class="form-section-title"><i class="fas fa-running"></i> Información Deportiva</h4><br>
        <div class="form-grid">
            <div class="form-group">
                <label>Club</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->club ?: 'Independiente') ?></div>
            </div>
            <div class="form-group">
                <label>Talla Camiseta</label>
                <div class="info-value-box"><?= strtoupper($this->user->talla ?? '-') ?></div>
            </div>
            <div class="form-group">
                <label>¿Federado?</label>
                <div class="info-value-box">
                    <?= $this->user->es_federado ? 
                        '<span class="status-pill confirmed">SÍ</span>' : 
                        '<span class="status-pill muted" style="background:#eee; padding:2px 8px; border-radius:4px;">NO</span>' ?>
                </div>
            </div>
            <div class="form-group">
                <label>Nº Licencia</label>
                <div class="info-value-box"><?= htmlspecialchars($this->user->num_licencia ?? '-') ?></div>
            </div>
        </div>
    </div>

    <div class="info-group">
        <h4 class="form-section-title"><i class="fas fa-server"></i> Cuenta</h4><br>
        <div class="form-grid">
            <div class="form-group">
                <label>Fecha de Registro</label>
                <div class="info-value-box"><?= $this->user->created_at ? date('d/m/Y H:i', strtotime($this->user->created_at)) : '-' ?></div>
            </div>
            <div class="form-group">
                <label>Última Actualización</label>
                <div class="info-value-box"><?= $this->user->updated_at ? date('d/m/Y H:i', strtotime($this->user->updated_at)) : '-' ?></div>
            </div>
        </div>
    </div>

</article>
                </div>

                <footer class="form-actions">
                    <div class="btns-left">
                        <a href="<?php echo URL ?>user" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; background: #eee; color: #333; border-radius: 8px;">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="<?php echo URL ?>user/edit/<?php echo $this->user->id ?>" class="btn-account-save" style="text-decoration: none; padding: 10px 20px; background: #27ae60; color: #fff; border-radius: 8px;">
                            <i class="fas fa-edit"></i> Editar Trailero
                        </a>
                    </div>
                </footer>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>
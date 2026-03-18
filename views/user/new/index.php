<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
    <script src="<?= URL ?>public/js/mostrar-lic-federativa.js" defer></script>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php' ?>

    <main class="account-container">
        <div class="account-card">
            
            <header class="account-header">
                <div class="user-info-main">
                    <i class="fas fa-user-plus"></i>
                    <div>
                        <h2>Nuevo Trailero</h2>
                        <p>Registro completo de nuevo perfil en el sistema</p>
                    </div>
                </div>
            </header>

            <section class="account-content">
                <?php require_once("template/partials/mensaje.partial.php") ?>
                <?php require_once("template/partials/error.partial.php") ?>

                <form action="<?= URL ?>user/create" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="profile-details-wrapper" style="display: grid; grid-template-columns: 250px 1fr; gap: 40px;">
                        
                        <aside class="profile-sidebar text-center">
                            <div class="avatar-container" style="margin-bottom: 20px;">
                                <img src="<?= URL ?>public/assets/img/avatars/default-avatar.png" 
                                     alt="Nuevo Usuario"
                                     style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid #f4f7f6; filter: grayscale(0.8);">
                            </div>

                            <div class="form-group" style="text-align: left; margin-bottom: 20px;">
                                <label style="font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: 700;">Subir Avatar</label>
                                <input type="file" name="avatar" class="form-control" style="font-size: 0.8rem;">
                            </div>

                            <div class="form-group" style="text-align: left;">
                                <label style="font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: 700;">Rol Asignado</label>
                                <select name="role_id" class="form-select <?= isset($this->errors['role_id']) ? 'is-invalid' : '' ?>" required>
                                    <option value="" selected disabled>Seleccionar...</option>
                                    <?php foreach ($this->roles as $id => $nombre_rol): ?>
                                        <option value="<?= $id ?>" <?= (($this->user->role_id ?? null) == $id) ? 'selected' : null ?>>
                                            <?= $nombre_rol ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-danger"><?= $this->errors['role_id'] ?? '' ?></small>
                            </div>
                        </aside>

                        <article class="profile-info-content">
                            
                            <div class="info-group" style="margin-bottom: 35px;">
                                <h4 class="form-section-title"><i class="fas fa-key"></i> Credenciales de Acceso</h4><br>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Email (Usuario)</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($this->user->email ?? '') ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Contraseña</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirmar Contraseña</label>
                                        <input type="password" name="password_confirm" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="info-group" style="margin-bottom: 35px;">
                                <h4 class="form-section-title"><i class="fas fa-id-card"></i> Datos Personales</h4><br>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($this->user->nombre ?? '') ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Apellidos</label>
                                        <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($this->user->apellidos ?? '') ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>DNI</label>
                                        <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($this->user->dni ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Sexo</label>
                                        <select name="sexo" class="form-select">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="hombre" <?= (($this->user->sexo ?? '') == 'hombre') ? 'selected' : '' ?>>Hombre</option>
                                            <option value="mujer" <?= (($this->user->sexo ?? '') == 'mujer') ? 'selected' : '' ?>>Mujer</option>
                                            <option value="otro" <?= (($this->user->sexo ?? '') == 'otro') ? 'selected' : '' ?>>Otro</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Fecha Nacimiento</label>
                                        <input type="date" name="fecha_nac" class="form-control" value="<?= $this->user->fecha_nac ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="info-group" style="margin-bottom: 35px;">
                                <h4 class="form-section-title"><i class="fas fa-envelope"></i> Contacto y Ubicación</h4><br>
                                <div class="form-grid">
                                    <div class="form-group" style="grid-column: 1 / -1;">
                                        <label>Dirección</label>
                                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($this->user->direccion ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Población</label>
                                        <input type="text" name="poblacion" class="form-control" value="<?= htmlspecialchars($this->user->poblacion ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Provincia</label>
                                        <input type="text" name="provincia" class="form-control" value="<?= htmlspecialchars($this->user->provincia ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>C.P.</label>
                                        <input type="text" name="cp" class="form-control" value="<?= htmlspecialchars($this->user->cp ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>País</label>
                                        <input type="text" name="pais" class="form-control" value="<?= htmlspecialchars($this->user->pais ?? 'España') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="tel" name="tlf" class="form-control" value="<?= htmlspecialchars($this->user->tlf ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Tel. Emergencia</label>
                                        <input type="tel" name="tlf_emg" class="form-control" value="<?= htmlspecialchars($this->user->tlf_emg ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="info-group">
                                <h4 class="form-section-title"><i class="fas fa-running"></i> Perfil Deportivo</h4><br>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Club</label>
                                        <input type="text" name="club" class="form-control" value="<?= htmlspecialchars($this->user->club ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Talla Camiseta</label>
                                        <select name="talla" class="form-select">
                                            <option value="" selected disabled>Elegir talla...</option>
                                            <?php foreach(['XS','S','M','L','XL','XXL'] as $t): ?>
                                                <option value="<?= $t ?>" <?= (($this->user->talla ?? '') == $t) ? 'selected' : '' ?>><?= $t ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>¿Federado?</label>
                                        <select id="es_federado" name="es_federado" class="form-select">
                                            <option value="0" selected>No</option>
                                            <option value="1">Sí</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nº Licencia</label>
                                        <input type="text" id="num_licencia" name="num_licencia" class="form-control" placeholder="No necesario">
                                    </div>
                                </div>
                            </div>

                        </article>
                    </div>

                    <footer class="form-actions" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="<?= URL ?>user" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; background: #eee; color: #333; border-radius: 8px;">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" style="cursor: pointer; border: none; padding: 10px 20px; background: #27ae60; color: #fff; border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </footer>
                </form>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
</body>
</html>
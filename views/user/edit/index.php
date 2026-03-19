<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/mostrar-lic-federativa.js" defer></script>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <i class="fas fa-user-edit"></i>
                    <div>
                        <h2>Editar Perfil del Trailero</h2>
                        <p>ID de usuario: #<?php echo $this->user->id ?> | @<?php echo htmlspecialchars($this->user->nombre) ?></p>
                    </div>
                </div>
            </header>

            <section class="account-content">
                <?php require_once "template/partials/mensaje.partial.php"?>
                <?php require_once "template/partials/error.partial.php"?>

                <form action="<?php echo URL ?>user/update/<?php echo $this->user->id ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="profile-details-wrapper" style="display: grid; grid-template-columns: 250px 1fr; gap: 40px;">

                        <aside class="profile-sidebar text-center">
                            <div class="avatar-container" style="margin-bottom: 20px;">
                                <img src="<?php echo URL ?>public/assets/img/avatars/<?php echo $this->user->avatar ?? 'default-avatar.png' ?>"
                                     alt="Avatar"
                                     style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid #f4f7f6;">
                            </div>

                            <div class="form-group" style="text-align: left; margin-bottom: 20px;">
                                <label style="font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: 700;">Cambiar Foto</label>
                                <input type="file" name="avatar" class="form-control" style="font-size: 0.8rem;">
                            </div>

                            <div class="form-group" style="text-align: left;">
                                <label style="font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: 700;">Rol del Sistema</label>
                                <select class="form-select <?php echo isset($this->errors['role_id']) ? 'is-invalid' : '' ?>" name="role_id">
                                    <?php foreach ($this->roles as $id => $rol): ?>
                                        <option value="<?php echo $id ?>" <?php echo ($this->user->role_id == $id) ? 'selected' : '' ?>><?php echo $rol ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </aside>

                        <article class="profile-info-content">

                            <div class="info-group" style="margin-bottom: 35px;">
                                <h4 class="form-section-title"><i class="fas fa-user"></i> Datos Personales</h4><br>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($this->user->nombre) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Apellidos</label>
                                        <input type="text" name="apellidos" class="form-control" value="<?php echo htmlspecialchars($this->user->apellidos) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>DNI / Pasaporte</label>
                                        <input type="text" name="dni" class="form-control" value="<?php echo htmlspecialchars($this->user->dni ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Sexo</label>
                                        <?php $opciones_sexo = ['H' => 'Masculino', 'M' => 'Femenino', 'Otro' => 'Otro']; ?>
                                        <select id="sexo" name="sexo" class="form-select">
                                            <option value="" disabled <?php echo empty($this->user->sexo) ? 'selected' : '' ?>>
                                                Seleccionar...
                                            </option>

                                            <?php foreach ($opciones_sexo as $valor => $texto): ?>
                                                <option value="<?php echo $valor ?>" <?php echo ($this->user->sexo == $valor) ? 'selected' : '' ?>>
                                                    <?php echo $texto ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Fecha Nacimiento</label>
                                        <input type="date" name="fecha_nac" class="form-control" value="<?php echo $this->user->fecha_nac ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Email (Cuenta)</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($this->user->email) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="info-group" style="margin-bottom: 35px;">
                                <h4 class="form-section-title"><i class="fas fa-map-marker-alt"></i> Contacto y Ubicación</h4><br>
                                <div class="form-grid">
                                    <div class="form-group" style="grid-column: 1 / -1;">
                                        <label>Dirección Postal</label>
                                        <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($this->user->direccion ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Población</label>
                                        <input type="text" name="poblacion" class="form-control" value="<?php echo htmlspecialchars($this->user->poblacion ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Provincia</label>
                                        <input type="text" name="provincia" class="form-control" value="<?php echo htmlspecialchars($this->user->provincia ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Código Postal</label>
                                        <input type="text" name="cp" class="form-control" value="<?php echo htmlspecialchars($this->user->cp ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>País</label>
                                        <input type="text" name="pais" class="form-control" value="<?php echo htmlspecialchars($this->user->pais ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono Móvil</label>
                                        <input type="tel" name="tlf" class="form-control" value="<?php echo htmlspecialchars($this->user->tlf ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Teléfono Emergencia</label>
                                        <input type="tel" name="tlf_emg" class="form-control" style="border-color: #fab1a0;" value="<?php echo htmlspecialchars($this->user->tlf_emg ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="info-group">
                                <h4 class="form-section-title"><i class="fas fa-running"></i> Información Deportiva</h4><br>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Club / Equipo</label>
                                        <input type="text" name="club" class="form-control" value="<?php echo htmlspecialchars($this->user->club ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Talla Camiseta</label>
                                        <select name="talla" class="form-select">
                                            <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $t): ?>
                                                <option value="<?php echo $t ?>" <?php echo ($this->user->talla == $t) ? 'selected' : '' ?>><?php echo $t ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Estado Licencia</label>
                                        <select name="es_federado" id="es_federado" class="form-select">
                                            <option value="0" <?php echo ! $this->user->es_federado ? 'selected' : '' ?>>No Federado</option>
                                            <option value="1" <?php echo $this->user->es_federado ? 'selected' : '' ?>>Federado (En vigor)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nº Licencia Federativa</label>
                                        <input type="text" name="num_licencia" id="num_licencia" class="form-control" value="<?php echo htmlspecialchars($this->user->num_licencia ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                        </article>
                    </div>

                    <footer class="form-actions" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="<?php echo URL ?>user" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; background: #eee; color: #333; border-radius: 8px;">
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
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>
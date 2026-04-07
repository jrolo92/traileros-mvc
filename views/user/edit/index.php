<!doctype html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/mostrar-lic-federativa.js" defer></script>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <section class="account-content">
    <?php require_once "template/partials/mensaje.partial.php"?>
    <?php require_once "template/partials/error.partial.php"?>

    <form action="<?php echo URL ?>user/update/<?php echo $this->user->id ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>">

        <div class="profile-details-wrapper">

            <aside class="profile-sidebar text-center">
                <div class="avatar-container">
                    <?php 
                        $rutaAvatar = !empty($this->user->avatar) ? $this->user->avatar : 'public/assets/img/avatars/default-avatar.png';
                    ?>
                    <div class="avatar-circle avatar-edit">
                        <img src="<?php echo URL . $rutaAvatar . '?t=' . time() ?>" alt="Avatar">
                    </div>
                </div>

                <div class="form-group">
                    <label>Cambiar Foto</label>
                    <input type="file" name="avatar" class="form-control">
                </div>

                <div class="form-group">
                    <label>Rol del Sistema</label>
                    <select class="form-select <?php echo isset($this->errors['role_id']) ? 'is-invalid' : '' ?>" name="role_id">
                        <?php foreach ($this->roles as $id => $rol): ?>
                            <option value="<?php echo $id ?>" <?php echo ($this->user->role_id == $id) ? 'selected' : '' ?>><?php echo $rol ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </aside>

            <article class="profile-info-content">

                <div class="info-group">
                    <h4 class="form-section-title"><i class="fas fa-user"></i> Datos Personales</h4>
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
                            <select name="sexo" class="form-select">
                                <option value="" disabled <?php echo empty($this->user->sexo) ? 'selected' : '' ?>>Seleccionar...</option>
                                <?php foreach ($opciones_sexo as $valor => $texto): ?>
                                    <option value="<?php echo $valor ?>" <?php echo ($this->user->sexo == $valor) ? 'selected' : '' ?>><?php echo $texto ?></option>
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

                <div class="info-group">
                    <h4 class="form-section-title"><i class="fas fa-map-marker-alt"></i> Contacto y Ubicación</h4>
                    <div class="form-grid">
                        <div class="form-group full-width">
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
                            <input type="tel" name="tlf_emg" class="form-control input-emergency" value="<?php echo htmlspecialchars($this->user->tlf_emg ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="info-group">
                    <h4 class="form-section-title"><i class="fas fa-running"></i> Información Deportiva</h4>
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

        <footer class="form-actions">
            <a href="<?php echo URL ?>user" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </footer>
    </form>
</section>
</body>
</html>
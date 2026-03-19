<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
    <script src="<?= URL ?>public/js/upload-avatar.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <form id="avatarForm" class="avatar-uploader">
                        <label for="avatarInput" class="avatar-label avatar-circle size-md" title="Cambiar foto de perfil">
                            
                            <?php if (!empty($_SESSION['user_avatar']) && file_exists($_SESSION['user_avatar'])): ?>
                                <img id="avatarPreview" src="<?= URL . $_SESSION['user_avatar'] . '?t=' . time() ?>" alt="Avatar">
                            <?php else: ?>
                                <i id="avatarIcon" class="fas fa-user-circle" style="font-size: 4rem;"></i>
                            <?php endif; ?>

                            <div class="avatar-overlay">
                                <i class="fas fa-camera"></i>
                            </div>
                        </label>

                        <input type="file" id="avatarInput" name="avatar" accept="image/jpeg, image/png, image/webp" style="display: none;">
                    </form>

                    <div class="user-titles">
                        <h2><?= $this->title ?></h2>
                        <p>Actualiza tu información personal y deportiva</p>
                    </div>
                </div> <nav class="account-menu">
                    <?php require_once "views/account/partials/menu.partial.php"?>
                </nav>
            </header>

            <section class="account-main-content">

                <?php require_once "template/partials/mensaje.partial.php"?>
                <?php require_once "template/partials/error.partial.php"?>

                <div class="account-content">
                    <form action="<?= URL ?>account/update" method="post" class="account-form">

                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="form-grid">
                            <h3 class="form-section-title" style="grid-column: 1 / -1;">Datos Personales</h3>
                            
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input id="nombre" type="text" name="nombre"
                                       class="<?= (isset($this->errors['name'])) ? 'input-error' : '' ?>"
                                       value="<?= htmlspecialchars($this->account->nombre); ?>" required>
                                <?php if (isset($this->errors['name'])): ?>
                                    <span class="error-msg"><?= $this->errors['name'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="apellidos">Apellidos</label>
                                <input id="apellidos" type="text" name="apellidos"
                                       value="<?= htmlspecialchars($this->account->apellidos ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input id="email" type="email" name="email"
                                       class="<?= (isset($this->errors['email'])) ? 'input-error' : '' ?>"
                                       value="<?= htmlspecialchars($this->account->email); ?>" required>
                                <?php if (isset($this->errors['email'])): ?>
                                    <span class="error-msg"><?= $this->errors['email'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="dni">DNI / NIE</label>
                                <input id="dni" type="text" name="dni"
                                       value="<?= htmlspecialchars($this->account->dni ?? ''); ?>">
                            </div>


                            <?php $opciones_sexo = ['H'    => 'Masculino', 'M'    => 'Femenino', 'Otro' => 'Otro'];?>
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <select name="sexo" class="form-select">
                                    <option value="" disabled <?= empty($this->account->sexo) ? 'selected' : '' ?>>
                                        Seleccionar...
                                    </option>
                                    <?php foreach ($opciones_sexo as $valor => $texto): ?>
                                        <option value="<?= $valor ?>" <?= ($this->account->sexo == $valor) ? 'selected' : '' ?>>
                                            <?= $texto ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="fecha_nac">Fecha de Nacimiento</label>
                                <input id="fecha_nac" type="date" name="fecha_nac"
                                       value="<?= $this->account->fecha_nac ?? ''; ?>">
                            </div>

                            <h3 class="form-section-title" style="grid-column: 1 / -1; margin-top: 20px;">Dirección y Contacto</h3>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="direccion">Dirección</label>
                                <input id="direccion" type="text" name="direccion"
                                       value="<?= htmlspecialchars($this->account->direccion ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="poblacion">Población</label>
                                <input id="poblacion" type="text" name="poblacion"
                                       value="<?= htmlspecialchars($this->account->poblacion ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="provincia">Provincia</label>
                                <input id="provincia" type="text" name="provincia"
                                       value="<?= htmlspecialchars($this->account->provincia ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="cp">Código Postal</label>
                                <input id="cp" type="text" name="cp"
                                       value="<?= htmlspecialchars($this->account->cp ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="pais">País</label>
                                <input id="pais" type="text" name="pais"
                                       value="<?= htmlspecialchars($this->account->pais ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="tlf">Teléfono</label>
                                <input id="tlf" type="text" name="tlf"
                                       value="<?= htmlspecialchars($this->account->tlf ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="tlf_emg">Teléfono 2</label>
                                <input id="tlf_emg" type="text" name="tlf_emg"
                                       value="<?= htmlspecialchars($this->account->tlf_emg ?? ''); ?>"
                                       placeholder="Contacto en caso de accidente">
                            </div>

                            <h3 class="form-section-title" style="grid-column: 1 / -1; margin-top: 20px;">Información Deportiva</h3>

                            <div class="form-group">
                                <label for="club">Club</label>
                                <input id="club" type="text" name="club"
                                       value="<?= htmlspecialchars($this->account->club ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="talla">Talla Camiseta</label>
                                <select id="talla" name="talla" class="form-select">
                                    <option value="" disabled <?= empty($this->account->talla) ? 'selected' : '' ?>>Seleccionar talla...</option>
                                    <?php foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $t): ?>
                                        <option value="<?= $t ?>" <?= (strtoupper($this->account->talla ?? '') == $t) ? 'selected' : '' ?>><?= $t ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="es_federado">¿Estás Federado?</label>
                                <select id="es_federado" name="es_federado" class="form-select">
                                    <option value="0" <?= (!$this->account->es_federado) ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= ($this->account->es_federado) ? 'selected' : '' ?>>Sí</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="num_licencia">Nº Licencia Federativa</label>
                                <input id="num_licencia" type="text" name="num_licencia"
                                       value="<?= htmlspecialchars($this->account->num_licencia ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="btns-left">
                                <a class="btn-account-cancel" href="<?= URL ?>account" role="button">Cancelar</a>
                            </div>
                            <button type="submit" class="btn-account-save">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>

                    </form>
                </div> 
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>
</html>
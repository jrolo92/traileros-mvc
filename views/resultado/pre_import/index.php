<!doctype html>
<html lang="es">

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <script src="<?php echo URL ?>public/js/menu-order.js" defer></script>
    <script src="<?php echo URL ?>public/js/search-ajax.js" defer></script>
</head>

<body>
    <?php require_once 'template/partials/header.partial.php'?>

    <main class="account-container">
        <div class="account-card">

            <header class="account-header">
                <div class="user-info-main">
                    <div class="avatar-circle size-md" style="background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trophy" style="font-size: 2rem;"></i>
                    </div>
                    
                    <div>
                        <h2><?php echo $this->title ?></h2>
                        <p>Indica a qué campo corresponde cada columna de tu archivo</p>
                    </div>
                </div>

                <div class="back-navigation">
                        <a href="javascript:window.history.back();" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                </div>
            </header>

            <div class="csv-preview" style="overflow-x: auto; margin-bottom: 20px;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <?php foreach($this->cabecera as $name): ?>
                                <th><?= htmlspecialchars($name) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Mostramos solo las 3 primeras filas de ejemplo
                        $filasEjemplo = array_slice($_SESSION['import_data']['filas'], 0, 3);
                        foreach($filasEjemplo as $fila): 
                        ?>
                            <tr>
                                <?php foreach($fila as $celda): ?>
                                    <td><?= htmlspecialchars($celda) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <form action="<?= URL ?>resultado/processImport" method="POST">

                <div class="form-group">
                    <label>Columna del Dorsal:</label>
                    <select name="map_dorsal" required>
                        <?php foreach($this->header as $key => $name): ?>
                            <option value="<?= $key ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Columna del Tiempo:</label>
                    <select name="map_tiempo" required>
                        <?php foreach($this->cabecera as $key => $name): ?>
                            <option value="<?= $key ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Finalizar Importación</button>
            </form>

            <section class="account-main-content">
                <?php require_once "template/partials/mensaje.partial.php" ?>
                <?php require_once "template/partials/error.partial.php" ?>


    <footer class="footer">
            <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>
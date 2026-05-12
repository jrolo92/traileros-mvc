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

            <div class="form-page-container">
    <div class="form-card" style="max-width: 1000px;"> <!-- Un poco más ancha para la tabla -->
        
        <header class="form-card-header">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <h2>Configuración de Importación</h2>
            <p>Asocia las columnas de tu CSV con los datos del sistema y verifica la vista previa.</p>
        </header>

        <form action="<?= URL ?>resultado/process_import" method="POST" class="custom-form">
            
            <div class="form-row">
                <!-- Selector Dorsal -->
                <div class="col">
                    <div class="form-group">
                        <label for="map_dorsal">Columna del Dorsal</label>
                        <select name="map_dorsal" id="map_dorsal" class="form-select" required>
                            <option value="" disabled selected>Selecciona columna...</option>
                            <?php if (isset($this->cabecera) && is_array($this->cabecera)): ?>
                                <?php foreach ($this->cabecera as $i => $nombre): ?>
                                    <option value="<?= $i ?>"><?= htmlspecialchars($nombre) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Selector Tiempo -->
                <div class="col">
                    <div class="form-group">
                        <label for="map_tiempo">Columna del Tiempo</label>
                        <select name="map_tiempo" id="map_tiempo" class="form-select" required>
                            <option value="" disabled selected>Selecciona columna...</option>
                            <?php if (isset($this->cabecera) && is_array($this->cabecera)): ?>
                                <?php foreach ($this->cabecera as $i => $nombre): ?>
                                    <option value="<?= $i ?>"><?= htmlspecialchars($nombre) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 25px 0;">

            <div class="form-section-header">
                <h3>Vista previa de los datos</h3>
            </div>

            <div class="table-container" style="margin-top: 15px; overflow-x: auto;">
                <table class="main-table"> <!-- Usamos tu clase global de tablas -->
                    <thead>
                        <tr>
                            <?php if (isset($this->cabecera)): ?>
                                <?php foreach ($this->cabecera as $col): ?>
                                    <th><?= htmlspecialchars($col) ?></th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Accedemos a las filas guardadas en la sesión para la previa
                        $filasPrevia = array_slice($_SESSION['import_data']['filas'], 0, 5);
                        foreach ($filasPrevia as $fila): 
                        ?>
                            <tr>
                                <?php foreach ($fila as $celda): ?>
                                    <td><?= htmlspecialchars($celda) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <div class="info">
                    <p style="color: var(--text-sec); font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i> Se han cargado <strong><?= count($_SESSION['import_data']['filas']) ?></strong> filas.
                    </p>
                </div>
                <div class="main-buttons">
                    <a href="<?= URL ?>carrera/gestion/<?= $this->evento_id ?>" class="btn-secondary" style="align-self: center;">Cancelar</a>
                    <button type="submit" class="btn-primary">Finalizar Importación</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <footer class="footer">
            <?php require_once 'template/partials/footer.partial.php'?>
    </footer>
</body>

</html>
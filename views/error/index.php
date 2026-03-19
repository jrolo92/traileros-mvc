<!doctype html>
<html lang="es"> 

<?php require_once("template/layouts/head.layout.php") ?>

<body>
    <?php require_once 'template/partials/header.partial.php'; ?>

    <main class="form-page-container">
        <div class="form-card" style="border-top: 5px solid #e74c3c;"> 

            <header class="form-card-header">
                <i class="fas fa-exclamation-triangle" style="color: #e74c3c; font-size: 3rem; margin-bottom: 15px;"></i>
                <h2 style="color: #333;">ERROR <?= $this->tipo ?></h2>
                <h4 style="color: #666; font-weight: 400;"><?= $this->titulo ?></h4>
            </header>

            <div style="text-align: center; padding: 20px 0;">
                <p class="lead" style="color: #555; font-size: 1.1rem;">
                    <?= $this->mensaje ?>
                </p>
            </div>

            <div class="form-actions" style="justify-content: center; border: none;">
                <a href="<?= URL ?>main" class="btn-account-save" style="background-color: #3498db;">
                    Volver
                </a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'; ?>
    </footer>
</body>

</html>
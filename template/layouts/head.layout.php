    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->title ?? 'Traileros' ?></title>
    <link rel="stylesheet" href="<?= URL ?>public/css/style.css">
	<!-- Cargar iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FAVICON -->
    <!-- Para navegadores antiguos -->
    <link rel="icon" href="<?= URL ?>public/assets/img/favicon.ico" sizes="32x32">

    <!-- Formato SVG -->
    <link rel="icon" href="<?= URL ?>public/assets/img/favicon.svg" type="image/svg+xml">
    <script>
        // fetch de upload-avatar.js
        const RUTA_URL = '<?= URL ?>';
        
        // Tema --> Bloque de ejecución inmediata para evitar el flash blanco
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <script src="<?= URL ?>public/js/theme-logic.js" defer></script>

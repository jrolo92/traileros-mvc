    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->title ?? 'Traileros' ?></title>
    <link rel="stylesheet" href="<?= URL ?>public/css/style.css">
	<!-- Cargar iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

document.addEventListener('DOMContentLoaded', () => {
    // Referencia a elementos
    const themeRow = document.getElementById('theme-row');
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = themeRow ? themeRow.querySelector('span') : null;
    const htmlElement = document.documentElement;

    // Mostrar icono y texto adecuado según el tema actual
    const updateUI = (theme) => {
        // Seguridad por si el botón no está en la página
        if (!themeIcon) return;

        // Sincronizar con el checkbox
        if (themeToggle) {
            themeToggle.checked = (theme === 'dark');
        }

        if (theme === 'dark') {
            themeIcon.className = 'fas fa-sun';
            if (themeText) themeText.textContent = 'Modo Claro';
        } else {
            themeIcon.className = 'fas fa-moon';
            if (themeText) themeText.textContent = 'Modo Oscuro';
        }
    };

    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    updateUI(savedTheme);

    // Evento click en cualquier parte de la fila
    if (themeRow) {
        themeRow.addEventListener('click', (e) => {

            if (e.target === themeToggle) return;

            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateUI(newTheme);
        });

        // Por si el usuario usa la tecla espacio o clica justo en el slider
        themeToggle.addEventListener('change', () => {
            const newTheme = themeToggle.checked ? 'dark' : 'light';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateUI(newTheme);
        });
    }
});
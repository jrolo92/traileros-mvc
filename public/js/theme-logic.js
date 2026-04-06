document.addEventListener('DOMContentLoaded', () => {
    // Referencia a elementos
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = themeToggle ? themeToggle.querySelector('span') : null;
    const htmlElement = document.documentElement;

    // Mostrar icono y texto adecuado según el tema actual
    const updateUI = (theme) => {
        // Seguridad por si el botón no está en la página
        if (!themeIcon) return;

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

    // Evento click
    if (themeToggle) {
        themeToggle.addEventListener('click', (e) => {
            e.preventDefault();

            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateUI(newTheme);
        });
    }
});
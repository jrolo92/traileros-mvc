document.addEventListener('DOMContentLoaded', () => {
    const themeBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');
    const htmlElement = document.documentElement;

    const updateUI = (theme) => {
        if (!themeIcon) return;

        if (theme === 'dark') {
            htmlElement.setAttribute('data-theme', 'dark');
            themeIcon.className = 'fas fa-sun';
            themeBtn.title = 'Modo claro';
        } else {
            htmlElement.setAttribute('data-theme', 'light');
            themeIcon.className = 'fas fa-moon';
            themeBtn.title = 'Modo oscuro';
        }
    };

    // Carga el tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    updateUI(savedTheme);

    // Evento de click en el botón del header
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            localStorage.setItem('theme', newTheme);
            updateUI(newTheme);

        });
    }
});
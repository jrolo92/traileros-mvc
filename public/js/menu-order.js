{
    document.addEventListener('DOMContentLoaded', () => {
        const dropdown = document.getElementById('orderDropdown');
        const btn = document.getElementById('dropdownBtn');

        // Verificamos que ambos elementos existan en la página actual
        if (dropdown && btn) {
            
            // Evento para abrir/cerrar el menú al hacer clic en el botón
            btn.addEventListener('click', (e) => {
                e.stopPropagation(); // Evita que el clic se propague al document
                dropdown.classList.toggle('is-open');
            });

            // Evento para cerrar el menú si se hace clic en cualquier otro lugar
            document.addEventListener('click', (e) => {
                // Si el clic NO ha sido dentro del contenedor del dropdown, lo cerramos
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('is-open');
                }
            });
        }
    });
}
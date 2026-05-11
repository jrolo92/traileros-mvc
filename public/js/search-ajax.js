// Proporciona la capacidad de búsqueda dinámica en todas las vistas que lo requieran
document.addEventListener('DOMContentLoaded', function () {
    // Selectores de elementos necesarios
    const searchInput = document.getElementById('search');
    const searchForm = document.querySelector('.carreras-search, .nav-search-form');
    const tableBody = document.querySelector('.admin-table tbody');
    const gridContainer = document.querySelector('.carreras-grid');
    const footerCount = document.querySelector('.admin-table-footer strong');

    // Temporizador
    let timeout = null;

    if (searchInput && searchForm) {
        // La búsqueda no va a funcionar pulsando enter
        searchForm.addEventListener('submit', e => e.preventDefault());
        // Si no cuando el usuario escriba una letra
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value;
            // Obtiene la url de cada búsqueda en función del formulario donde se encuentre
            const baseUrl = searchForm.getAttribute('action');
            
            clearTimeout(timeout);

            // Tiempo de espera de 300ms para hacer la búsqueda
            timeout = setTimeout(() => {
                const url = `${baseUrl}${baseUrl.includes('?') ? '&' : '?'}term=${encodeURIComponent(searchTerm)}`;

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        // Devuelve el documento completo
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // 1. Si es una TABLA, solo actualizamos el cuerpo (tbody)
                        const newTableBody = doc.querySelector('.admin-table tbody');
                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        } 
                        // 2. Si es un GRID de tarjetas, actualizamos el grid entero
                        else {
                            const newGrid = doc.querySelector('.carreras-grid');
                            const currentGrid = document.querySelector('.carreras-grid');
                            if (newGrid && currentGrid) {
                                currentGrid.innerHTML = newGrid.innerHTML;
                            }
                        }

                        // 3. Actualizar el contador (si lo hay)
                        const newCount = doc.querySelector('.admin-table-footer strong');
                        if (footerCount && newCount) {
                            footerCount.innerHTML = newCount.innerHTML;
                        }

                        // 4. Manejo de paginación (solo para la vista principal de carreras)
                        const pagination = document.querySelector('.pagination-container');
                        if (pagination) {
                            pagination.style.display = searchTerm.length > 0 ? 'none' : 'flex';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 300);
        });
    }
});
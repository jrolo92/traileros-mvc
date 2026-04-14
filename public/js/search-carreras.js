document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById("search");
    const searchForm = document.querySelector('.carreras-search');
    const carrerasGrid = document.querySelector('.carreras-grid');
    const pagination = document.querySelector('.pagination-container');

    let timeout = null;

    if (searchInput && carrerasGrid) {
        // Evitamos que el enter recargue la pagina
        if(searchForm) {
            searchForm.addEventListener('submit', e => e.preventDefault());
        }

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value;

            // Limpiamos el timeout anterior
            clearTimeout(timeout);

            // Esperamos 300ms antes de lanzar la petición
            timeout = setTimeout(() => {
                fetch(`carrera/search?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Actualizamos el grid de carreras:
                        const newGrid = doc.querySelector('.carreras-grid');
                        if (newGrid) {
                            carrerasGrid.innerHTML = newGrid.innerHTML;
                        }

                        // Ocultamos paginación:
                        if (pagination) {
                            if (searchTerm > 0) {
                                pagination.style.display = 'none';
                            } else {
                                pagination.style.display = 'flex';
                            }
                        }
                    })
                    .catch (error => console.error('Error en búsqueda de carreras:', error));
            }, 300);
        });
    }
});
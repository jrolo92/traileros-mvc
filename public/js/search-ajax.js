document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const searchForm = document.querySelector('.carreras-search');

    const contentContainer = document.querySelector('.account-content');
    const subtitleContainer = document.getElementById('subtitle-container');
    const footerCount = document.querySelector('.admin-table-footer strong');

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    }

    if (searchInput && contentContainer) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value;

            // Petición al controlador
            fetch(`inscripcion/search?term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Actualizar Tabla
                    const newContent = doc.querySelector('.account-content').innerHTML;
                    contentContainer.innerHTML = newContent;
                    
                    // Actualizar Subtítulo (NUEVO)
                    const newSubtitle = doc.getElementById('subtitle-container').innerHTML;
                    if (subtitleContainer) subtitleContainer.innerHTML = newSubtitle;

                    // Actualizar Contador
                    const newCount = doc.querySelector('.admin-table-footer strong').innerHTML;
                    if (footerCount && newCount) footerCount.innerHTML = newCount;
                })
                .catch(error => console.error('Error en búsqueda AJAX:', error));
        });
    }
});

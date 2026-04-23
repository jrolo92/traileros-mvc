document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const searchForm = document.querySelector('.nav-search-form');
    const contentContainer = document.querySelector('.account-content');
    const footerCount = document.querySelector('.admin-table-footer strong');

    let timeout = null;

    if (searchForm) {
        searchForm.addEventListener('submit', function(e){
            e.preventDefault();
        });
    }

    if (searchInput && contentContainer) {
        searchInput.addEventListener('input', function (){
            const searchTerm = this.value;
            clearTimeout(timeout);

            timeout = setTimeout(() => {
                // Petición al controlador
                fetch(`user/search?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Actualizar tabla
                        const newContent = doc.querySelector('.account-content');
                        if (newContent && contentContainer) contentContainer.innerHTML = newContent.innerHTML;

                        // Actualizar Contador
                        const newCount = doc.querySelector('.admin-table-footer strong');
                        if (footerCount && newCount) footerCount.innerHTML = newCount.innerHTML;
                    })
                    .catch(error => console.error('Error en la búsqueda AJAX:', error));
            }, 300);
        });
    }
});
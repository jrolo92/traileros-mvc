document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const searchForm = document.querySelector('.carreras-search'); 
    const contentContainer = document.querySelector('.account-content');
    const footerCount = document.querySelector('.admin-table-footer strong');

    let timeout = null;

    if (searchForm) {
        searchForm.addEventListener('submit', (e) => e.preventDefault());
    }

    if (searchInput && contentContainer) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value;
            clearTimeout(timeout);

            const url = searchTerm === '' 
                ? `gestion` 
                : `search_gestion?term=${encodeURIComponent(searchTerm)}`;

            timeout = setTimeout(() => {
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newContent = doc.querySelector('.account-content');
                        if (newContent) contentContainer.innerHTML = newContent.innerHTML;

                        const newCount = doc.querySelector('.admin-table-footer strong');
                        if (footerCount && newCount) footerCount.innerHTML = newCount.innerHTML;
                    })
                    .catch(error => console.error('Error:', error));
            }, 300);
        });
    }
}); 
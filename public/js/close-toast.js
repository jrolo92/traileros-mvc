document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast');
    
    toasts.forEach(toast => {
        // Espera 4 segundos y añade la clase de salida
        setTimeout(() => {
            toast.classList.add('fade-out');
            // Elimina el elemento del DOM después de la animación
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 4000);
    });
});
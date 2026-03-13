document.addEventListener('DOMContentLoaded', function() {
    // 1. Seleccionamos los elementos necesarios
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    const captionText = document.getElementById("modalCaption");
    const closeBtn = document.querySelector(".close-modal");

    // Seleccionamos la imagen principal de la carrera
    const carreraImg = document.querySelector(".carrera-media .detail-img");

    // 2. Si la imagen existe, le añadimos el evento de clic
    if (carreraImg) {
        carreraImg.addEventListener('click', function() {
            // Abrimos el modal añadiendo la clase 'show' (definida en CSS)
            modal.classList.add('show');
            // Copiamos la fuente de la imagen pequeña a la grande del modal
            modalImg.src = this.src;
            // Copiamos el texto alternativo (alt) como pie de foto
            captionText.innerHTML = this.alt;
        });
    }

    // 3. Funciones para cerrar el modal
    const closeModal = function() {
        modal.classList.remove('show');
    };

    // Cerrar al hacer clic en la 'X'
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Cerrar al hacer clic en cualquier lugar fuera de la imagen (en el fondo oscuro)
    if (modal) {
        modal.addEventListener('click', function(event) {
            // Verificamos que se haya hecho clic en el fondo y no en la imagen
            if (event.target === modal) {
                closeModal();
            }
        });
    }

    // Cerrar al pulsar la tecla 'Esc'
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape" && modal.classList.contains('show')) {
            closeModal();
        }
    });
});
// Esperamos a que el DOM esté listo por seguridad
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteAccountForm');

    // Solo ejecutamos la lógica si el formulario de borrado está presente
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            const confirmacion = confirm(
                "¡CUIDADO!\n\n" +
                "Estás a punto de borrar tu cuenta de forma irreversible.\n" +
                "¿Deseas continuar con la eliminación?"
            );
            
            if (!confirmacion) {
                e.preventDefault(); // Cancela el envío si el usuario pulsa cancelar
            }
        });
    }
});
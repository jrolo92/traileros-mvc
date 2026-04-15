document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('modalidades-container');
    const addButton = document.getElementById('add-modalidad');

    addButton.addEventListener('click', () => {
        // Clonamos el primer bloque
        const firstBlock = container.querySelector('.modalidad-block');
        const newBlock = firstBlock.cloneNode(true);

        // Limpiamos los inputs del clon para mostrarlos vacios
        newBlock.querySelectorAll('input').forEach(input => input.value = '');
        
        // Mostramos el botón de eliminar en el nuevo bloque
        const removeBtn = newBlock.querySelector('.remove-mod');
        removeBtn.style.display = 'block';
        removeBtn.onclick = function() { newBlock.remove(); updateNumbers(); };

        container.appendChild(newBlock);
        updateNumbers();
    });

    // Actualiza el número de la modalidad (que aparece en la vista)
    function updateNumbers() {
        container.querySelectorAll('.mod-number').forEach((span, index) => {
            span.textContent = `Modalidad #${index + 1}`;
        });
    }
});
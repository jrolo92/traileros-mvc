document.getElementById('modalidad_id').addEventListener('change', function(){
    const selectedText = this.options[this.selectedIndex].text;
    const precioMatch = selectedText.match(/\d+(?:\.\d+)?/); // Extrae solo el número del texto
    if(precioMatch) {
        document.getElementById('precio').innerText = precioMatch[0] + ' €';
    }
});
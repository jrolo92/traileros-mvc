document.addEventListener('DOMContentLoaded', function() {
    const selectFederado = document.getElementById("es_federado");
    const campoLicencia = document.getElementById("num_licencia");

    if (selectFederado && campoLicencia) {
        
        function toggleLicencia() {
            if (selectFederado.value == "1") {
                // Estado: FEDERADO
                campoLicencia.disabled = false;
                campoLicencia.style.opacity = "1";
                campoLicencia.style.backgroundColor = "var(--bg-card)";
                campoLicencia.placeholder = "Introduce tu nº de licencia";
            } else {
                // Estado: NO FEDERADO
                campoLicencia.disabled = true;
                campoLicencia.value = ""; 
                campoLicencia.style.opacity = "0.5";
                campoLicencia.style.backgroundColor = "var(--bg-card)";
                campoLicencia.placeholder = "No necesario";
            }
        }
    }
    
    selectFederado.addEventListener('change', toggleLicencia);
    toggleLicencia(); 
});
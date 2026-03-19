document.getElementById('avatarInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('avatar', file);

        // Mostramos un estado de "cargando" visual (opcional)
        const overlay = document.querySelector('.avatar-overlay');
        overlay.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        overlay.style.opacity = '1';

        fetch(RUTA_URL + 'account/uploadAvatar', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizamos la imagen en el header y perfil sin recargar
                const preview = document.getElementById('avatarPreview');
                const icon = document.getElementById('avatarIcon');
                
                if (preview) {
                    preview.src = data.newImageUrl + '?t=' + new Date().getTime(); // Anti-cache
                } else if (icon) {
                    // Si antes había un icono, lo reemplazamos por una imagen
                    const newImg = document.createElement('img');
                    newImg.id = 'avatarPreview';
                    newImg.src = data.newImageUrl;
                    newImg.className = 'avatar-img';
                    icon.parentNode.replaceChild(newImg, icon);
                }
                
                // Restauramos el icono de la cámara
                overlay.innerHTML = '<i class="fas fa-camera"></i>';
                overlay.style.opacity = '';
                alert('¡Foto de perfil actualizada!');
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir la imagen');
        });
    }
});
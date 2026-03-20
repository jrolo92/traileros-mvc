document.getElementById('avatarInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('avatar', file);

        // Mostramos un estado de "cargando" visual
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
                // Url anti-cache
                const newUrl = data.newImageUrl + '?t=' + new Date().getTime();
                // Actualizamos la imagen en el header y perfil sin recargar
                const preview = document.getElementById('avatarPreview');
                const icon = document.getElementById('avatarIcon');
                
                if (preview) {
                    preview.src = newUrl;
                } else if (icon) {
                    // Si antes había un icono, lo reemplazamos por una imagen
                    const newImg = document.createElement('img');
                    newImg.id = 'avatarPreview';
                    newImg.src = newUrl;
                    newImg.className = 'avatar-img';
                    icon.parentNode.replaceChild(newImg, icon);
                }

                // Buscamos todas las imágenes dentro de contenedores "avatar-circle" -> incluye al header
                const allAvatars = document.querySelectorAll('.avatar-circle img');
                allAvatars.forEach(img => {
                    img.src = newUrl;
                });

                // Si el header tenía un icono (cuenta recién creada) tambien lo modifica
                const allHeaderIcons = document.querySelectorAll('.avatar-circle i.fa-user-circle');
                allHeaderIcons.forEach(iconNode => {
                    const navImg = document.createElement('img');
                    navImg.src = newUrl;
                    navImg.alt = "Avatar";
                    // Copiamos clases si es necesario para mantener el estilo
                    iconNode.parentNode.replaceChild(navImg, iconNode);
                });
                
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
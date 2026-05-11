1. Crear instancia EC2 (server):
    1. Hemos escogido una imagen de máquina de Amazon (AMI) de Ubuntu Server 24.04 LTS (HVM),EBS General Purpose (SSD) Volume Type de 64 bits (x86). 
    2. Tipo de instancia: t3.micro.
    3. Creamos el par de claves para mayor seguridad.
    4. En configuración de red creamos un grupo de seguridad que permita la conexión SSH, HTTPS y HTTP.
    5. Lanzamos la instancia (Me dan ipv4 pública)

2. Conexión vía SSH:
    1. Nos conectamos al server: `ssh -i "NombreParClave" ubutu@ip_server`

3. Instalación LAMP:
    1. Actualizamos sistema: `sudo apt update && sudo apt upgrade -y`
    2. Instalamos Apache y MySQL: `sudo apt install apache2 mysql-server -y`
    3. Instalamos PHP 8.4 (es el que he estado usando en mi proyecto) para ello:
        - Vamos a añadir el repositorio de PHP 8.4 para Ubuntu: `sudo add-apt-repository ppa:ondrej/php -y` y actualizamos sistema.
        - Instalamos PHP 8.4 y las extensiones necesarias para el proyecto: `sudo apt install php8.4 libapache2-mod-php8.4 php8.4-mysql php8.4-gd php8.4-curl php8.4-xml php8.4-mbstring php8.4-zip unzip -y`

4. Configuración de la Base de Datos:
    1. Entramos en MySQL: `sudo mysql`
    2. Ejecutamos comandos para crear bd y usuario de la misma. Conceder privilegios al usuario creado.
    3. Volcamos datos de la BD local (.sql).

5. Instalación de Composer:
    1. Vamos a generar la carpeta vendor en el server a partir del composer.json:
    `cd ~`
    `curl -sS https://getcomposer.org/installer | php`
    `sudo mv composer.phar /usr/local/bin/composer`

6. Despliegue del código y permisos:
    1. Limpiamos la pagina por defecto de Apache: `cd /var/www/html` + `sudo rm index.html`
    2. Clonamos repositorio: `sudo git clone https://github.com/tu-usuario/traileros-mvc.git`
    3. Instalamos librerias: `sudo composer install --no-dev`
    4. Gestión de permisos (para escritura de imágenes):

    ```bash
        # www-data es el usuario estándar que usa Apache en Ubuntu
        sudo chown -R www-data:www-data /var/www/html

        # Permisos específicos para las carpetas de subida
        sudo chmod -R 775 /var/www/html/public/assets/img/avatars
        sudo chmod -R 775 /var/www/html/public/assets/img/carreras
        sudo chmod -R 775 /var/www/html/storage
    ```

7. Activar URLs amigables:
    1. Activamos modulo rewrite: `sudo a2enmod rewrite`
    2. Configuramos el sitio: 
        - Ejecutamos: `sudo nano /etc/apache2/sites-available/000-default.conf`
        - Debajo de la línea "DocumentRoot /var/www/html" pegamos este bloque:
        ```bash
            <Directory /var/www/html>
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
            </Directory>
        ```
    3. Reiniciamos Apache: `sudo systemctl restart apache2`


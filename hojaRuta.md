public/
├── css/            # Aquí se compilará tu archivo .css final
├── scss/           # Tus archivos de desarrollo
│   ├── base/       # Reset, tipografías, variables (colores de montaña)
│   ├── components/ # Botones, cards de carreras, formularios
│   ├── layouts/    # Header, Footer, Sidebar
│   └── main.scss   # Archivo que importa todos los anteriores
├── js/             # Scripts para validaciones y efectos
└── assets/         # Imágenes de las rutas y logos

06/04/2026: Para que funcione el redimensionador de imagenes (mejor rendimiento) hay que descomentar la linea de php.ini --> extension=gd.
            IMPORTANTE: Esto habría que comprobar si sigue funcionando en un hosting (AWS)

07/04/26 - 08/04/26: Implantanción de modo oscuro.

09/04/2026: Creación de lógica de inscripciones (MVC). 
            IMPORTANTE: Plantear conveniencia de implantar pasarela de pago (ahora solo hago una simulación).

10/04/2026: Creacion de vistas de inscripciones: main y new

13/04-17/04: Refactorizacion tablas bd:
                Añado tabla modalidades

22/04/26: Integra pasarela de pago con Stripe (Modo sandbox).
23/04/26: Integra paginación en la vista de carreras  y arreglos en los sistemas de búsqueda y orden de los distintos MVC (AJAX para búsqueda sin recargar).

24/04/26: Integra envío de correos automáticos usando PHPMailer en las siguientes situaciones (con un mensaje personalizado para cada una):
            - Registro de nueva cuenta.
            - Cambio de contraseña.
            - Olvidé mi contraseña (nueva funcionalidad).
            - Inscripción a evento.
            - Eliminación de una inscripción (solo por admin u organizador).

27/04/26: Refactor de dos tablas de la base de datos:
    Eventos: elimino columnas -> edad max y edad min (pasan a la tabla de modalidades).
    Eventos: añado columnas -> fecha_fin_inscripciones y estado.
          Integración de Exportación en pdf (FPDF con composer).
          Otros pequeños arreglos para el funcionamiento normal de la web.

28-29/04/26: Nuevo MVC: Resultados. Los resultados se importarán en formato CSV/Excel (procedente de los servicios de cronometraje) y no serán editables una vez publicados.
    La tabla de resultados se creará de forma dinámica con los campos aportados en el CSV, mapea las columnas de dorsal (y tiempo) y obtiene los datos del corredor con ese dorsal.
    Añade panel de control de carreras

30/04/26: Nuevo controlador: contact. Permite enviar un correo electrónico para contactar con Traileros mediante un formulario de contacto.
          Mejora DRY: Métodos protegidos para crear token csrf, comprobar token csrf y manejo de errores que son reutilizables en todos los controladores hijos.
          Toast para notificacion de inicio de sesión en vista principal (que avisa de rellenar datos del perfil si aún no lo has hecho).

05/05/26: Integra varias mejoras:
            - Página de post pago conectada con API para generar código QR, envío de correo electronico de confirmacion con el QR.
            - Gestión de abandonos: elimina automáticamente las inscripciones que se quedan en pendiente (por falta de confirmación de pago). MEJORA -> Configurar server (crontab)
            - Ahora se pueden ordenar las inscripciones por id también.
            - Arreglado fallo que rompía la paginación al ordenar las carreras por cualquier criterio.
            - Ahora le botón de cambiar entre modo oscuro y claro aparece en el header y no en el menú dropdown ya que requería inicio de sesión previo.
            - Posibilidad de pasar a ser usuario organizador mediante una petición pulsando un botón (requiere tener los datos del perfil completos). La petición le llega al panel de control de usuarios de administrador y podrá decidir si aceptarlo o no.
            - Ahora hay un acceso directo en el header al panel de gestión de eventos (para usuarios administrador y organizador) y de usuarios (solo para el admin).

06/05/26: Integración dinámica de tracks de las distintas modalidades de las carreras mediante el visor embebido de wikiloc.

07-08/05/26: Creación y configuración del server para el despliegue usando AWS (free-tier). Finalización y entrega de la documentación del proyecto. Readme de proyecto y del despliegue.

11/05/26: Reorganización de los archivos de funcionalidades de javascript (un único search-AJAX que sirve para todas las vistas).



MEJORAS:
    - En la documentación se podría hacer unas matrices de control de acceso de cada controlador para que se vean los permisos de una manera mas visual. PE:
    Método,Usuario (3),Organizador (2),Admin (1),Condición Extra
    render,✅,✅,✅,Filtro en SQL (ya lo tienes)
    create,✅,✅,✅,-
    edit,❌,✅,✅,"Si es Org, solo sus eventos"
    delete,❌,❌,✅,-
    show,✅,✅,✅,Validar propiedad del registro
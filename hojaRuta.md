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

13/04-17/04: Refactorizacion tablas bd

22/04/26: Integra pasarela de pago con Stripe (Modo sandbox)


MEJORAS:
    - En la documentación se podría hacer unas matrices de control de acceso de cada controlador para que se vean los permisos de una manera mas visual. PE:
    Método,Usuario (3),Organizador (2),Admin (1),Condición Extra
    render,✅,✅,✅,Filtro en SQL (ya lo tienes)
    create,✅,✅,✅,-
    edit,❌,✅,✅,"Si es Org, solo sus eventos"
    delete,❌,❌,✅,-
    show,✅,✅,✅,Validar propiedad del registro
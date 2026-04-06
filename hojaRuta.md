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
Esto habría que comprobar si sigue funcionando en un hosting (AWS)
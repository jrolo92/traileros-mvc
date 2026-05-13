# 🗺️ Hoja de Ruta del Proyecto: Traileros

Registro detallado de hitos, mejoras técnicas y despliegue del sistema de gestión de carreras de montaña.

---

## 📅 Abril 2026

### Semana 1: Rendimiento y UI
* **06/04/2026** - **Optimización de Imágenes:**
    * Habilitación de la librería `GD` en `php.ini` (`extension=gd`) para el redimensionador de imágenes.
    * *Nota:* Pendiente verificar compatibilidad en entorno AWS.
* **07/04/2026 - 08/04/2026** - **Interfaz de Usuario:**
    * Implementación completa del **Modo Oscuro** (Dark Mode).

### Semana 2: Core de Inscripciones
* **09/04/2026** - **Lógica MVC:**
    * Creación del sistema de inscripciones bajo patrón MVC.
    * Simulación de pasarela de pago (planteamiento de integración real).
* **10/04/2026** - **Vistas de Inscripción:**
    * Desarrollo de las interfaces `main` y `new` para el usuario.
* **13/04/2026 - 17/04/2026** - **Refactorización de Datos:**
    * Reestructuración de la BD: Introducción de la tabla `modalidades` para mayor escalabilidad.

### Semana 3: Integraciones Externas
* **22/04/2026** - **Pagos:**
    * Integración de pasarela de pago con **Stripe** (Entorno Sandbox).
* **23/04/2026** - **UX & AJAX:**
    * Implementación de paginación en la vista de carreras.
    * Refactorización de sistemas de búsqueda y ordenación mediante **AJAX** (carga asíncrona).
* **24/04/2026** - **Comunicaciones (PHPMailer):**
    * Automatización de correos electrónicos para:
        * Registro de cuenta y cambio/olvido de contraseña.
        * Confirmación de inscripción.
        * Notificación de eliminación de inscripciones.

### Semana 4: Estructura y Resultados
* **27/04/2026** - **Refactorización BD y Reportes:**
    * Migración de `edad_max` y `edad_min` de Eventos a Modalidades.
    * Nuevos campos en Eventos: `fecha_fin_inscripciones` y `estado`.
    * Integración de exportación a **PDF** mediante la librería **FPDF**.
* **28/04/2026 - 29/04/2026** - **Módulo de Resultados:**
    * Nuevo MVC: Resultados.
    * Importación dinámica desde **CSV/Excel** con mapeo de dorsales y tiempos.
    * Creación del Panel de Control de Carreras para organizadores.
* **30/04/2026** - **Consolidación y Contacto:**
    * Nuevo controlador `Contact` (Formulario de contacto).
    * **Refactorización DRY:** Métodos protegidos en el controlador base para tokens CSRF y manejo de errores.
    * Sistema de notificaciones *Toast* para flujo de login y perfil incompleto.

---

## 📅 Mayo 2026

### Semana 1: Mejoras Pro y Lógica de Negocio
* **05/05/2026** - **Sprint de Mejoras:**
    * **Post-pago:** Generación automática de **Códigos QR** y envío por email.
    * **Mantenimiento:** Lógica de limpieza de inscripciones pendientes (Abandono de carrito). *Configurar Crontab*.
    * **Corrección de Bugs:** Fix en paginación vinculada a ordenación.
    * **Accesibilidad:** Cambio de tema movido al header (acceso público).
    * **Roles:** Sistema de peticiones para pasar a rol "Organizador" con aprobación del Admin.
* **06/05/2026** - **Mapas y Tracks:**
    * Integración de visualización de tracks mediante visor embebido de **Wikiloc**.

### Semana 2: Despliegue y Cierre
* **07/05/2026 - 08/05/2026** - **Infraestructura AWS:**
    * Configuración de servidor en **AWS (Free Tier)**.
    * Redacción de documentación técnica: README de proyecto y guía de despliegue.
* **11/05/2026** - **Limpieza de Código:**
    * Reorganización final de JS: Unificación de `search-AJAX` como módulo global.
* **12/05/2026** - **QA & Testing:**
    * Pruebas de estrés y funcionales de todo el sistema. Corrección de errores de última hora (case sensitive de linux en las consultas de los modelos).

---

# Matriz de Privilegios y Permisos

Esta tabla resume la configuración de seguridad definida en el sistema para los tres niveles de acceso.

## Roles del Sistema
- **Rol 1 (Super-Admin):** Control total del sistema, incluyendo borrado físico.
- **Rol 2 (Gestor/Organizador):** Gestión operativa de eventos, inscripciones y resultados.
- **Rol 3 (Usuario/Corredor):** Acceso a participación y gestión de perfil personal.

---

### 1. Gestión de Carreras (Eventos)
| Acción | Descripción | Rol 1 | Rol 2 | Rol 3 |
| :--- | :--- | :---: | :---: | :---: |
| `render` / `show` | Listar y ver detalles de carreras | ✅ | ✅ | ✅ |
| `search` / `order` | Filtrar y ordenar el listado | ✅ | ✅ | ✅ |
| `inscribir` | Acceso al botón de inscripción | ✅ | ✅ | ✅ |
| `new` / `create` | Crear nuevas carreras | ✅ | ✅ | ❌ |
| `edit` / `update` | Modificar datos de una carrera | ✅ | ✅ | ❌ |
| `gestion` | Panel de administración de la carrera | ✅ | ✅ | ❌ |
| `delete` | Borrado definitivo de la carrera | ✅ | ✅ | ❌ |

---

### 2. Gestión de Inscripciones
| Acción | Descripción | Rol 1 | Rol 2 | Rol 3 |
| :--- | :--- | :---: | :---: | :---: |
| `new` / `create` | Realizar una nueva inscripción | ✅ | ✅ | ✅ |
| `render` / `show` | Ver listado de inscripciones propias/globales | ✅ | ✅ | ✅ |
| `search` / `order` | Buscar en el listado de inscritos | ✅ | ✅ | ✅ |
| `edit` / `update` | Modificar datos de una inscripción | ✅ | ✅ | ❌ |
| `cancel` | Dar de baja una inscripción (lógica) | ✅ | ✅ | ❌ |
| `export` / `participantes` | Descargar listados en CSV/PDF | ✅ | ✅ | ❌ |
| `delete` | Borrado físico del registro | ✅ | ❌ | ❌ |

---

### 3. Resultados
| Acción | Descripción | Rol 1 | Rol 2 | Rol 3 |
| :--- | :--- | :---: | :---: | :---: |
| `render` | Ver clasificaciones oficiales | ✅ | ✅ | ✅ |
| `pre_import` | Acceso a la herramienta de subida de tiempos | ✅ | ✅ | ❌ |

---

### 4. Usuarios y Perfil (Account)
| Acción | Descripción | Rol 1 | Rol 2 | Rol 3 |
| :--- | :--- | :---: | :---: | :---: |
| **Módulo User** | Gestión total de otros usuarios (CRUD) | ✅ | ❌ | ❌ |
| `account/render` | Ver mi propio perfil | ✅ | ✅ | ✅ |
| `account/edit` | Modificar mis datos personales | ✅ | ✅ | ✅ |
| `account/password` | Cambiar mi contraseña | ✅ | ✅ | ✅ |
| `delete_confirmed` | Eliminar mi propia cuenta | ✅ | ✅ | ✅ |

---
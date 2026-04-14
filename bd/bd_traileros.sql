-- 1. Creación de la Base de Datos
CREATE DATABASE IF NOT EXISTS traileros;
USE traileros;

-- 2. Tablas de Gestión de Usuarios y Roles
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Credenciales y sistema 
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password CHAR(60) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    
    -- Datos Personales
    apellidos VARCHAR(100),
    sexo ENUM('H', 'M', 'Otro'),
    fecha_nacimiento DATE,
    dni VARCHAR(12) UNIQUE,
    
    -- Datos de Contacto
    telefono VARCHAR(15),
    telefono_emergencia VARCHAR(15),
    direccion VARCHAR(255),
    poblacion VARCHAR(100),
    provincia VARCHAR(50),
    codigo_postal VARCHAR(10),
    pais VARCHAR(50) DEFAULT 'España',
    
    -- Datos Deportivos
    club VARCHAR(100),
    talla_camiseta ENUM('XS', 'S', 'M', 'L', 'XL', 'XXL'),
    federado BOOLEAN DEFAULT FALSE,
    num_licencia VARCHAR(50),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS roles(
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20),
    description VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS roles_users(
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED,
    role_id INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. Entidades del Dominio (Traileros)
DROP TABLE IF EXISTS Categorias;
CREATE TABLE IF NOT EXISTS Categorias (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    edad_min INT UNSIGNED DEFAULT 0,
    edad_max INT UNSIGNED DEFAULT 99,
    sexo ENUM('H', 'M', 'Mixto') DEFAULT 'Mixto'
);

CREATE TABLE IF NOT EXISTS Eventos (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200) NOT NULL,
    fecha DATE NOT NULL,
    ubicacion VARCHAR(255),
    distancia DECIMAL(10, 2),
    desnivel INT,
    dificultad VARCHAR(20),
    descripcion TEXT,
    cupo_maximo INT UNSIGNED DEFAULT 100,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    edad_minima TINYINT UNSIGNED DEFAULT 18,
    edad_maxima TINYINT UNSIGNED DEFAULT 99,
    imagen VARCHAR(255),
    organizador_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (organizador_id) REFERENCES users(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS Inscripciones;
CREATE TABLE IF NOT EXISTS Inscripciones (
    user_id INT UNSIGNED,
    evento_id INT UNSIGNED,
    categoria_id INT UNSIGNED,
    fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    dorsal INT UNSIGNED NULL,
    metodo_pago VARCHAR(50),
    estado_pago ENUM('pendiente', 'completado', 'fallido', 'cancelado') DEFAULT 'pendiente',
    precio_final DECIMAL(10,2),
    PRIMARY KEY (user_id, evento_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES Eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES Categorias(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Resultados (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tiempo TIME,
    posicion INT,
    user_id INT UNSIGNED NOT NULL,
    evento_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES Eventos(id) ON DELETE CASCADE
);

-- 4. Inserción de Datos Iniciales
-- Roles básicos
INSERT INTO roles (name, description) VALUES 
('Admin', 'Acceso total al sistema'),
('Organizador', 'Puede crear y gestionar eventos'),
('Corredor', 'Puede inscribirse en carreras y ver resultados');

INSERT INTO users (id, name, email, password) VALUES 
(1, 'Admin', 'admin@traileros.com', '*84AAC12F54AB666ECFC2A83C676908C8BBC381B1'),
(2, 'Javi', 'javi@traileros.com', '*84AAC12F54AB666ECFC2A83C676908C8BBC381B1');

INSERT INTO roles_users (user_id, role_id) VALUES (1, 1), (2, 3);

-- Carreras
INSERT INTO Eventos (id, nombre, fecha, ubicacion, distancia, desnivel, dificultad, descripcion, cupo_maximo, precio, imagen, organizador_id)
VALUES 
(1, 'Gran Vuelta Valle del Genal', '2025-10-25', 'Pujerra, Málaga', 55.00, 2900, 'Alta', 'Recorrido circular de 55 km...', 500, 45.00, 'genal.jpg', 1),
(2, 'CXM La Toleta', '2025-11-23', 'Puerto Serrano, Cádiz', 29.00, 1650, 'Media', 'Carrera por montaña...', 300, 25.00, 'cxm-toleta.jpg', 1),
(3, 'Víboras Trail Algodonales', '2025-02-01', 'Algodonales, Cádiz', 42.00, 4200, 'Alta', 'Atraviesa la Sierra de Líjar...', 400, 35.00, 'viboras.jpg', 1),
(4, '101 Km de Ronda', '2025-05-10', 'Ronda, Málaga', 101.00, 2500, 'Alta', 'Mítica prueba organizada por La Legión...', 4000, 65.00, '101km.jpg', 1),
(5, 'Ultra Trail Sierra de los Bandoleros', '2025-03-07', 'Prado del Rey, Cádiz', 82.00, 4500, 'Muy Alta', 'Recorrido épico...', 600, 55.00, 'bandoleros.jpeg', 1),
(6, 'XIV Pinsapo Trail', '2026-03-21', 'Yunquera, Málaga', 29.00, 1200, 'Media', 'Transita por el Parque Nacional...', 350, 28.00, 'yunquera.jpeg', 1);

-- Categorias
INSERT INTO Categorias (nombre, edad_min, edad_max, sexo) VALUES 
-- Jóvenes
('Juvenil Masculino', 17, 18, 'H'), 
('Juvenil Femenino', 17, 18, 'M'),
('Junior Masculino', 19, 20, 'H'), 
('Junior Femenino', 19, 20, 'M'),
-- Subcategorías (Todos estos son también 'Absolutos')
('Promesa Masculino', 21, 23, 'H'), 
('Promesa Femenino', 21, 23, 'M'),
('Senior Masculino', 24, 39, 'H'), 
('Senior Femenino', 24, 39, 'M'),
('Veterano A', 40, 49, 'H'),
('Veterana A', 40, 49, 'M'),
('Veterano B', 50, 59, 'H'),
('Veterana B', 50, 59, 'M'),
('Veterano C', 60, 99, 'H'),
('Veterana C', 60, 99, 'M');

ALTER TABLE Inscripciones 
MODIFY COLUMN estado_pago ENUM('pendiente', 'completado', 'fallido', 'cancelado') 
DEFAULT 'pendiente';
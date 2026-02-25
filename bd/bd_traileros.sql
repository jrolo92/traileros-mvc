-- 1. Creación de la Base de Datos
CREATE DATABASE IF NOT EXISTS traileros;
USE traileros;

-- 2. Tablas de Gestión de Usuarios y Roles
CREATE TABLE IF NOT EXISTS users(
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    email VARCHAR(50) UNIQUE,
    password CHAR(60),
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
CREATE TABLE IF NOT EXISTS Categorias (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
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
    imagenUrl VARCHAR(255),
    organizador_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (organizador_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Inscripciones (
    user_id INT UNSIGNED,
    evento_id INT UNSIGNED,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    metodo_de_pago VARCHAR(50),
    PRIMARY KEY (user_id, evento_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES Eventos(id) ON DELETE CASCADE
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

INSERT INTO users (id, name, email, password) 
VALUES (1, 'Admin', 'admin@trail.com', '12345678');

-- Carreras
INSERT INTO Eventos (id, nombre, fecha, ubicacion, distancia, desnivel, dificultad, descripcion, imagenUrl, organizador_id)
VALUES 
(1, 'Gran Vuelta Valle del Genal', '2025-10-25', 'Pujerra, Málaga', 55.00, 2900, 'Alta', 'Recorrido circular de 55 km por el Alto Valle del Genal con 2900 m de desnivel positivo.', 'assets/images/genal.jpg', 1),
(2, 'CXM La Toleta', '2025-11-23', 'Puerto Serrano, Cádiz', 29.00, 1650, 'Media', 'Carrera por montaña que recorre los parajes naturales de Puerto Serrano.', 'assets/images/cxm-toleta.jpg', 1),
(3, 'Víboras Trail Algodonales', '2025-02-01', 'Algodonales, Cádiz', 42.00, 4200, 'Alta', 'Atraviesa la Sierra de Líjar con salida en la Plaza de la Constitución.', 'assets/images/viboras.jpg', 1),
(4, '101 Km de Ronda', '2025-05-10', 'Ronda, Málaga', 101.00, 2500, 'Alta', 'Mítica prueba organizada por La Legión que recorre 101 km por la Serranía de Ronda.', 'assets/images/101km.jpg', 1),
(5, 'Ultra Trail Sierra de los Bandoleros', '2025-03-07', 'Prado del Rey, Cádiz', 82.00, 4500, 'Muy Alta', 'Recorrido épico por la Sierra de Grazalema, con tramos nocturnos y desniveles extremos.', 'assets/images/bandoleros.jpeg', 1),
(6, 'XIV Pinsapo Trail', '2026-03-21', 'Yunquera, Málaga', 29.00, 1200, 'Media', 'Transita por el Parque Nacional Sierra de las Nieves y sus pinsapares.', 'assets/images/yunquera.jpeg', 1);
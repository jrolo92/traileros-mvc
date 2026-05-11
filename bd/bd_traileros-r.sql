CREATE DATABASE IF NOT EXISTS traileros DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE traileros;

-- 1. TABLA ROLES
DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(20) DEFAULT NULL,
  description varchar(100) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  update_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (id, name, description) VALUES 
(1,'Admin','Acceso total al sistema'),
(2,'Organizador','Puede crear y gestionar eventos'),
(3,'Corredor','Puede inscribirse en carreras y ver resultados');

-- 2. TABLA USERS
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  email varchar(50) NOT NULL,
  password char(60) NOT NULL,
  avatar varchar(255) DEFAULT 'default-avatar.png',
  apellidos varchar(100) DEFAULT NULL,
  sexo enum('H','M','Otro') DEFAULT NULL,
  fecha_nacimiento date DEFAULT NULL,
  dni varchar(12) DEFAULT NULL,
  telefono varchar(15) DEFAULT NULL,
  telefono_emergencia varchar(15) DEFAULT NULL,
  direccion varchar(255) DEFAULT NULL,
  poblacion varchar(100) DEFAULT NULL,
  provincia varchar(50) DEFAULT NULL,
  codigo_postal varchar(10) DEFAULT NULL,
  pais varchar(50) DEFAULT 'España',
  club varchar(100) DEFAULT 'Independiente',
  talla_camiseta enum('XS','S','M','L','XL','XXL') DEFAULT NULL,
  federado tinyint(1) DEFAULT 0,
  num_licencia varchar(50) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  UNIQUE KEY dni (dni)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO users (id, name, email, password, avatar, apellidos, sexo, dni) VALUES 
(1,'Admin','admin@traileros.com','$2y$10$O.lndtwk7CET3HOgDTf8r.AbDtxHAUy.kNBptIid76KBkaiXTHNIO','public/assets/img/avatars/avatar_1_1775460115.png','Admin','H','12345678A'),
(2,'Javier','javi@traileros.com','$2y$10$O.lndtwk7CET3HOgDTf8r.AbDtxHAUy.kNBptIid76KBkaiXTHNIO','public/assets/img/avatars/avatar_3_1775458527.png','Rolo','H','12345678C');

-- 3. TABLA ROLES_USERS
DROP TABLE IF EXISTS roles_users;
CREATE TABLE roles_users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned DEFAULT NULL,
  role_id int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles_users (user_id, role_id) VALUES (2,3), (1,1);

DROP TABLE IF EXISTS upgrade_requests;
CREATE TABLE upgrade_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT(10) unsigned NOT NULL,
    status ENUM('pendiente', 'aprobado', 'denegado') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. TABLA CATEGORIAS
DROP TABLE IF EXISTS categorias;
CREATE TABLE categorias (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  nombre varchar(100) NOT NULL,
  edad_min int(10) unsigned DEFAULT 0,
  edad_max int(10) unsigned DEFAULT 99,
  sexo enum('H','M','Mixto') DEFAULT 'Mixto',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categorias (id, nombre, edad_min, edad_max, sexo) VALUES 
(1,'Juvenil Masculino',17,18,'H'),(2,'Juvenil Femenino',17,18,'M'),(3,'Junior Masculino',19,20,'H'),(4,'Junior Femenino',19,20,'M'),
(5,'Promesa Masculino',21,23,'H'),(6,'Promesa Femenino',21,23,'M'),(7,'Senior Masculino',24,39,'H'),(8,'Senior Femenino',24,39,'M'),
(9,'Veterano A',40,49,'H'),(10,'Veterana A',40,49,'M'),(11,'Veterano B',50,59,'H'),(12,'Veterana B',50,59,'M'),
(13,'Veterano C',60,99,'H'),(14,'Veterana C',60,99,'M');

-- 5. TABLA EVENTOS
DROP TABLE IF EXISTS eventos;
CREATE TABLE eventos (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  nombre varchar(200) NOT NULL,
  fecha date NOT NULL,
  fecha_cierre_inscripcion datetime DEFAULT NULL,
  ubicacion varchar(255) DEFAULT NULL,
  dificultad varchar(20) DEFAULT NULL,
  descripcion text DEFAULT NULL,
  imagen varchar(255) DEFAULT NULL,
  organizador_id int(10) unsigned NOT NULL,
  estado enum('borrador', 'abierto', 'cerrado', 'finalizado', 'cancelado') DEFAULT 'borrador',
  PRIMARY KEY (id),
  FOREIGN KEY (organizador_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO eventos (id, nombre, fecha, ubicacion, dificultad, descripcion, imagen, organizador_id) VALUES 
(1,'Gran Vuelta Valle del Genal','2025-10-25','Pujerra, Málaga','Alta','Recorrido circular...','genal.jpg',1),
(2,'CXM La Toleta','2025-11-23','Puerto Serrano, Cádiz','Media','Carrera por montaña...','cxm-toleta.jpg',1),
(3,'Víboras Trail Algodonales','2025-02-01','Algodonales, Cádiz','Alta','Atraviesa la Sierra...','viboras.jpg',1),
(4,'101 Km de Ronda','2025-05-10','Ronda, Málaga','Alta','Mítica prueba...','101km.jpg',1),
(5,'Ultra Trail Sierra de los Bandoleros','2025-03-07','Prado del Rey, Cádiz','Muy Alta','Recorrido épico...','bandoleros.jpeg',1),
(6,'XIV Pinsapo Trail','2026-03-21','Yunquera, Málaga','Media','Parque Nacional...','yunquera.jpeg',1),
(7,'XIII Trail Dolmen del Gigante','2026-04-25','El Gastor, Cádiz','Baja','16 km...','64c3b512015f5ae9041cae9da3a54ace.jpeg',1),
(8,'XI Trail Moros y Cristianos','2026-04-11','Benamahoma, Cádiz','Media','17 km...','f187f12d1032c871b905dedd37d3aa38.jpg',1);

-- 6. TABLA MODALIDADES
DROP TABLE IF EXISTS modalidades;
CREATE TABLE modalidades (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  evento_id int(10) unsigned NOT NULL,
  nombre varchar(100) NOT NULL,
  distancia decimal(10,2) DEFAULT NULL,
  desnivel int(11) DEFAULT NULL,
  precio decimal(10,2) NOT NULL DEFAULT 0.00,
  cupo_maximo int(10) unsigned DEFAULT 100,
  edad_minima tinyint(3) unsigned DEFAULT 18,
  edad_maxima tinyint(3) unsigned DEFAULT 99,
  track_url VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (evento_id) REFERENCES eventos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO modalidades (evento_id, nombre, distancia, desnivel, precio, cupo_maximo) VALUES 
(1, 'Ultra Genal', 55.00, 2900, 45.00, 500),
(2, 'Trail La Toleta', 29.00, 1650, 25.00, 300),
(3, 'Víboras Trail', 42.00, 4200, 35.00, 400),
(4, '101km Ronda', 101.00, 2500, 65.00, 4000),
(5, 'UTSB Bandoleros', 82.00, 4500, 55.00, 600),
(6, 'CxM', 29.00, 1200, 28.00, 350),
(6, 'Open', 16.00, 900, 18.00, 200),
(7, 'Trail Dolmen', 16.00, 550, 20.00, 100),
(8, 'Trail', 17.00, 695, 13.00, 100),  
(8, 'Sprint Trail', 12.00, 495, 9.00, 100);

-- 7. TABLA INSCRIPCIONES
DROP TABLE IF EXISTS inscripciones;
CREATE TABLE inscripciones (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  evento_id int(10) unsigned NOT NULL,
  modalidad_id int(10) unsigned NOT NULL,
  categoria_id int(10) unsigned DEFAULT NULL,
  fecha_inscripcion datetime DEFAULT current_timestamp(),
  dorsal int(10) unsigned DEFAULT NULL,
  id_pago varchar(255) DEFAULT NULL,
  metodo_pago varchar(50) DEFAULT NULL,
  estado_pago enum('pendiente','completado','fallido','cancelado') DEFAULT 'pendiente',
  precio_final decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY `usuario_evento_unico` (`user_id`, `evento_id`),
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (evento_id) REFERENCES eventos (id) ON DELETE CASCADE,
  FOREIGN KEY (modalidad_id) REFERENCES modalidades (id) ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO inscripciones (user_id, evento_id, modalidad_id, categoria_id, fecha_inscripcion, dorsal, metodo_pago, estado_pago, precio_final) VALUES 
(1, 7, 7, 7, '2026-04-14 13:16:39', 1, 'tarjeta', 'completado', 20.00),
(1, 8, 8, 7, '2026-04-10 13:16:13', 1, 'bizum', 'completado', 13.00);

-- 8. TABLA RESULTADOS
DROP TABLE IF EXISTS resultados;
CREATE TABLE resultados (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  inscripcion_id int(10) unsigned UNIQUE NOT NULL,
  tiempo TIME DEFAULT NULL,
  posicion_general int(11) DEFAULT NULL,
  posicion_categoria int(11) DEFAULT NULL,
  estado enum('FINISHER', 'DNS', 'DNF', 'DNP', 'DESC') DEFAULT 'DNS',
  ritmo_medio TIME DEFAULT NULL,
  comentarios TEXT DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (inscripcion_id) REFERENCES inscripciones (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
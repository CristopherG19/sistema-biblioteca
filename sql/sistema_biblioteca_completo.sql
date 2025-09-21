-- =====================================================
-- SISTEMA DE BIBLIOTECA COMPLETO - EXTRACCIÓN TOTAL
-- Generado automáticamente: 2025-09-21 21:29:59
-- Base de datos: biblioteca_db
-- Tablas: 13 | Procedimientos: 66
-- Funciones: 0 | Triggers: 3 | Vistas: 2
-- =====================================================

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. CREACIÓN DE BASE DE DATOS
-- =====================================================

CREATE DATABASE IF NOT EXISTS `biblioteca_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `biblioteca_db`;

-- =====================================================
-- 2. CREACIÓN DE TABLAS
-- =====================================================

-- Tabla: categorias
CREATE TABLE `categorias` (
  `idCategoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `activa` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idCategoria`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_activa` (`activa`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: historiallectura
CREATE TABLE `historiallectura` (
  `idHistorial` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idLibro` int NOT NULL,
  `fecha_inicio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `tipo` enum('Prestamo','Lectura','Reserva') COLLATE utf8mb4_unicode_ci DEFAULT 'Prestamo',
  `calificacion` int DEFAULT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idHistorial`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_libro` (`idLibro`),
  KEY `idx_fecha_inicio` (`fecha_inicio`),
  KEY `idx_tipo` (`tipo`),
  CONSTRAINT `historiallectura_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `historiallectura_ibfk_2` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE,
  CONSTRAINT `historiallectura_chk_1` CHECK (((`calificacion` >= 1) and (`calificacion` <= 5)))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: interesesusuario
CREATE TABLE `interesesusuario` (
  `idInteresUsuario` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idCategoria` int NOT NULL,
  `fecha_agregado` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idInteresUsuario`),
  UNIQUE KEY `unique_interes` (`idUsuario`,`idCategoria`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_categoria` (`idCategoria`),
  CONSTRAINT `interesesusuario_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `interesesusuario_ibfk_2` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: libros
CREATE TABLE `libros` (
  `idLibro` int NOT NULL AUTO_INCREMENT,
  `idCategoria` int NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autor` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `editorial` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio` int DEFAULT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `disponible` int NOT NULL DEFAULT '0',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `portada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archivo_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_adicion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  `numero_paginas` int DEFAULT NULL,
  `tamano_archivo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_subida` datetime DEFAULT NULL,
  PRIMARY KEY (`idLibro`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `idx_titulo` (`titulo`),
  KEY `idx_autor` (`autor`),
  KEY `idx_isbn` (`isbn`),
  KEY `idx_categoria` (`idCategoria`),
  KEY `idx_disponible` (`disponible`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`idCategoria`) REFERENCES `categorias` (`idCategoria`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: librosfavoritos
CREATE TABLE `librosfavoritos` (
  `idFavorito` int NOT NULL AUTO_INCREMENT,
  `idUsuario` int NOT NULL,
  `idLibro` int NOT NULL,
  `fecha_agregado` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFavorito`),
  UNIQUE KEY `unique_favorito` (`idUsuario`,`idLibro`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_libro` (`idLibro`),
  CONSTRAINT `librosfavoritos_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `librosfavoritos_ibfk_2` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: multas
CREATE TABLE `multas` (
  `idMulta` int NOT NULL AUTO_INCREMENT,
  `idPrestamo` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `pagada` tinyint(1) DEFAULT '0',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_pago` datetime DEFAULT NULL,
  PRIMARY KEY (`idMulta`),
  KEY `idx_prestamo` (`idPrestamo`),
  KEY `idx_pagada` (`pagada`),
  KEY `idx_fecha_creacion` (`fecha_creacion`),
  CONSTRAINT `multas_ibfk_1` FOREIGN KEY (`idPrestamo`) REFERENCES `prestamos` (`idPrestamo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: prestamos
CREATE TABLE `prestamos` (
  `idPrestamo` int NOT NULL AUTO_INCREMENT,
  `idLibro` int NOT NULL,
  `idUsuario` int NOT NULL,
  `fechaPrestamo` datetime DEFAULT CURRENT_TIMESTAMP,
  `fechaDevolucionEsperada` datetime NOT NULL,
  `fechaDevolucionReal` datetime DEFAULT NULL,
  `estado` enum('Activo','Devuelto','Vencido') COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `multa` decimal(10,2) DEFAULT '0.00',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPrestamo`),
  KEY `idx_libro` (`idLibro`),
  KEY `idx_usuario` (`idUsuario`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_prestamo` (`fechaPrestamo`),
  KEY `idx_fecha_devolucion` (`fechaDevolucionEsperada`),
  KEY `idx_prestamos_usuario_estado` (`idUsuario`,`estado`),
  KEY `idx_prestamos_libro_estado` (`idLibro`,`estado`),
  KEY `idx_prestamos_fecha_estado` (`fechaPrestamo`,`estado`),
  CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`idLibro`) REFERENCES `libros` (`idLibro`) ON DELETE RESTRICT,
  CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: roles
CREATE TABLE `roles` (
  `idRol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: solicitudes_prestamo
CREATE TABLE `solicitudes_prestamo` (
  `idSolicitud` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `libro_id` int NOT NULL,
  `fecha_solicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','Aprobada','Rechazada','Convertida') COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `observaciones_usuario` text COLLATE utf8mb4_unicode_ci,
  `observaciones_bibliotecario` text COLLATE utf8mb4_unicode_ci,
  `fecha_respuesta` datetime DEFAULT NULL,
  `bibliotecario_id` int DEFAULT NULL,
  `prestamo_id` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSolicitud`),
  KEY `prestamo_id` (`prestamo_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_solicitud` (`fecha_solicitud`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_libro` (`libro_id`),
  KEY `idx_bibliotecario` (`bibliotecario_id`),
  KEY `idx_solicitudes_estado_fecha` (`estado`,`fecha_solicitud`),
  CONSTRAINT `solicitudes_prestamo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_prestamo_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`idLibro`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_prestamo_ibfk_3` FOREIGN KEY (`bibliotecario_id`) REFERENCES `usuarios` (`idUsuario`) ON DELETE SET NULL,
  CONSTRAINT `solicitudes_prestamo_ibfk_4` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`idPrestamo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: solicitudesampliacion
CREATE TABLE `solicitudesampliacion` (
  `idSolicitud` int NOT NULL AUTO_INCREMENT,
  `idPrestamo` int NOT NULL,
  `diasAdicionales` int NOT NULL DEFAULT '7',
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `fechaSolicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaRespuesta` datetime DEFAULT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `respuestaBibliotecario` text COLLATE utf8mb4_unicode_ci,
  `idBibliotecario` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSolicitud`),
  KEY `idx_prestamo` (`idPrestamo`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_solicitud` (`fechaSolicitud`),
  KEY `idx_bibliotecario` (`idBibliotecario`),
  KEY `idx_ampliaciones_estado_fecha` (`estado`,`fechaSolicitud`),
  CONSTRAINT `solicitudesampliacion_ibfk_1` FOREIGN KEY (`idPrestamo`) REFERENCES `prestamos` (`idPrestamo`) ON DELETE CASCADE,
  CONSTRAINT `solicitudesampliacion_ibfk_2` FOREIGN KEY (`idBibliotecario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: usuarios
CREATE TABLE `usuarios` (
  `idUsuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuario` (`usuario`),
  KEY `idx_email` (`email`),
  KEY `idx_rol` (`rol`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `roles` (`idRol`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. DATOS INICIALES
-- =====================================================

-- Datos de tabla: categorias (10 registros)
INSERT INTO `categorias` (`idCategoria`, `nombre`, `descripcion`, `fecha_creacion`, `activa`) VALUES
('1', 'Ficción', 'Novelas y cuentos de ficción', '2025-09-19 03:20:05', '1'),
('2', 'Ciencia', 'Libros de ciencias exactas y naturales', '2025-09-19 03:20:05', '1'),
('3', 'Historia', 'Libros de historia universal y local', '2025-09-19 03:20:05', '1'),
('4', 'Tecnología', 'Libros sobre tecnología e informática', '2025-09-19 03:20:05', '1'),
('5', 'Matemáticas', 'Libros de matemáticas y estadística', '2025-09-19 03:20:05', '1'),
('6', 'Literatura', 'Obras literarias clásicas y contemporáneas', '2025-09-19 03:20:05', '1'),
('7', 'Filosofía', 'Libros de filosofía y ética', '2025-09-19 03:20:05', '1'),
('8', 'Arte', 'Libros sobre arte, música y cultura', '2025-09-19 03:20:05', '1'),
('9', 'Deportes', 'Libros sobre deportes y actividad física', '2025-09-19 03:20:05', '1'),
('10', 'categoria de prueba', 'dddddddd', '2025-09-18 22:49:15', '1');

-- Datos de tabla: historiallectura (2 registros)
INSERT INTO `historiallectura` (`idHistorial`, `idUsuario`, `idLibro`, `fecha_inicio`, `fecha_fin`, `tipo`, `calificacion`, `comentario`) VALUES
('1', '1', '8', '2025-09-18 22:55:13', NULL, 'Lectura', NULL, NULL),
('2', '1', '11', '2025-09-18 23:21:36', NULL, 'Lectura', NULL, NULL);

-- Datos de tabla: libros (11 registros)
INSERT INTO `libros` (`idLibro`, `idCategoria`, `titulo`, `autor`, `editorial`, `anio`, `isbn`, `stock`, `disponible`, `descripcion`, `portada`, `archivo_pdf`, `fecha_adicion`, `fecha_actualizacion`, `activo`, `numero_paginas`, `tamano_archivo`, `fecha_subida`) VALUES
('1', '1', 'Cien años de soledad', 'Gabriel García Márquez', 'Editorial Sudamericana', '1967', '978-84-376-0494-7', '5', '5', 'Una de las obras más importantes de la literatura hispanoamericana del siglo XX. Narra la historia de la familia Buendía a lo largo de siete generaciones en el pueblo ficticio de Macondo.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('2', '2', 'Sapiens: De animales a dioses', 'Yuval Noah Harari', 'Debate', '2011', '978-84-9992-424-0', '3', '3', 'Un fascinante relato de cómo el Homo sapiens llegó a dominar el mundo. Una exploración de cómo la evolución de la humanidad ha dado forma al mundo moderno.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('3', '3', 'Breve historia del tiempo', 'Stephen Hawking', 'Crítica', '1988', '978-84-7423-842-0', '4', '4', 'Una exploración de los conceptos fundamentales de la física moderna, desde la teoría de la relatividad hasta la mecánica cuántica.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('4', '4', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Prentice Hall', '2008', '978-0-13-235088-4', '6', '6', 'Una guía práctica para escribir código limpio y mantenible. Incluye principios, patrones y prácticas para mejorar la calidad del código.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('5', '5', 'El mundo de Sofía', 'Jostein Gaarder', 'Siruela', '1991', '978-84-7844-445-2', '3', '1', 'Una novela que introduce a la filosofía de manera accesible a través de la historia de Sofía, una joven que recibe lecciones de filosofía.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 23:28:46', '1', NULL, NULL, NULL),
('6', '6', 'La historia del arte', 'E.H. Gombrich', 'Phaidon', '1950', '978-0-7148-3247-0', '2', '2', 'Una introducción completa y accesible al arte occidental, desde la prehistoria hasta el siglo XX.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('7', '7', 'El hombre que calculaba', 'Malba Tahan', 'Zahar', '1938', '978-85-378-0001-1', '4', '4', 'Una obra que combina matemáticas con literatura, presentando problemas matemáticos de manera entretenida a través de historias.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('8', '9', '1984', 'George Orwell', 'Debolsillo', '1949', '978-84-9908-567-1', '5', '3', 'Una distopía clásica que describe una sociedad totalitaria donde el Gran Hermano vigila constantemente a los ciudadanos.', NULL, 'libro_8_1758254106.pdf', '2025-09-18 22:46:17', '2025-09-18 23:01:03', '1', '1', '235971', '2025-09-18 22:55:08'),
('9', '9', 'Fútbol: La filosofía de vida', 'Jorge Valdano', 'Aguilar', '2014', '978-84-03-01345-6', '3', '3', 'Una reflexión sobre el fútbol como fenómeno social y cultural, escrita por uno de los grandes pensadores del deporte.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('10', '1', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'Real Academia Española', '1605', '978-84-239-7434-8', '7', '7', 'La obra cumbre de la literatura española y una de las más importantes de la literatura universal. Narra las aventuras de Alonso Quijano.', NULL, NULL, '2025-09-18 22:46:17', '2025-09-18 22:46:17', '1', NULL, NULL, NULL),
('11', '2', 'libro prueba', 'autor', 'editorial', '2025', '3213231', '12', '12', 'dsadas', NULL, 'libro_11_1758255685.pdf', '2025-09-18 23:21:25', '2025-09-18 23:21:25', '1', '12', '141049', '2025-09-18 23:21:25');

-- Datos de tabla: prestamos (4 registros)
INSERT INTO `prestamos` (`idPrestamo`, `idLibro`, `idUsuario`, `fechaPrestamo`, `fechaDevolucionEsperada`, `fechaDevolucionReal`, `estado`, `observaciones`, `multa`, `fecha_creacion`, `fecha_actualizacion`) VALUES
('1', '8', '2', '2025-09-19 00:00:00', '2025-10-03 00:00:00', NULL, 'Activo', 'ssssssss', '0.00', '2025-09-18 23:00:15', '2025-09-18 23:00:15'),
('2', '5', '2', '2025-09-19 00:00:00', '2025-10-03 00:00:00', NULL, 'Activo', 'kk', '0.00', '2025-09-18 23:00:39', '2025-09-18 23:00:39'),
('3', '8', '1', '2025-09-19 00:00:00', '2025-10-03 00:00:00', NULL, 'Activo', 'Préstamo de prueba del sistema', '0.00', '2025-09-18 23:01:03', '2025-09-18 23:01:03'),
('4', '5', '2', '2025-09-18 23:28:46', '2025-10-04 00:00:00', NULL, 'Activo', 'calla ctmre', '0.00', '2025-09-18 23:28:46', '2025-09-18 23:28:46');

-- Datos de tabla: roles (2 registros)
INSERT INTO `roles` (`idRol`, `nombre`, `descripcion`, `fecha_creacion`) VALUES
('1', 'Bibliotecario', 'Administrador del sistema con acceso completo', '2025-09-19 03:20:05'),
('2', 'Lector', 'Usuario que puede solicitar y gestionar préstamos', '2025-09-19 03:20:05');

-- Datos de tabla: solicitudes_prestamo (1 registros)
INSERT INTO `solicitudes_prestamo` (`idSolicitud`, `usuario_id`, `libro_id`, `fecha_solicitud`, `estado`, `observaciones_usuario`, `observaciones_bibliotecario`, `fecha_respuesta`, `bibliotecario_id`, `prestamo_id`, `fecha_creacion`, `fecha_actualizacion`) VALUES
('1', '2', '5', '2025-09-18 23:28:28', 'Convertida', '', 'calla ctmre', '2025-09-18 23:28:46', '1', '4', '2025-09-18 23:28:28', '2025-09-18 23:28:46');

-- Datos de tabla: usuarios (2 registros)
INSERT INTO `usuarios` (`idUsuario`, `nombre`, `apellido`, `usuario`, `password`, `rol`, `email`, `telefono`, `direccion`, `fecha_registro`, `ultimo_acceso`, `activo`) VALUES
('1', 'Administrador', 'Sistema', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1', 'admin@biblioteca.com', '123456789', NULL, '2025-09-19 03:20:05', '2025-09-18 22:20:15', '1'),
('2', 'Cristopher Alvaro Gutierrez Chuquiyuri', NULL, 'usuario', '$2y$10$XGE4DHu799eXFCgbm/xZ3.ZO6/50ySsd0fecEZhYJbl.dDTvTJn66', '2', 'cgch_1996@hotmail.com', '945628098', NULL, '2025-09-18 22:45:00', '2025-09-18 22:45:19', '1');

-- Datos de tabla: vista_estadisticas_generales (1 registros)
INSERT INTO `vista_estadisticas_generales` (`total_usuarios`, `total_libros`, `total_prestamos`, `prestamos_activos`, `prestamos_devueltos`, `prestamos_vencidos`, `total_solicitudes`, `solicitudes_pendientes`) VALUES
('2', '11', '4', '4', '0', '0', '1', '0');

-- Datos de tabla: vista_prestamos_activos (4 registros)
INSERT INTO `vista_prestamos_activos` (`idPrestamo`, `fechaPrestamo`, `fechaDevolucionEsperada`, `observaciones`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `libro_titulo`, `libro_autor`, `libro_isbn`, `categoria_nombre`, `dias_restantes`) VALUES
('1', '2025-09-19 00:00:00', '2025-10-03 00:00:00', 'ssssssss', 'Cristopher Alvaro Gutierrez Chuquiyuri', NULL, 'cgch_1996@hotmail.com', '1984', 'George Orwell', '978-84-9908-567-1', 'Deportes', '12'),
('2', '2025-09-19 00:00:00', '2025-10-03 00:00:00', 'kk', 'Cristopher Alvaro Gutierrez Chuquiyuri', NULL, 'cgch_1996@hotmail.com', 'El mundo de Sofía', 'Jostein Gaarder', '978-84-7844-445-2', 'Matemáticas', '12'),
('3', '2025-09-19 00:00:00', '2025-10-03 00:00:00', 'Préstamo de prueba del sistema', 'Administrador', 'Sistema', 'admin@biblioteca.com', '1984', 'George Orwell', '978-84-9908-567-1', 'Deportes', '12'),
('4', '2025-09-18 23:28:46', '2025-10-04 00:00:00', 'calla ctmre', 'Cristopher Alvaro Gutierrez Chuquiyuri', NULL, 'cgch_1996@hotmail.com', 'El mundo de Sofía', 'Jostein Gaarder', '978-84-7844-445-2', 'Matemáticas', '13');

-- =====================================================
-- 4. PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento: sp_actualizar_categoria
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_categoria`(
                IN p_id INT,
                IN p_nombre VARCHAR(100),
                IN p_descripcion TEXT
            )
BEGIN
                UPDATE Categorias 
                SET nombre = p_nombre,
                    descripcion = p_descripcion,
                    fecha_actualizacion = NOW()
                WHERE idCategoria = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_actualizar_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_libro`(
                IN p_id INT,
                IN p_idCategoria INT,
                IN p_titulo VARCHAR(200),
                IN p_autor VARCHAR(100),
                IN p_editorial VARCHAR(100),
                IN p_anio INT,
                IN p_isbn VARCHAR(20),
                IN p_stock INT,
                IN p_disponible INT,
                IN p_descripcion TEXT
            )
BEGIN
                UPDATE Libros 
                SET idCategoria = p_idCategoria,
                    titulo = p_titulo,
                    autor = p_autor,
                    editorial = p_editorial,
                    anio = p_anio,
                    isbn = p_isbn,
                    stock = p_stock,
                    disponible = p_disponible,
                    descripcion = p_descripcion,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_actualizar_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_usuario`(
                IN p_id INT,
                IN p_nombre VARCHAR(100),
                IN p_usuario VARCHAR(50),
                IN p_password VARCHAR(255),
                IN p_rol INT,
                IN p_email VARCHAR(100),
                IN p_telefono VARCHAR(20)
            )
BEGIN
                UPDATE Usuarios 
                SET nombre = p_nombre,
                    usuario = p_usuario,
                    password = p_password,
                    rol = p_rol,
                    email = p_email,
                    telefono = p_telefono,
                    fecha_actualizacion = NOW()
                WHERE idUsuario = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_ampliacion_aprobar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_aprobar`(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    DECLARE v_prestamo_id INT;
    DECLARE v_dias_adicionales INT;
    DECLARE v_fecha_actual DATETIME;
    
    -- Obtener datos de la solicitud
    SELECT idPrestamo, diasAdicionales INTO v_prestamo_id, v_dias_adicionales
    FROM SolicitudesAmpliacion
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    IF v_prestamo_id IS NOT NULL THEN
        -- Obtener fecha actual de devolución
        SELECT fechaDevolucionEsperada INTO v_fecha_actual
        FROM Prestamos
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar fecha de devolución
        UPDATE Prestamos
        SET fechaDevolucionEsperada = DATE_ADD(v_fecha_actual, INTERVAL v_dias_adicionales DAY),
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | Ampliación: Ampliado por ', v_dias_adicionales, ' días. Motivo: ', p_respuesta)
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar solicitud
        UPDATE SolicitudesAmpliacion
        SET estado = 'Aprobada',
            idBibliotecario = p_bibliotecario_id,
            respuestaBibliotecario = p_respuesta,
            fechaRespuesta = NOW()
        WHERE idSolicitud = p_solicitud_id;
        
        SELECT 'success' as status, CONVERT('Ampliación aprobada exitosamente' USING utf8mb4) as message;
    ELSE
        SELECT 'error' as status, 'Solicitud no encontrada o ya procesada' as message;
    END IF;
END

-- Procedimiento: sp_ampliacion_obtener_solicitudes
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_obtener_solicitudes`(IN p_estado VARCHAR(20))
BEGIN
    SELECT 
        sa.idSolicitud,
        sa.idPrestamo,
        sa.diasAdicionales,
        sa.motivo,
        sa.fechaSolicitud,
        sa.fechaRespuesta,
        sa.estado,
        sa.respuestaBibliotecario,
        sa.idBibliotecario,
        p.idUsuario,
        u.nombre as usuario_nombre,
        u.apellido as usuario_apellido,
        u.email as usuario_email,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        p.fechaPrestamo,
        p.fechaDevolucionEsperada,
        b.nombre as bibliotecario_nombre,
        b.apellido as bibliotecario_apellido
    FROM SolicitudesAmpliacion sa
    INNER JOIN Prestamos p ON sa.idPrestamo = p.idPrestamo
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    LEFT JOIN Usuarios b ON sa.idBibliotecario = b.idUsuario
    WHERE (p_estado IS NULL OR sa.estado = p_estado)
    ORDER BY sa.fechaSolicitud DESC;
END

-- Procedimiento: sp_ampliacion_rechazar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_rechazar`(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    UPDATE SolicitudesAmpliacion
    SET estado = 'Rechazada',
        idBibliotecario = p_bibliotecario_id,
        respuestaBibliotecario = p_respuesta,
        fechaRespuesta = NOW()
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    SELECT ROW_COUNT() as affected_rows;
END

-- Procedimiento: sp_ampliacion_solicitar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_solicitar`(
    IN p_prestamo_id INT,
    IN p_dias_adicionales INT,
    IN p_motivo TEXT
)
BEGIN
    DECLARE v_existe INT DEFAULT 0;
    
    -- Verificar que el préstamo existe y está activo
    SELECT COUNT(*) INTO v_existe
    FROM Prestamos
    WHERE idPrestamo = p_prestamo_id AND fechaDevolucionReal IS NULL;
    
    IF v_existe > 0 THEN
        INSERT INTO SolicitudesAmpliacion (idPrestamo, diasAdicionales, motivo, estado, fechaSolicitud)
        VALUES (p_prestamo_id, p_dias_adicionales, p_motivo, 'Pendiente', NOW());
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status, 'Solicitud de ampliación enviada' as message;
    ELSE
        SELECT 0 as idSolicitud, 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    END IF;
END

-- Procedimiento: sp_categoria_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_obtener_por_id`(IN p_id INT)
BEGIN
                SELECT idCategoria, nombre, descripcion, fecha_creacion, activa
                FROM Categorias
                WHERE idCategoria = p_id AND activa = TRUE;
            END

-- Procedimiento: sp_eliminar_categoria
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_categoria`(IN p_id INT)
BEGIN
                UPDATE Categorias 
                SET activa = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idCategoria = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_eliminar_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_libro`(IN p_id INT)
BEGIN
                UPDATE Libros 
                SET activo = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_eliminar_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_usuario`(IN p_id INT)
BEGIN
                UPDATE Usuarios 
                SET activo = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idUsuario = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_insertar_categoria
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_categoria`(
                IN p_nombre VARCHAR(100),
                IN p_descripcion TEXT
            )
BEGIN
                INSERT INTO Categorias (nombre, descripcion)
                VALUES (p_nombre, p_descripcion);
                SELECT LAST_INSERT_ID() as idCategoria;
            END

-- Procedimiento: sp_insertar_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_libro`(
                IN p_idCategoria INT,
                IN p_titulo VARCHAR(200),
                IN p_autor VARCHAR(100),
                IN p_editorial VARCHAR(100),
                IN p_anio INT,
                IN p_isbn VARCHAR(20),
                IN p_stock INT,
                IN p_disponible INT,
                IN p_descripcion TEXT
            )
BEGIN
                INSERT INTO Libros (idCategoria, titulo, autor, editorial, anio, isbn, stock, disponible, descripcion)
                VALUES (p_idCategoria, p_titulo, p_autor, p_editorial, p_anio, p_isbn, p_stock, p_disponible, p_descripcion);
                SELECT LAST_INSERT_ID() as idLibro;
            END

-- Procedimiento: sp_insertar_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_usuario`(
                IN p_nombre VARCHAR(100),
                IN p_usuario VARCHAR(50),
                IN p_password VARCHAR(255),
                IN p_rol INT,
                IN p_email VARCHAR(100),
                IN p_telefono VARCHAR(20)
            )
BEGIN
                INSERT INTO Usuarios (nombre, usuario, password, rol, email, telefono)
                VALUES (p_nombre, p_usuario, p_password, p_rol, p_email, p_telefono);
                SELECT LAST_INSERT_ID() as idUsuario;
            END

-- Procedimiento: sp_libros_disponibles_solicitud
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libros_disponibles_solicitud`()
BEGIN
        SELECT 
            l.idLibro,
            l.titulo,
            l.autor,
            l.editorial,
            l.anio,
            l.isbn,
            l.disponible,
            l.descripcion,
            c.nombre as categoria_nombre
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.disponible > 0
        ORDER BY l.titulo;
    END

-- Procedimiento: sp_libro_actualizar_pdf
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_pdf`(
        IN p_id INT,
        IN p_archivo_pdf VARCHAR(255),
        IN p_numero_paginas INT,
        IN p_tamano_archivo VARCHAR(50)
    )
BEGIN
        UPDATE Libros 
        SET archivo_pdf = p_archivo_pdf,
            numero_paginas = p_numero_paginas,
            tamano_archivo = p_tamano_archivo,
            fecha_subida = NOW(),
            fecha_actualizacion = NOW()
        WHERE idLibro = p_id;
        
        SELECT ROW_COUNT() as affected_rows;
    END

-- Procedimiento: sp_libro_actualizar_stock_devolucion
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_stock_devolucion`(IN p_id INT)
BEGIN
                UPDATE Libros 
                SET disponible = disponible + 1,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                
                SELECT 'success' as status, 'Stock actualizado' as message;
            END

-- Procedimiento: sp_libro_actualizar_stock_prestamo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_stock_prestamo`(IN p_id INT)
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                
                SELECT disponible INTO v_disponible
                FROM Libros
                WHERE idLibro = p_id;
                
                IF v_disponible > 0 THEN
                    UPDATE Libros 
                    SET disponible = disponible - 1,
                        fecha_actualizacion = NOW()
                    WHERE idLibro = p_id;
                    
                    SELECT 'success' as status, 'Stock actualizado' as message;
                ELSE
                    SELECT 'error' as status, 'No hay ejemplares disponibles' as message;
                END IF;
            END

-- Procedimiento: sp_libro_buscar_por_titulo_autor
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_buscar_por_titulo_autor`(IN p_termino VARCHAR(100))
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.activo = TRUE
    AND (l.titulo LIKE CONCAT('%', p_termino, '%')
         OR l.autor LIKE CONCAT('%', p_termino, '%')
         OR l.isbn LIKE CONCAT('%', p_termino, '%'))
    ORDER BY l.titulo;
END

-- Procedimiento: sp_libro_obtener_con_detalle_pdf
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_con_detalle_pdf`(IN p_id INT)
BEGIN
        SELECT l.*, c.nombre as categoria_nombre,
               CASE WHEN l.archivo_pdf IS NOT NULL AND l.archivo_pdf <> '' THEN 1 ELSE 0 END as tiene_pdf,
               l.archivo_pdf, 
               l.numero_paginas, 
               l.tamano_archivo, 
               l.fecha_subida
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.idLibro = p_id AND l.activo = TRUE;
    END

-- Procedimiento: sp_libro_obtener_con_prestamos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_con_prestamos`()
BEGIN
    SELECT l.*, c.nombre as categoria,
           COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
           COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
    WHERE l.activo = TRUE
    GROUP BY l.idLibro
    ORDER BY l.titulo;
END

-- Procedimiento: sp_libro_obtener_disponibles
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_disponibles`(IN p_limite INT)
BEGIN
    DECLARE v_limite INT DEFAULT 1000;
    
    -- Asignar el valor de p_limite a v_limite, usando 1000 si p_limite es NULL
    SET v_limite = COALESCE(p_limite, 1000);
    
    SELECT l.*, c.nombre AS categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.disponible > 0 AND l.activo = TRUE
    ORDER BY l.titulo
    LIMIT v_limite;
END

-- Procedimiento: sp_libro_obtener_estadisticas_pdf
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_estadisticas_pdf`()
BEGIN
        SELECT 
            COUNT(*) as total_libros,
            COUNT(CASE WHEN archivo_pdf IS NOT NULL AND archivo_pdf <> '' THEN 1 END) as libros_con_pdf,
            COUNT(CASE WHEN archivo_pdf IS NULL OR archivo_pdf = '' THEN 1 END) as libros_sin_pdf,
            AVG(CASE WHEN numero_paginas IS NOT NULL THEN numero_paginas END) as promedio_paginas,
            SUM(CASE WHEN tamano_archivo IS NOT NULL THEN 
                CAST(REPLACE(REPLACE(tamano_archivo, 'MB', ''), 'KB', '') AS DECIMAL(10,2)) 
            END) as tamano_total_mb
        FROM Libros 
        WHERE activo = TRUE;
    END

-- Procedimiento: sp_libro_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.idLibro = p_id AND l.activo = TRUE;
END

-- Procedimiento: sp_libro_obtener_por_isbn
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_por_isbn`(IN p_isbn VARCHAR(20))
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.isbn = p_isbn AND l.activo = TRUE;
            END

-- Procedimiento: sp_libro_obtener_recientes
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_recientes`(IN p_limite INT)
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.activo = TRUE
                ORDER BY l.idLibro DESC
                LIMIT p_limite;
            END

-- Procedimiento: sp_libro_registrar_lectura
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_registrar_lectura`(IN p_libro_id INT, IN p_usuario_id INT)
BEGIN
    INSERT INTO HistorialLectura (idUsuario, idLibro, tipo)
    VALUES (p_usuario_id, p_libro_id, 'Lectura');
    
    SELECT LAST_INSERT_ID() as id_historial;
END

-- Procedimiento: sp_libro_verificar_isbn_existe
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_verificar_isbn_existe`(IN p_isbn VARCHAR(20), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Libros
    WHERE isbn = p_isbn 
    AND (p_excluir_id IS NULL OR idLibro != p_excluir_id)
    AND activo = TRUE;
END

-- Procedimiento: sp_listar_categorias
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_categorias`()
BEGIN
                SELECT idCategoria, nombre, descripcion, fecha_creacion, activa
                FROM Categorias
                WHERE activa = TRUE
                ORDER BY nombre;
            END

-- Procedimiento: sp_listar_libros
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_libros`()
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.activo = TRUE
                ORDER BY l.titulo;
            END

-- Procedimiento: sp_listar_roles
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_roles`()
BEGIN
                SELECT idRol, nombre, descripcion, fecha_creacion
                FROM Roles
                ORDER BY idRol;
            END

-- Procedimiento: sp_listar_usuarios
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_usuarios`()
BEGIN
                SELECT u.*, r.nombre as rol_nombre
                FROM Usuarios u
                INNER JOIN Roles r ON u.rol = r.idRol
                WHERE u.activo = TRUE
                ORDER BY u.nombre, u.apellido;
            END

-- Procedimiento: sp_prestamo_actualizar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_actualizar`(
            IN p_id INT,
            IN p_fecha_devolucion_esperada DATE,
            IN p_estado VARCHAR(20),
            IN p_observaciones TEXT,
            IN p_fecha_devolucion_real DATETIME
        )
BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            UPDATE Prestamos 
            SET fechaDevolucionEsperada = COALESCE(p_fecha_devolucion_esperada, fechaDevolucionEsperada),
                estado = COALESCE(p_estado, estado),
                observaciones = COALESCE(p_observaciones, observaciones),
                fechaDevolucionReal = COALESCE(p_fecha_devolucion_real, fechaDevolucionReal)
            WHERE idPrestamo = p_id;
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Préstamo actualizado exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo actualizar el préstamo' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_prestamo_actualizar_observaciones
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_actualizar_observaciones`(
        IN p_prestamo_id INT,
        IN p_observaciones TEXT
    )
BEGIN
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        UPDATE Prestamos 
        SET observaciones = p_observaciones
        WHERE idPrestamo = p_prestamo_id;
        
        IF ROW_COUNT() > 0 THEN
            SELECT 'success' as status, 'Observaciones actualizadas exitosamente' as message;
        ELSE
            SELECT 'error' as status, 'No se pudo actualizar las observaciones' as message;
        END IF;
        
        COMMIT;
    END

-- Procedimiento: sp_prestamo_auto_devolver
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_auto_devolver`(IN p_prestamo_id INT)
BEGIN
            DECLARE v_libro_id INT;
            DECLARE v_estado VARCHAR(20);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            -- Verificar que el préstamo existe y está activo
            SELECT idLibro, estado INTO v_libro_id, v_estado 
            FROM Prestamos 
            WHERE idPrestamo = p_prestamo_id;
            
            IF v_libro_id IS NULL THEN
                SELECT 'error' as status, 'Préstamo no encontrado' as message;
            ELSEIF v_estado != 'Activo' THEN
                SELECT 'error' as status, 'El préstamo no está activo' as message;
            ELSE
                -- Marcar como devuelto automáticamente
                UPDATE Prestamos 
                SET estado = 'Devuelto',
                    fechaDevolucionReal = NOW(),
                    observaciones = CONCAT(COALESCE(observaciones, ''), ' [Auto-devolución]')
                WHERE idPrestamo = p_prestamo_id;
                
                -- Restaurar stock del libro
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
                
                SELECT 'success' as status, 'Préstamo devuelto automáticamente' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_prestamo_buscar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_buscar`(IN p_termino VARCHAR(255))
BEGIN
            SELECT 
                p.idPrestamo,
                p.idLibro,
                p.idUsuario,
                p.fechaPrestamo,
                p.fechaDevolucionEsperada,
                p.fechaDevolucionReal,
                p.estado,
                p.observaciones,
                u.nombre as usuario_nombre,
                u.apellido as usuario_apellido,
                l.titulo as libro_titulo,
                l.autor as libro_autor
            FROM Prestamos p
            INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
            INNER JOIN Libros l ON p.idLibro = l.idLibro
            WHERE p.idPrestamo LIKE CONCAT('%', p_termino, '%')
               OR u.nombre LIKE CONCAT('%', p_termino, '%')
               OR u.apellido LIKE CONCAT('%', p_termino, '%')
               OR l.titulo LIKE CONCAT('%', p_termino, '%')
               OR l.autor LIKE CONCAT('%', p_termino, '%')
               OR p.estado LIKE CONCAT('%', p_termino, '%')
            ORDER BY p.fechaPrestamo DESC;
        END

-- Procedimiento: sp_prestamo_devolver_completo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_devolver_completo`(IN p_id INT)
BEGIN
                DECLARE v_libro_id INT;
                DECLARE v_affected_rows INT DEFAULT 0;
                
                -- Obtener el ID del libro
                SELECT idLibro INTO v_libro_id
                FROM Prestamos
                WHERE idPrestamo = p_id AND fechaDevolucionReal IS NULL;
                
                IF v_libro_id IS NOT NULL THEN
                    -- Marcar préstamo como devuelto
                    UPDATE Prestamos 
                    SET fechaDevolucionReal = NOW(),
                        estado = 'Devuelto'
                    WHERE idPrestamo = p_id;
                    
                    SET v_affected_rows = ROW_COUNT();
                    
                    -- Incrementar stock disponible
                    UPDATE Libros 
                    SET disponible = disponible + 1
                    WHERE idLibro = v_libro_id;
                    
                    SELECT 'success' as status, 'Préstamo devuelto exitosamente' as message, v_affected_rows as affected_rows;
                ELSE
                    SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message, 0 as affected_rows;
                END IF;
            END

-- Procedimiento: sp_prestamo_eliminar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_eliminar`(
        IN p_prestamo_id INT
    )
BEGIN
        DECLARE v_libro_id INT;
        DECLARE v_estado VARCHAR(20);
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        -- Obtener información del préstamo
        SELECT idLibro, estado INTO v_libro_id, v_estado
        FROM Prestamos 
        WHERE idPrestamo = p_prestamo_id;
        
        IF v_libro_id IS NULL THEN
            SELECT 'error' as status, 'Préstamo no encontrado' as message;
        ELSE
            -- Si el préstamo está activo, restaurar stock del libro
            IF v_estado = 'Activo' THEN
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
            END IF;
            
            -- Eliminar el préstamo
            DELETE FROM Prestamos 
            WHERE idPrestamo = p_prestamo_id;
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Préstamo eliminado exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo eliminar el préstamo' as message;
            END IF;
        END IF;
        
        COMMIT;
    END

-- Procedimiento: sp_prestamo_insertar_completo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_insertar_completo`(
                IN p_libro_id INT,
                IN p_usuario_id INT,
                IN p_fecha_prestamo DATE,
                IN p_fecha_devolucion_esperada DATE,
                IN p_observaciones TEXT
            )
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                DECLARE v_affected_rows INT DEFAULT 0;
                DECLARE v_prestamo_id INT;
                
                -- Verificar disponibilidad
                SELECT disponible INTO v_disponible
                FROM Libros
                WHERE idLibro = p_libro_id AND activo = 1;
                
                IF v_disponible > 0 THEN
                    -- Insertar préstamo
                    INSERT INTO Prestamos (idLibro, idUsuario, fechaPrestamo, fechaDevolucionEsperada, observaciones, estado)
                    VALUES (p_libro_id, p_usuario_id, p_fecha_prestamo, p_fecha_devolucion_esperada, p_observaciones, 'Activo');
                    
                    SET v_prestamo_id = LAST_INSERT_ID();
                    SET v_affected_rows = ROW_COUNT();
                    
                    -- Decrementar stock disponible
                    UPDATE Libros 
                    SET disponible = disponible - 1
                    WHERE idLibro = p_libro_id;
                    
                    SELECT 'success' as status, 'Préstamo creado exitosamente' as message, v_prestamo_id as idPrestamo;
                ELSE
                    SELECT 'error' as status, 'El libro no está disponible para préstamo' as message, 0 as idPrestamo;
                END IF;
            END

-- Procedimiento: sp_prestamo_obtener_activos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_activos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_estadisticas
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_estadisticas`()
BEGIN
                SELECT 
                    COUNT(*) as total_prestamos,
                    COUNT(CASE WHEN fechaDevolucionReal IS NULL THEN 1 END) as prestamos_activos,
                    COUNT(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 END) as prestamos_devueltos,
                    COUNT(CASE WHEN fechaDevolucionReal IS NULL AND fechaVencimiento < NOW() THEN 1 END) as prestamos_vencidos
                FROM Prestamos;
            END

-- Procedimiento: sp_prestamo_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_id`(IN p_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idPrestamo = p_id;
            END

-- Procedimiento: sp_prestamo_obtener_por_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_libro`(IN p_libro_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                WHERE p.idLibro = p_libro_id
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_por_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_usuario`(IN p_usuario_id INT)
BEGIN
                SELECT p.*, l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idUsuario = p_usuario_id
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_todos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_todos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_usuario_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_usuario_libro`(IN p_usuario_id INT, IN p_libro_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                WHERE p.idUsuario = p_usuario_id 
                AND p.idLibro = p_libro_id
                AND p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC
                LIMIT 1;
            END

-- Procedimiento: sp_prestamo_obtener_vencidos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_vencidos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.fechaDevolucionReal IS NULL 
                AND p.fechaDevolucionEsperada < NOW()
                ORDER BY p.fechaDevolucionEsperada ASC;
            END

-- Procedimiento: sp_prestamo_registrar_devolucion
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_registrar_devolucion`(
            IN p_prestamo_id INT,
            IN p_observaciones TEXT
        )
BEGIN
            DECLARE v_libro_id INT;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            -- Obtener el libro del préstamo
            SELECT idLibro INTO v_libro_id FROM Prestamos WHERE idPrestamo = p_prestamo_id AND estado = 'Activo';
            
            IF v_libro_id IS NULL THEN
                SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
            ELSE
                -- Actualizar el préstamo
                UPDATE Prestamos 
                SET estado = 'Devuelto',
                    fechaDevolucionReal = NOW(),
                    observaciones = COALESCE(p_observaciones, observaciones)
                WHERE idPrestamo = p_prestamo_id;
                
                -- Restaurar stock del libro
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
                
                SELECT 'success' as status, 'Devolución registrada exitosamente' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_prestamo_validar_disponibilidad
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_validar_disponibilidad`(IN p_libro_id INT)
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                DECLARE v_activo TINYINT DEFAULT 0;
                
                SELECT disponible, activo INTO v_disponible, v_activo
                FROM Libros
                WHERE idLibro = p_libro_id;
                
                IF v_disponible > 0 AND v_activo = 1 THEN
                    SELECT 1 as disponible;
                ELSE
                    SELECT 0 as disponible;
                END IF;
            END

-- Procedimiento: sp_solicitudes_estadisticas
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas,
        SUM(CASE WHEN DATE(fecha_solicitud) = CURDATE() THEN 1 ELSE 0 END) as solicitudes_hoy
    FROM solicitudes_prestamo;
END

-- Procedimiento: sp_solicitudes_estadisticas_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_estadisticas_usuario`(IN p_usuario_id INT)
BEGIN
            SELECT 
                COUNT(*) as total_solicitudes,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
            FROM solicitudes_prestamo
            WHERE usuario_id = p_usuario_id;
        END

-- Procedimiento: sp_solicitudes_listar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_listar`(IN p_estado VARCHAR(20))
BEGIN
    SELECT 
        s.idSolicitud,
        s.usuario_id,
        s.libro_id,
        s.fecha_solicitud,
        s.estado,
        s.observaciones_usuario,
        s.observaciones_bibliotecario,
        s.fecha_respuesta,
        s.bibliotecario_id,
        s.prestamo_id,
        u.nombre as usuario_nombre,
        u.apellido as usuario_apellido,
        u.email as usuario_email,
        u.telefono as usuario_telefono,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        l.disponible as libro_disponible,
        c.nombre as categoria_nombre,
        b.nombre as bibliotecario_nombre,
        b.apellido as bibliotecario_apellido
    FROM solicitudes_prestamo s
    INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    LEFT JOIN Usuarios b ON s.bibliotecario_id = b.idUsuario
    WHERE (p_estado IS NULL OR s.estado = p_estado)
    ORDER BY 
        CASE s.estado
            WHEN 'Pendiente' THEN 1
            WHEN 'Aprobada' THEN 2
            WHEN 'Rechazada' THEN 3
            WHEN 'Convertida' THEN 4
        END,
        s.fecha_solicitud DESC;
END

-- Procedimiento: sp_solicitudes_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_usuario`(IN p_usuario_id INT)
BEGIN
    SELECT 
        s.idSolicitud,
        s.usuario_id,
        s.libro_id,
        s.fecha_solicitud,
        s.estado,
        s.observaciones_usuario,
        s.observaciones_bibliotecario,
        s.fecha_respuesta,
        s.prestamo_id,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        l.disponible as libro_disponible,
        c.nombre as categoria_nombre,
        b.nombre as bibliotecario_nombre,
        b.apellido as bibliotecario_apellido
    FROM solicitudes_prestamo s
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    LEFT JOIN Usuarios b ON s.bibliotecario_id = b.idUsuario
    WHERE s.usuario_id = p_usuario_id
    ORDER BY s.fecha_solicitud DESC;
END

-- Procedimiento: sp_solicitud_aprobar_y_crear_prestamo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_aprobar_y_crear_prestamo`(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_fecha_devolucion DATETIME,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_usuario_id INT;
    DECLARE v_libro_id INT;
    DECLARE v_prestamo_id INT;
    DECLARE v_disponible INT DEFAULT 0;
    
    -- Obtener datos de la solicitud
    SELECT usuario_id, libro_id INTO v_usuario_id, v_libro_id
    FROM solicitudes_prestamo
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    IF v_usuario_id IS NOT NULL THEN
        -- Verificar disponibilidad
        SELECT disponible INTO v_disponible
        FROM Libros
        WHERE idLibro = v_libro_id;
        
        IF v_disponible > 0 THEN
            -- Crear préstamo
            INSERT INTO Prestamos (idLibro, idUsuario, fechaDevolucionEsperada, observaciones)
            VALUES (v_libro_id, v_usuario_id, p_fecha_devolucion, p_observaciones);
            
            SET v_prestamo_id = LAST_INSERT_ID();
            
            -- Actualizar stock
            UPDATE Libros 
            SET disponible = disponible - 1,
                fecha_actualizacion = NOW()
            WHERE idLibro = v_libro_id;
            
            -- Actualizar solicitud
            UPDATE solicitudes_prestamo 
            SET estado = 'Convertida',
                bibliotecario_id = p_bibliotecario_id,
                observaciones_bibliotecario = p_observaciones,
                fecha_respuesta = NOW(),
                prestamo_id = v_prestamo_id
            WHERE idSolicitud = p_solicitud_id;
            
            SELECT 'success' as status, v_prestamo_id as prestamo_id, 'Solicitud aprobada y préstamo creado' as message;
        ELSE
            SELECT 'error' as status, 0 as prestamo_id, 'No hay ejemplares disponibles' as message;
        END IF;
    ELSE
        SELECT 'error' as status, 0 as prestamo_id, 'Solicitud no encontrada o ya procesada' as message;
    END IF;
END

-- Procedimiento: sp_solicitud_cancelar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_cancelar`(
            IN p_solicitud_id INT,
            IN p_usuario_id INT
        )
BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            UPDATE solicitudes_prestamo 
            SET estado = 'Rechazada',
                observaciones_bibliotecario = 'Cancelada por el usuario',
                fecha_respuesta = NOW()
            WHERE idSolicitud = p_solicitud_id 
            AND usuario_id = p_usuario_id 
            AND estado = 'Pendiente';
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Solicitud cancelada exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo cancelar la solicitud' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_solicitud_insertar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_insertar`(
    IN p_usuario_id INT,
    IN p_libro_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    
    -- Verificar si el libro está disponible
    SELECT disponible INTO v_disponible 
    FROM Libros 
    WHERE idLibro = p_libro_id;
    
    -- Solo permitir solicitud si hay ejemplares disponibles
    IF v_disponible > 0 THEN
        INSERT INTO solicitudes_prestamo (usuario_id, libro_id, observaciones_usuario)
        VALUES (p_usuario_id, p_libro_id, p_observaciones);
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status;
    ELSE
        SELECT 0 as idSolicitud, 'no_disponible' as status;
    END IF;
END

-- Procedimiento: sp_solicitud_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_obtener_por_id`(IN p_id INT)
BEGIN
            SELECT 
                s.idSolicitud,
                s.usuario_id,
                s.libro_id,
                s.fecha_solicitud,
                s.observaciones_usuario,
                s.estado,
                s.bibliotecario_id,
                s.fecha_respuesta,
                s.observaciones_bibliotecario,
                u.nombre as usuario_nombre,
                u.apellido as usuario_apellido,
                u.email as usuario_email,
                l.titulo as libro_titulo,
                l.autor as libro_autor,
                c.nombre as categoria_nombre
            FROM solicitudes_prestamo s
            INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
            INNER JOIN Libros l ON s.libro_id = l.idLibro
            INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
            WHERE s.idSolicitud = p_id;
        END

-- Procedimiento: sp_solicitud_responder
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_responder`(
    IN p_solicitud_id INT,
    IN p_estado VARCHAR(20),
    IN p_bibliotecario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    UPDATE solicitudes_prestamo 
    SET 
        estado = p_estado,
        bibliotecario_id = p_bibliotecario_id,
        observaciones_bibliotecario = p_observaciones,
        fecha_respuesta = NOW()
    WHERE idSolicitud = p_solicitud_id 
    AND estado = 'Pendiente';
    
    SELECT ROW_COUNT() as affected_rows;
END

-- Procedimiento: sp_usuario_actualizar_ultimo_acceso
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_actualizar_ultimo_acceso`(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET ultimo_acceso = NOW() 
    WHERE idUsuario = p_id;
END

-- Procedimiento: sp_usuario_buscar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_buscar`(IN p_termino VARCHAR(100))
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.activo = TRUE
    AND (u.nombre LIKE CONCAT('%', p_termino, '%')
         OR u.apellido LIKE CONCAT('%', p_termino, '%')
         OR u.usuario LIKE CONCAT('%', p_termino, '%')
         OR u.email LIKE CONCAT('%', p_termino, '%'))
    ORDER BY u.nombre, u.apellido;
END

-- Procedimiento: sp_usuario_estadisticas
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total_usuarios,
        SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
        SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
        SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as nuevos_hoy
    FROM Usuarios
    WHERE activo = TRUE;
END

-- Procedimiento: sp_usuario_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.idUsuario = p_id AND u.activo = TRUE;
END

-- Procedimiento: sp_usuario_obtener_por_rol
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_rol`(IN p_rol INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.rol = p_rol AND u.activo = TRUE
    ORDER BY u.nombre, u.apellido;
END

-- Procedimiento: sp_usuario_obtener_por_username
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_username`(IN p_usuario VARCHAR(50))
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.usuario = p_usuario AND u.activo = TRUE;
END

-- Procedimiento: sp_usuario_verificar_email
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_verificar_email`(IN p_email VARCHAR(100), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE email = p_email 
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id)
    AND activo = TRUE;
END

-- Procedimiento: sp_usuario_verificar_existe
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_verificar_existe`(IN p_usuario VARCHAR(50))
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE usuario = p_usuario AND activo = TRUE;
END

-- =====================================================
-- 6. TRIGGERS
-- =====================================================

-- Trigger: tr_libros_actualizar_fecha
CREATE DEFINER=`root`@`localhost` TRIGGER `tr_libros_actualizar_fecha` BEFORE UPDATE ON `libros` FOR EACH ROW BEGIN
    SET NEW.fecha_actualizacion = NOW();
END

-- Trigger: tr_prestamos_actualizar_fecha
CREATE DEFINER=`root`@`localhost` TRIGGER `tr_prestamos_actualizar_fecha` BEFORE UPDATE ON `prestamos` FOR EACH ROW BEGIN
    SET NEW.fecha_actualizacion = NOW();
END

-- Trigger: tr_solicitudes_actualizar_fecha
CREATE DEFINER=`root`@`localhost` TRIGGER `tr_solicitudes_actualizar_fecha` BEFORE UPDATE ON `solicitudes_prestamo` FOR EACH ROW BEGIN
    SET NEW.fecha_actualizacion = NOW();
END

-- =====================================================
-- 7. VISTAS
-- =====================================================

-- Vista: vista_estadisticas_generales
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_estadisticas_generales` AS select (select count(0) from `usuarios` where (`usuarios`.`activo` = true)) AS `total_usuarios`,(select count(0) from `libros` where (`libros`.`activo` = true)) AS `total_libros`,(select count(0) from `prestamos`) AS `total_prestamos`,(select count(0) from `prestamos` where (`prestamos`.`fechaDevolucionReal` is null)) AS `prestamos_activos`,(select count(0) from `prestamos` where (`prestamos`.`fechaDevolucionReal` is not null)) AS `prestamos_devueltos`,(select count(0) from `prestamos` where ((`prestamos`.`fechaDevolucionReal` is null) and (`prestamos`.`fechaDevolucionEsperada` < curdate()))) AS `prestamos_vencidos`,(select count(0) from `solicitudes_prestamo`) AS `total_solicitudes`,(select count(0) from `solicitudes_prestamo` where (`solicitudes_prestamo`.`estado` = 'Pendiente')) AS `solicitudes_pendientes`;

-- Vista: vista_prestamos_activos
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_prestamos_activos` AS select `p`.`idPrestamo` AS `idPrestamo`,`p`.`fechaPrestamo` AS `fechaPrestamo`,`p`.`fechaDevolucionEsperada` AS `fechaDevolucionEsperada`,`p`.`observaciones` AS `observaciones`,`u`.`nombre` AS `usuario_nombre`,`u`.`apellido` AS `usuario_apellido`,`u`.`email` AS `usuario_email`,`l`.`titulo` AS `libro_titulo`,`l`.`autor` AS `libro_autor`,`l`.`isbn` AS `libro_isbn`,`c`.`nombre` AS `categoria_nombre`,(to_days(`p`.`fechaDevolucionEsperada`) - to_days(curdate())) AS `dias_restantes` from (((`prestamos` `p` join `usuarios` `u` on((`p`.`idUsuario` = `u`.`idUsuario`))) join `libros` `l` on((`p`.`idLibro` = `l`.`idLibro`))) join `categorias` `c` on((`l`.`idCategoria` = `c`.`idCategoria`))) where (`p`.`fechaDevolucionReal` is null);

DELIMITER ;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
COMMIT;

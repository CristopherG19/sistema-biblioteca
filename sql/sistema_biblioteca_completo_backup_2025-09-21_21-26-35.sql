-- =====================================================
-- SISTEMA DE BIBLIOTECA COMPLETO - SCRIPT UNIFICADO
-- Generado automáticamente: 2025-09-21 20:50:51
-- Total de procedimientos: 66
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

-- Tabla Roles
CREATE TABLE IF NOT EXISTS `Roles` (
  `idRol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Usuarios
CREATE TABLE IF NOT EXISTS `Usuarios` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `rol` int(11) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `telefono` varchar(20),
  `activo` boolean DEFAULT TRUE,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` timestamp NULL,
  PRIMARY KEY (`idUsuario`),
  FOREIGN KEY (`rol`) REFERENCES `Roles`(`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Categorias
CREATE TABLE IF NOT EXISTS `Categorias` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Libros
CREATE TABLE IF NOT EXISTS `Libros` (
  `idLibro` int(11) NOT NULL AUTO_INCREMENT,
  `idCategoria` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `editorial` varchar(100),
  `anio` int(4),
  `isbn` varchar(20) UNIQUE,
  `stock` int(11) DEFAULT 0,
  `disponible` int(11) DEFAULT 0,
  `descripcion` text,
  `portada` varchar(255),
  `archivo_pdf` varchar(255),
  `numero_paginas` int(11),
  `tamano_archivo` bigint(20),
  `fecha_subida` timestamp NULL,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLibro`),
  FOREIGN KEY (`idCategoria`) REFERENCES `Categorias`(`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Prestamos
CREATE TABLE IF NOT EXISTS `Prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idLibro` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fechaPrestamo` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fechaDevolucionEsperada` date NOT NULL,
  `fechaDevolucionReal` timestamp NULL,
  `estado` enum('Activo','Devuelto','Vencido') DEFAULT 'Activo',
  `observaciones` text,
  PRIMARY KEY (`idPrestamo`),
  FOREIGN KEY (`idLibro`) REFERENCES `Libros`(`idLibro`),
  FOREIGN KEY (`idUsuario`) REFERENCES `Usuarios`(`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Solicitudes de Préstamo
CREATE TABLE IF NOT EXISTS `solicitudes_prestamo` (
  `idSolicitud` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fecha_solicitud` timestamp DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','Aprobada','Rechazada','Convertida') DEFAULT 'Pendiente',
  `observaciones_usuario` text,
  `bibliotecario_id` int(11),
  `fecha_respuesta` timestamp NULL,
  `observaciones_bibliotecario` text,
  PRIMARY KEY (`idSolicitud`),
  FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios`(`idUsuario`),
  FOREIGN KEY (`libro_id`) REFERENCES `Libros`(`idLibro`),
  FOREIGN KEY (`bibliotecario_id`) REFERENCES `Usuarios`(`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 3. DATOS INICIALES
-- =====================================================

-- Insertar roles
INSERT INTO `Roles` (`idRol`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema'),
(2, 'Bibliotecario', 'Gestión de libros y préstamos'),
(3, 'Lector', 'Solo consulta y solicitudes');

-- Insertar usuario administrador
INSERT INTO `Usuarios` (`nombre`, `apellido`, `usuario`, `password`, `rol`, `email`, `telefono`) VALUES
('Administrador', 'Sistema', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin@biblioteca.com', '123456789');

-- Insertar categorías
INSERT INTO `Categorias` (`nombre`, `descripcion`) VALUES
('Ficción', 'Novelas y literatura de ficción'),
('Ciencia', 'Libros de ciencias naturales'),
('Historia', 'Libros de historia y biografías'),
('Tecnología', 'Libros de tecnología e informática'),
('Filosofía', 'Libros de filosofía y pensamiento'),
('Matemáticas', 'Libros de matemáticas y estadística'),
('Literatura', 'Literatura clásica y contemporánea'),
('Deportes', 'Libros de deportes y actividad física');

-- =====================================================
-- 4. PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento: sp_actualizar_categoria
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_actualizar_categoria(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_actualizar_libro
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_actualizar_libro(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_actualizar_usuario
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_actualizar_usuario(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_ampliacion_aprobar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_ampliacion_aprobar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_ampliacion_obtener_solicitudes
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_ampliacion_obtener_solicitudes(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_ampliacion_rechazar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_ampliacion_rechazar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_ampliacion_solicitar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_ampliacion_solicitar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_categoria_obtener_por_id
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_categoria_obtener_por_id(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_eliminar_categoria
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_eliminar_categoria(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_eliminar_libro
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_eliminar_libro(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_eliminar_usuario
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_eliminar_usuario(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_insertar_categoria
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_insertar_categoria(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_insertar_libro
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_insertar_libro(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_insertar_usuario
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_insertar_usuario(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libros_disponibles_solicitud
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libros_disponibles_solicitud(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_actualizar_pdf
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_actualizar_pdf(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_actualizar_stock_devolucion
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_actualizar_stock_devolucion(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_actualizar_stock_prestamo
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_actualizar_stock_prestamo(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_buscar_por_titulo_autor
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_buscar_por_titulo_autor(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_con_detalle_pdf
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_con_detalle_pdf(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_con_prestamos
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_con_prestamos(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_disponibles
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_disponibles(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_estadisticas_pdf
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_estadisticas_pdf(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_por_id
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_por_id(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_por_isbn
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_por_isbn(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_obtener_recientes
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_obtener_recientes(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_registrar_lectura
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_registrar_lectura(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_libro_verificar_isbn_existe
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_libro_verificar_isbn_existe(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_listar_categorias
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_listar_categorias(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_listar_libros
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_listar_libros(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_listar_roles
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_listar_roles(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_listar_usuarios
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_listar_usuarios(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_actualizar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_actualizar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_actualizar_observaciones
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_actualizar_observaciones(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_auto_devolver
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_auto_devolver(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_buscar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_buscar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_devolver_completo
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_devolver_completo(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_eliminar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_eliminar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_insertar_completo
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_insertar_completo(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_activos
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_activos(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_estadisticas
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_estadisticas(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_por_id
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_por_id(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_por_libro
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_por_libro(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_por_usuario
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_por_usuario(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_todos
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_todos(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_usuario_libro
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_usuario_libro(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_obtener_vencidos
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_obtener_vencidos(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_registrar_devolucion
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_registrar_devolucion(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_prestamo_validar_disponibilidad
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_prestamo_validar_disponibilidad(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitudes_estadisticas
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitudes_estadisticas(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitudes_estadisticas_usuario
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitudes_estadisticas_usuario(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitudes_listar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitudes_listar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitudes_usuario
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitudes_usuario(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitud_aprobar_y_crear_prestamo
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitud_aprobar_y_crear_prestamo(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitud_cancelar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitud_cancelar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitud_insertar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitud_insertar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitud_obtener_por_id
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitud_obtener_por_id(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_solicitud_responder
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_solicitud_responder(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_actualizar_ultimo_acceso
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_actualizar_ultimo_acceso(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_buscar
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_buscar(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_estadisticas
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_estadisticas(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_obtener_por_id
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_obtener_por_id(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_obtener_por_rol
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_obtener_por_rol(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_obtener_por_username
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_obtener_por_username(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_verificar_email
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_verificar_email(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

-- Procedimiento: sp_usuario_verificar_existe
-- Este procedimiento debe ser creado manualmente
-- Ver implementación en base de datos actual
-- CREATE PROCEDURE sp_usuario_verificar_existe(...)
-- BEGIN
--     -- Implementación del procedimiento
-- END //

DELIMITER ;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
COMMIT;

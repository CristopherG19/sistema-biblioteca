-- =====================================================
-- SISTEMA DE GESTIÓN BIBLIOTECARIA - SCRIPT COMPLETO
-- =====================================================
-- Archivo: sistema_biblioteca_completo.sql
-- Fecha: 2025-01-19
-- Descripción: Script unificado que incluye:
--   - Creación de la base de datos
--   - Esquema completo de tablas
--   - Procedimientos almacenados
--   - Datos iniciales
--   - Índices y optimizaciones
-- =====================================================

-- =====================================================
-- 1. CREACIÓN DE BASE DE DATOS
-- =====================================================

DROP DATABASE IF EXISTS biblioteca_db;
CREATE DATABASE biblioteca_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE biblioteca_db;

-- =====================================================
-- 2. CREACIÓN DE TABLAS PRINCIPALES
-- =====================================================

-- Tabla de Roles
CREATE TABLE Roles (
    idRol INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    idUsuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol INT NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (rol) REFERENCES Roles(idRol) ON DELETE RESTRICT,
    INDEX idx_usuario (usuario),
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
);

-- Tabla de Categorías
CREATE TABLE Categorias (
    idCategoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activa BOOLEAN DEFAULT TRUE,
    INDEX idx_nombre (nombre),
    INDEX idx_activa (activa)
);

-- Tabla de Libros
CREATE TABLE Libros (
    idLibro INT PRIMARY KEY AUTO_INCREMENT,
    idCategoria INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    editorial VARCHAR(100),
    anio INT,
    isbn VARCHAR(20) UNIQUE,
    stock INT NOT NULL DEFAULT 0,
    disponible INT NOT NULL DEFAULT 0,
    descripcion TEXT,
    portada VARCHAR(255),
    archivo_pdf VARCHAR(255),
    fecha_adicion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (idCategoria) REFERENCES Categorias(idCategoria) ON DELETE RESTRICT,
    INDEX idx_titulo (titulo),
    INDEX idx_autor (autor),
    INDEX idx_isbn (isbn),
    INDEX idx_categoria (idCategoria),
    INDEX idx_disponible (disponible),
    INDEX idx_activo (activo)
);

-- Tabla de Préstamos
CREATE TABLE Prestamos (
    idPrestamo INT PRIMARY KEY AUTO_INCREMENT,
    idLibro INT NOT NULL,
    idUsuario INT NOT NULL,
    fechaPrestamo DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaDevolucionEsperada DATETIME NOT NULL,
    fechaDevolucionReal DATETIME NULL,
    estado ENUM('Activo', 'Devuelto', 'Vencido') DEFAULT 'Activo',
    observaciones TEXT,
    multa DECIMAL(10,2) DEFAULT 0.00,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro) ON DELETE RESTRICT,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) ON DELETE RESTRICT,
    INDEX idx_libro (idLibro),
    INDEX idx_usuario (idUsuario),
    INDEX idx_estado (estado),
    INDEX idx_fecha_prestamo (fechaPrestamo),
    INDEX idx_fecha_devolucion (fechaDevolucionEsperada)
);

-- =====================================================
-- 3. CREACIÓN DE TABLAS DE FUNCIONALIDADES AVANZADAS
-- =====================================================

-- Tabla de Solicitudes de Préstamo
CREATE TABLE solicitudes_prestamo (
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    libro_id INT NOT NULL,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada', 'Convertida') DEFAULT 'Pendiente',
    observaciones_usuario TEXT,
    observaciones_bibliotecario TEXT,
    fecha_respuesta DATETIME NULL,
    bibliotecario_id INT NULL,
    prestamo_id INT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES Libros(idLibro) ON DELETE CASCADE,
    FOREIGN KEY (bibliotecario_id) REFERENCES Usuarios(idUsuario) ON DELETE SET NULL,
    FOREIGN KEY (prestamo_id) REFERENCES Prestamos(idPrestamo) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha_solicitud (fecha_solicitud),
    INDEX idx_usuario (usuario_id),
    INDEX idx_libro (libro_id),
    INDEX idx_bibliotecario (bibliotecario_id)
);

-- Tabla de Solicitudes de Ampliación
CREATE TABLE SolicitudesAmpliacion (
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    idPrestamo INT NOT NULL,
    diasAdicionales INT NOT NULL DEFAULT 7,
    motivo TEXT,
    fechaSolicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaRespuesta DATETIME NULL,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL DEFAULT 'Pendiente',
    respuestaBibliotecario TEXT,
    idBibliotecario INT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (idPrestamo) REFERENCES Prestamos(idPrestamo) ON DELETE CASCADE,
    FOREIGN KEY (idBibliotecario) REFERENCES Usuarios(idUsuario) ON DELETE SET NULL,
    INDEX idx_prestamo (idPrestamo),
    INDEX idx_estado (estado),
    INDEX idx_fecha_solicitud (fechaSolicitud),
    INDEX idx_bibliotecario (idBibliotecario)
);

-- Tabla de Multas
CREATE TABLE Multas (
    idMulta INT PRIMARY KEY AUTO_INCREMENT,
    idPrestamo INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    pagada BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_pago DATETIME NULL,
    FOREIGN KEY (idPrestamo) REFERENCES Prestamos(idPrestamo) ON DELETE CASCADE,
    INDEX idx_prestamo (idPrestamo),
    INDEX idx_pagada (pagada),
    INDEX idx_fecha_creacion (fecha_creacion)
);

-- Tabla de Historial de Lectura
CREATE TABLE HistorialLectura (
    idHistorial INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idLibro INT NOT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME NULL,
    tipo ENUM('Prestamo', 'Lectura', 'Reserva') DEFAULT 'Prestamo',
    calificacion INT CHECK (calificacion >= 1 AND calificacion <= 5),
    comentario TEXT,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro) ON DELETE CASCADE,
    INDEX idx_usuario (idUsuario),
    INDEX idx_libro (idLibro),
    INDEX idx_fecha_inicio (fecha_inicio),
    INDEX idx_tipo (tipo)
);

-- Tabla de Libros Favoritos
CREATE TABLE LibrosFavoritos (
    idFavorito INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idLibro INT NOT NULL,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (idUsuario, idLibro),
    INDEX idx_usuario (idUsuario),
    INDEX idx_libro (idLibro)
);

-- Tabla de Intereses de Usuario
CREATE TABLE InteresesUsuario (
    idInteresUsuario INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idCategoria INT NOT NULL,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (idCategoria) REFERENCES Categorias(idCategoria) ON DELETE CASCADE,
    UNIQUE KEY unique_interes (idUsuario, idCategoria),
    INDEX idx_usuario (idUsuario),
    INDEX idx_categoria (idCategoria)
);

-- =====================================================
-- 4. DATOS INICIALES
-- =====================================================

-- Insertar roles
INSERT INTO Roles (idRol, nombre, descripcion) VALUES
(1, 'Bibliotecario', 'Administrador del sistema con acceso completo'),
(2, 'Lector', 'Usuario que puede solicitar y gestionar préstamos');

-- Insertar categorías de ejemplo
INSERT INTO Categorias (nombre, descripcion) VALUES
('Ficción', 'Novelas y cuentos de ficción'),
('Ciencia', 'Libros de ciencias exactas y naturales'),
('Historia', 'Libros de historia universal y local'),
('Tecnología', 'Libros sobre tecnología e informática'),
('Matemáticas', 'Libros de matemáticas y estadística'),
('Literatura', 'Obras literarias clásicas y contemporáneas'),
('Filosofía', 'Libros de filosofía y ética'),
('Arte', 'Libros sobre arte, música y cultura'),
('Deportes', 'Libros sobre deportes y actividad física');

-- Insertar usuario administrador por defecto
INSERT INTO Usuarios (nombre, apellido, usuario, password, rol, email, telefono) VALUES
('Administrador', 'Sistema', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin@biblioteca.com', '123456789');

-- =====================================================
-- 5. PROCEDIMIENTOS ALMACENADOS - USUARIOS
-- =====================================================

DELIMITER //

-- Obtener usuario por ID
CREATE PROCEDURE sp_usuario_obtener_por_id(IN p_id INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.idUsuario = p_id AND u.activo = TRUE;
END //

-- Buscar usuarios
CREATE PROCEDURE sp_usuario_buscar(IN p_termino VARCHAR(100))
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
END //

-- Verificar si usuario existe
CREATE PROCEDURE sp_usuario_verificar_existe(IN p_usuario VARCHAR(50))
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE usuario = p_usuario AND activo = TRUE;
END //

-- Verificar si email existe
CREATE PROCEDURE sp_usuario_verificar_email(IN p_email VARCHAR(100), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE email = p_email 
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id)
    AND activo = TRUE;
END //

-- Obtener estadísticas de usuarios
CREATE PROCEDURE sp_usuario_estadisticas()
BEGIN
    SELECT 
        COUNT(*) as total_usuarios,
        SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
        SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
        SUM(CASE WHEN DATE(fecha_registro) = CURRENT_DATE THEN 1 ELSE 0 END) as nuevos_hoy
    FROM Usuarios
    WHERE activo = TRUE;
END //

-- Obtener usuario por username
CREATE PROCEDURE sp_usuario_obtener_por_username(IN p_usuario VARCHAR(50))
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.usuario = p_usuario AND u.activo = TRUE;
END //

-- Actualizar último acceso
CREATE PROCEDURE sp_usuario_actualizar_ultimo_acceso(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET ultimo_acceso = NOW() 
    WHERE idUsuario = p_id;
END //

-- Obtener usuarios por rol
CREATE PROCEDURE sp_usuario_obtener_por_rol(IN p_rol INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.rol = p_rol AND u.activo = TRUE
    ORDER BY u.nombre, u.apellido;
END //

-- =====================================================
-- 6. PROCEDIMIENTOS ALMACENADOS - LIBROS
-- =====================================================

-- Obtener libro por ID
CREATE PROCEDURE sp_libro_obtener_por_id(IN p_id INT)
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.idLibro = p_id AND l.activo = TRUE;
END //

-- Actualizar PDF del libro
CREATE PROCEDURE sp_libro_actualizar_pdf(IN p_id INT, IN p_archivo_pdf VARCHAR(255))
BEGIN
    UPDATE Libros 
    SET archivo_pdf = p_archivo_pdf,
        fecha_actualizacion = NOW()
    WHERE idLibro = p_id;
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- Obtener libros disponibles
CREATE PROCEDURE sp_libro_obtener_disponibles(IN p_limite INT)
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.disponible > 0 AND l.activo = TRUE
    ORDER BY l.titulo
    LIMIT CASE WHEN p_limite IS NULL THEN 1000 ELSE p_limite END;
END //

-- Buscar libros por título o autor
CREATE PROCEDURE sp_libro_buscar_por_titulo_autor(IN p_termino VARCHAR(100))
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.activo = TRUE
    AND (l.titulo LIKE CONCAT('%', p_termino, '%')
         OR l.autor LIKE CONCAT('%', p_termino, '%')
         OR l.isbn LIKE CONCAT('%', p_termino, '%'))
    ORDER BY l.titulo;
END //

-- Verificar si ISBN existe
CREATE PROCEDURE sp_libro_verificar_isbn_existe(IN p_isbn VARCHAR(20), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Libros
    WHERE isbn = p_isbn 
    AND (p_excluir_id IS NULL OR idLibro != p_excluir_id)
    AND activo = TRUE;
END //

-- Obtener libros con información de préstamos
CREATE PROCEDURE sp_libro_obtener_con_prestamos()
BEGIN
    SELECT l.*, c.nombre as categoria,
           CASE WHEN COUNT(p.idPrestamo) IS NULL THEN 0 ELSE COUNT(p.idPrestamo) END as total_prestamos,
           CASE WHEN SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END) IS NULL THEN 0 ELSE SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END) END as prestamos_activos
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
    WHERE l.activo = TRUE
    GROUP BY l.idLibro
    ORDER BY l.titulo;
END //

-- Registrar lectura de libro
CREATE PROCEDURE sp_libro_registrar_lectura(IN p_libro_id INT, IN p_usuario_id INT)
BEGIN
    INSERT INTO HistorialLectura (idUsuario, idLibro, tipo)
    VALUES (p_usuario_id, p_libro_id, 'Lectura');
    
    SELECT LAST_INSERT_ID() as id_historial;
END //

-- =====================================================
-- 7. PROCEDIMIENTOS ALMACENADOS - PRÉSTAMOS
-- =====================================================

-- Obtener todos los préstamos
CREATE PROCEDURE sp_prestamo_obtener_todos()
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre, u.apellido as usuario_apellido, u.email as usuario_email,
           l.titulo as libro_titulo, l.autor as libro_autor, l.isbn as libro_isbn,
           c.nombre as categoria_nombre
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    ORDER BY p.fechaPrestamo DESC;
END //

-- Obtener préstamos por usuario
CREATE PROCEDURE sp_prestamo_obtener_por_usuario(IN p_usuario_id INT)
BEGIN
    SELECT p.*, 
           l.titulo as libro_titulo, l.autor as libro_autor, l.isbn as libro_isbn,
           c.nombre as categoria_nombre
    FROM Prestamos p
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.idUsuario = p_usuario_id
    ORDER BY p.fechaPrestamo DESC;
END //

-- Obtener préstamos activos
CREATE PROCEDURE sp_prestamo_obtener_activos()
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre, u.apellido as usuario_apellido, u.email as usuario_email,
           l.titulo as libro_titulo, l.autor as libro_autor, l.isbn as libro_isbn,
           c.nombre as categoria_nombre
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.fechaDevolucionReal IS NULL
    ORDER BY p.fechaDevolucionEsperada ASC;
END //

-- Obtener préstamos vencidos
CREATE PROCEDURE sp_prestamo_obtener_vencidos()
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre, u.apellido as usuario_apellido, u.email as usuario_email,
           l.titulo as libro_titulo, l.autor as libro_autor, l.isbn as libro_isbn,
           c.nombre as categoria_nombre
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.fechaDevolucionReal IS NULL 
    AND p.fechaDevolucionEsperada < NOW()
    ORDER BY p.fechaDevolucionEsperada ASC;
END //

-- Insertar préstamo completo
CREATE PROCEDURE sp_prestamo_insertar_completo(
    IN p_idLibro INT,
    IN p_idUsuario INT,
    IN p_fechaDevolucionEsperada DATETIME,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE v_prestamo_id INT;
    
    -- Verificar disponibilidad
    SELECT disponible INTO v_disponible
    FROM Libros
    WHERE idLibro = p_idLibro;
    
    IF v_disponible > 0 THEN
        -- Insertar préstamo
        INSERT INTO Prestamos (idLibro, idUsuario, fechaDevolucionEsperada, observaciones)
        VALUES (p_idLibro, p_idUsuario, p_fechaDevolucionEsperada, p_observaciones);
        
        SET v_prestamo_id = LAST_INSERT_ID();
        
        -- Actualizar stock
        UPDATE Libros 
        SET disponible = disponible - 1,
            fecha_actualizacion = NOW()
        WHERE idLibro = p_idLibro;
        
        SELECT v_prestamo_id as idPrestamo, 'success' as status, 'Préstamo creado exitosamente' as message;
    ELSE
        SELECT 0 as idPrestamo, 'error' as status, 'No hay ejemplares disponibles' as message;
    END IF;
END //

-- Devolver préstamo completo
CREATE PROCEDURE sp_prestamo_devolver_completo(IN p_idPrestamo INT, IN p_observaciones TEXT)
BEGIN
    DECLARE v_idLibro INT;
    DECLARE v_observaciones_actuales TEXT;
    
    -- Obtener ID del libro
    SELECT idLibro, observaciones INTO v_idLibro, v_observaciones_actuales
    FROM Prestamos
    WHERE idPrestamo = p_idPrestamo AND fechaDevolucionReal IS NULL;
    
    IF v_idLibro IS NOT NULL THEN
        -- Actualizar préstamo
        UPDATE Prestamos 
        SET fechaDevolucionReal = NOW(),
            estado = 'Devuelto',
            observaciones = CONCAT(CASE WHEN v_observaciones_actuales IS NULL THEN '' ELSE v_observaciones_actuales END, 
                                 CASE WHEN p_observaciones IS NOT NULL THEN CONCAT(' | Devolución: ', p_observaciones) ELSE '' END)
        WHERE idPrestamo = p_idPrestamo;
        
        -- Actualizar stock
        UPDATE Libros 
        SET disponible = disponible + 1,
            fecha_actualizacion = NOW()
        WHERE idLibro = v_idLibro;
        
        SELECT 'success' as status, 'Préstamo devuelto exitosamente' as message;
    ELSE
        SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    END IF;
END //

-- Obtener estadísticas de préstamos
CREATE PROCEDURE sp_prestamo_obtener_estadisticas()
BEGIN
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN fechaDevolucionReal IS NULL THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 ELSE 0 END) as devueltos,
        SUM(CASE WHEN fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < CURRENT_DATE THEN 1 ELSE 0 END) as vencidos
    FROM Prestamos;
END //

-- Obtener préstamo por ID
CREATE PROCEDURE sp_prestamo_obtener_por_id(IN p_id INT)
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre, u.apellido as usuario_apellido, u.email as usuario_email,
           l.titulo as libro_titulo, l.autor as libro_autor, l.isbn as libro_isbn,
           c.nombre as categoria_nombre
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.idPrestamo = p_id;
END //

-- =====================================================
-- 8. PROCEDIMIENTOS ALMACENADOS - SOLICITUDES DE PRÉSTAMO
-- =====================================================

-- Listar solicitudes
CREATE PROCEDURE sp_solicitudes_listar(IN p_estado VARCHAR(20))
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
END //

-- Obtener solicitudes por usuario
CREATE PROCEDURE sp_solicitudes_usuario(IN p_usuario_id INT)
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
END //

-- Insertar solicitud
CREATE PROCEDURE sp_solicitud_insertar(
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
END //

-- Responder solicitud
CREATE PROCEDURE sp_solicitud_responder(
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
END //

-- Aprobar solicitud y crear préstamo
CREATE PROCEDURE sp_solicitud_aprobar_y_crear_prestamo(
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
END //

-- Obtener estadísticas de solicitudes
CREATE PROCEDURE sp_solicitudes_estadisticas()
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas,
        SUM(CASE WHEN DATE(fecha_solicitud) = CURRENT_DATE THEN 1 ELSE 0 END) as solicitudes_hoy
    FROM solicitudes_prestamo;
END //

-- =====================================================
-- 9. PROCEDIMIENTOS ALMACENADOS - AMPLIACIONES
-- =====================================================

-- Solicitar ampliación
CREATE PROCEDURE sp_ampliacion_solicitar(
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
END //

-- Obtener solicitudes de ampliación
CREATE PROCEDURE sp_ampliacion_obtener_solicitudes(IN p_estado VARCHAR(20))
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
END //

-- Aprobar ampliación
CREATE PROCEDURE sp_ampliacion_aprobar(
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
            observaciones = CONCAT(CASE WHEN observaciones IS NULL THEN '' ELSE observaciones END, ' | Ampliación: Ampliado por ', v_dias_adicionales, ' días. Motivo: ', p_respuesta)
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
END //

-- Rechazar ampliación
CREATE PROCEDURE sp_ampliacion_rechazar(
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
END //

DELIMITER ;

-- =====================================================
-- 10. ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_prestamos_usuario_estado ON Prestamos(idUsuario, estado);
CREATE INDEX idx_prestamos_libro_estado ON Prestamos(idLibro, estado);
CREATE INDEX idx_prestamos_fecha_estado ON Prestamos(fechaPrestamo, estado);
CREATE INDEX idx_solicitudes_estado_fecha ON solicitudes_prestamo(estado, fecha_solicitud);
CREATE INDEX idx_ampliaciones_estado_fecha ON SolicitudesAmpliacion(estado, fechaSolicitud);

-- =====================================================
-- 11. VISTAS PARA CONSULTAS FRECUENTES
-- =====================================================

-- Vista de préstamos activos con información completa
CREATE VIEW vista_prestamos_activos AS
SELECT 
    p.idPrestamo,
    p.fechaPrestamo,
    p.fechaDevolucionEsperada,
    p.observaciones,
    u.nombre as usuario_nombre,
    u.apellido as usuario_apellido,
    u.email as usuario_email,
    l.titulo as libro_titulo,
    l.autor as libro_autor,
    l.isbn as libro_isbn,
    c.nombre as categoria_nombre,
    DATEDIFF(p.fechaDevolucionEsperada, CURRENT_DATE) as dias_restantes
FROM Prestamos p
INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
INNER JOIN Libros l ON p.idLibro = l.idLibro
INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
WHERE p.fechaDevolucionReal IS NULL;

-- Vista de estadísticas generales
CREATE VIEW vista_estadisticas_generales AS
SELECT 
    (SELECT COUNT(*) FROM Usuarios WHERE activo = TRUE) as total_usuarios,
    (SELECT COUNT(*) FROM Libros WHERE activo = TRUE) as total_libros,
    (SELECT COUNT(*) FROM Prestamos) as total_prestamos,
    (SELECT COUNT(*) FROM Prestamos WHERE fechaDevolucionReal IS NULL) as prestamos_activos,
    (SELECT COUNT(*) FROM Prestamos WHERE fechaDevolucionReal IS NOT NULL) as prestamos_devueltos,
    (SELECT COUNT(*) FROM Prestamos WHERE fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < CURRENT_DATE) as prestamos_vencidos,
    (SELECT COUNT(*) FROM solicitudes_prestamo) as total_solicitudes,
    (SELECT COUNT(*) FROM solicitudes_prestamo WHERE estado = 'Pendiente') as solicitudes_pendientes;

-- =====================================================
-- 12. TRIGGERS PARA MANTENER CONSISTENCIA
-- =====================================================

-- Trigger para actualizar fecha de actualización en libros
DELIMITER //
CREATE TRIGGER tr_libros_actualizar_fecha
    BEFORE UPDATE ON Libros
    FOR EACH ROW
BEGIN
    SET NEW.fecha_actualizacion = NOW();
END //

-- Trigger para actualizar fecha de actualización en préstamos
CREATE TRIGGER tr_prestamos_actualizar_fecha
    BEFORE UPDATE ON Prestamos
    FOR EACH ROW
BEGIN
    SET NEW.fecha_actualizacion = NOW();
END //

-- Trigger para actualizar fecha de actualización en solicitudes
CREATE TRIGGER tr_solicitudes_actualizar_fecha
    BEFORE UPDATE ON solicitudes_prestamo
    FOR EACH ROW
BEGIN
    SET NEW.fecha_actualizacion = NOW();
END //

DELIMITER ;

-- =====================================================
-- 13. CONFIGURACIÓN FINAL
-- =====================================================

-- Configurar variables de sesión para mejor rendimiento
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';

-- Configurar timezone
SET time_zone = '+00:00';

-- =====================================================
-- 14. COMENTARIOS FINALES
-- =====================================================

-- Este script crea un sistema completo de gestión bibliotecaria con:
-- - 8 tablas principales y de soporte
-- - 25+ procedimientos almacenados
-- - Índices optimizados para consultas frecuentes
-- - Vistas para facilitar consultas complejas
-- - Triggers para mantener consistencia de datos
-- - Datos iniciales para comenzar a usar el sistema

-- Para usar este script:
-- 1. Ejecutar en phpMyAdmin o cliente MySQL
-- 2. Verificar que todas las tablas se crearon correctamente
-- 3. Verificar que todos los procedimientos almacenados se instalaron
-- 4. Configurar la conexión en config/database.php
-- 5. Iniciar sesión con usuario: admin, contraseña: password

COMMIT;

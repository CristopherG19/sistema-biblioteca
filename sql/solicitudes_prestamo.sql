-- Script para agregar funcionalidad de solicitudes de préstamo
-- Ejecutar en phpMyAdmin o cliente MySQL

USE biblioteca_db;

-- Crear tabla de solicitudes de préstamo
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
    prestamo_id INT NULL, -- Para vincular cuando se convierte en préstamo
    
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES Libros(idLibro) ON DELETE CASCADE,
    FOREIGN KEY (bibliotecario_id) REFERENCES Usuarios(idUsuario) ON DELETE SET NULL,
    FOREIGN KEY (prestamo_id) REFERENCES Prestamos(idPrestamo) ON DELETE SET NULL,
    
    INDEX idx_estado (estado),
    INDEX idx_fecha_solicitud (fecha_solicitud),
    INDEX idx_usuario (usuario_id),
    INDEX idx_libro (libro_id)
);

-- Stored procedures para solicitudes de préstamo

-- Procedimiento para listar solicitudes con información de usuario y libro
DELIMITER //
CREATE PROCEDURE sp_solicitudes_listar(IN p_estado VARCHAR(20) DEFAULT NULL)
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

-- Procedimiento para listar solicitudes de un usuario específico
DELIMITER //
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

-- Procedimiento para insertar solicitud
DELIMITER //
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
        INSERT INTO solicitudes_prestamo (
            usuario_id, 
            libro_id, 
            observaciones_usuario
        ) VALUES (
            p_usuario_id, 
            p_libro_id, 
            p_observaciones
        );
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status;
    ELSE
        SELECT 0 as idSolicitud, 'no_disponible' as status;
    END IF;
END //

-- Procedimiento para responder solicitud
DELIMITER //
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

-- Procedimiento para obtener estadísticas de solicitudes
DELIMITER //
CREATE PROCEDURE sp_solicitudes_estadisticas()
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas,
        SUM(CASE WHEN DATE(fecha_solicitud) = CURDATE() THEN 1 ELSE 0 END) as solicitudes_hoy
    FROM solicitudes_prestamo;
END //

DELIMITER ;

-- Insertar datos de ejemplo (opcional)
-- INSERT INTO solicitudes_prestamo (usuario_id, libro_id, observaciones_usuario) 
-- VALUES (2, 1, 'Me interesa mucho este libro para mi investigación');

COMMIT;
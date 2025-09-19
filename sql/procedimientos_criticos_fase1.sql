-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS CRÍTICOS - FASE 1
-- Fecha: 18 de septiembre de 2025
-- Descripción: 4 procedimientos críticos identificados en el reporte
-- =====================================================

USE biblioteca_db;

-- 1. PROCEDIMIENTO PARA OBTENER CATEGORÍA POR ID
DELIMITER //
DROP PROCEDURE IF EXISTS sp_categoria_obtener_por_id//
CREATE PROCEDURE sp_categoria_obtener_por_id(
    IN p_id_categoria INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT 
        idCategoria,
        nombre,
        descripcion,
        fechaCreacion
    FROM Categorias 
    WHERE idCategoria = p_id_categoria;
END//

-- 2. PROCEDIMIENTO PARA RESPONDER SOLICITUDES
DELIMITER //
DROP PROCEDURE IF EXISTS sp_solicitud_responder//
CREATE PROCEDURE sp_solicitud_responder(
    IN p_solicitud_id INT,
    IN p_estado VARCHAR(20),
    IN p_bibliotecario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE solicitudes_prestamo 
    SET estado = p_estado, 
        bibliotecario_id = p_bibliotecario_id, 
        observaciones_bibliotecario = p_observaciones, 
        fecha_respuesta = NOW()
    WHERE idSolicitud = p_solicitud_id 
    AND estado = 'Pendiente';

    SET v_affected_rows = ROW_COUNT();

    IF v_affected_rows > 0 THEN
        COMMIT;
        SELECT 'success' as status, v_affected_rows as affected_rows, 'Solicitud respondida correctamente' as message;
    ELSE
        ROLLBACK;
        SELECT 'error' as status, 0 as affected_rows, 'No se pudo responder la solicitud (no existe o ya fue procesada)' as message;
    END IF;
END//

-- 3. PROCEDIMIENTO PARA OBTENER SOLICITUD POR ID
DELIMITER //
DROP PROCEDURE IF EXISTS sp_solicitud_obtener_por_id//
CREATE PROCEDURE sp_solicitud_obtener_por_id(
    IN p_solicitud_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT
        s.idSolicitud,
        s.usuario_id,
        s.libro_id,
        s.fecha_solicitud,
        s.estado,
        s.observaciones_usuario,
        s.bibliotecario_id,
        s.observaciones_bibliotecario,
        s.fecha_respuesta,
        u.nombre as usuario_nombre,
        u.email as usuario_email,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.editorial as libro_editorial,
        c.nombre as categoria_nombre
    FROM solicitudes_prestamo s
    INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE s.idSolicitud = p_solicitud_id;
END//

-- 4. PROCEDIMIENTO PARA OBTENER PRÉSTAMO POR ID
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_por_id//
CREATE PROCEDURE sp_prestamo_obtener_por_id(
    IN p_prestamo_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT 
        p.idPrestamo,
        p.idUsuario,
        p.idLibro,
        p.fechaPrestamo,
        p.fechaDevolucionPrevista,
        p.fechaDevolucionReal,
        p.observaciones,
        p.estado,
        u.nombre as usuario_nombre,
        u.email as usuario_email,
        u.telefono as usuario_telefono,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.editorial as libro_editorial,
        l.isbn as libro_isbn,
        c.nombre as categoria_nombre
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.idPrestamo = p_prestamo_id;
END//

DELIMITER ;

-- Verificar que los procedimientos se crearon correctamente
SELECT 'Procedimientos críticos creados exitosamente' as status;
SHOW PROCEDURE STATUS WHERE Db = 'biblioteca_db' AND Name IN ('sp_categoria_obtener_por_id', 'sp_solicitud_responder', 'sp_solicitud_obtener_por_id', 'sp_prestamo_obtener_por_id');
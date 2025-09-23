-- Script para corregir los procedimientos de ampliación de préstamos
USE biblioteca_db;

-- Eliminar procedimientos existentes
DROP PROCEDURE IF EXISTS sp_ampliacion_solicitar;
DROP PROCEDURE IF EXISTS sp_ampliacion_obtener_solicitudes;
DROP PROCEDURE IF EXISTS sp_ampliacion_aprobar;
DROP PROCEDURE IF EXISTS sp_ampliacion_rechazar;

-- Crear procedimiento para solicitar ampliación
DELIMITER //
CREATE PROCEDURE sp_ampliacion_solicitar(
    IN p_prestamo_id INT,
    IN p_dias_adicionales INT,
    IN p_motivo TEXT
)
BEGIN
    DECLARE v_existe INT DEFAULT 0;
    
    -- Verificar que el préstamo existe y está activo
    SELECT COUNT(*) INTO v_existe
    FROM prestamos
    WHERE idPrestamo = p_prestamo_id AND fechaDevolucionReal IS NULL;
    
    IF v_existe > 0 THEN
        INSERT INTO solicitudesampliacion (idPrestamo, diasAdicionales, motivo, estado, fechaSolicitud)
        VALUES (p_prestamo_id, p_dias_adicionales, p_motivo, 'Pendiente', NOW());
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status, 'Solicitud de ampliación enviada' as message;
    ELSE
        SELECT 0 as idSolicitud, 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    END IF;
END //

-- Crear procedimiento para obtener solicitudes
CREATE PROCEDURE sp_ampliacion_obtener_solicitudes(IN p_estado VARCHAR(20))
BEGIN
    SELECT 
        sa.idSolicitud,
        sa.idPrestamo,
        sa.diasAdicionales,
        sa.motivo,
        sa.fechaSolicitud,
        sa.estado,
        sa.respuestaBibliotecario,
        sa.fechaRespuesta,
        p.fechaDevolucionEsperada,
        u.nombre as usuario_nombre,
        u.apellido as usuario_apellido,
        l.titulo as libro_titulo,
        l.autor as libro_autor
    FROM solicitudesampliacion sa
    INNER JOIN prestamos p ON sa.idPrestamo = p.idPrestamo
    INNER JOIN usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN libros l ON p.idLibro = l.idLibro
    WHERE (p_estado IS NULL OR sa.estado = p_estado)
    ORDER BY sa.fechaSolicitud DESC;
END //

-- Crear procedimiento para aprobar ampliación
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
    FROM solicitudesampliacion
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    IF v_prestamo_id IS NOT NULL THEN
        -- Obtener fecha actual de devolución
        SELECT fechaDevolucionEsperada INTO v_fecha_actual
        FROM prestamos
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar fecha de devolución
        UPDATE prestamos
        SET fechaDevolucionEsperada = DATE_ADD(v_fecha_actual, INTERVAL v_dias_adicionales DAY),
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | Ampliación: Ampliado por ', v_dias_adicionales, ' días. Motivo: ', p_respuesta)
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar solicitud
        UPDATE solicitudesampliacion
        SET estado = 'Aprobada',
            idBibliotecario = p_bibliotecario_id,
            respuestaBibliotecario = p_respuesta,
            fechaRespuesta = NOW()
        WHERE idSolicitud = p_solicitud_id;
        
        SELECT 'success' as status, 'Ampliación aprobada exitosamente' as message;
    ELSE
        SELECT 'error' as status, 'Solicitud no encontrada o ya procesada' as message;
    END IF;
END //

-- Crear procedimiento para rechazar ampliación
CREATE PROCEDURE sp_ampliacion_rechazar(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    UPDATE solicitudesampliacion
    SET estado = 'Rechazada',
        idBibliotecario = p_bibliotecario_id,
        respuestaBibliotecario = p_respuesta,
        fechaRespuesta = NOW()
    WHERE idSolicitud = p_solicitud_id AND estado = 'Pendiente';
    
    SELECT 'success' as status, 'Solicitud rechazada' as message;
END //

DELIMITER ;

-- Verificar que los procedimientos se crearon correctamente
SHOW PROCEDURE STATUS WHERE Name LIKE 'sp_ampliacion%';

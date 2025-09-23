-- Script para eliminar el estado redundante "APROBADA" de las solicitudes de préstamo
USE biblioteca_db;

-- 1. Actualizar cualquier solicitud que esté en estado "Aprobada" a "Convertida"
UPDATE solicitudes_prestamo 
SET estado = 'Convertida' 
WHERE estado = 'Aprobada';

-- 2. Modificar la tabla para eliminar el estado "Aprobada" del ENUM
ALTER TABLE solicitudes_prestamo 
MODIFY COLUMN estado ENUM('Pendiente','Rechazada','Convertida') 
COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente';

-- 3. Actualizar el procedimiento de estadísticas para eliminar referencias a "Aprobada"
DROP PROCEDURE IF EXISTS sp_solicitudes_estadisticas;
DELIMITER //
CREATE PROCEDURE sp_solicitudes_estadisticas()
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas,
        SUM(CASE WHEN DATE(fecha_solicitud) = CURDATE() THEN 1 ELSE 0 END) as solicitudes_hoy
    FROM solicitudes_prestamo;
END //
DELIMITER ;

-- 4. Actualizar el procedimiento de estadísticas por usuario
DROP PROCEDURE IF EXISTS sp_solicitudes_estadisticas_usuario;
DELIMITER //
CREATE PROCEDURE sp_solicitudes_estadisticas_usuario(IN p_usuario_id INT)
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
    FROM solicitudes_prestamo
    WHERE usuario_id = p_usuario_id;
END //
DELIMITER ;

-- 5. Verificar los cambios
SELECT 'Estados disponibles después del cambio:' as mensaje;
SHOW COLUMNS FROM solicitudes_prestamo LIKE 'estado';

SELECT 'Estadísticas actuales:' as mensaje;
CALL sp_solicitudes_estadisticas();

-- Script para corregir el procedimiento de estadísticas de préstamos
USE biblioteca_db;

-- Eliminar el procedimiento existente
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_estadisticas;

-- Crear el procedimiento corregido
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_estadisticas()
BEGIN
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN fechaDevolucionReal IS NULL THEN 1 END) as activos,
        COUNT(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 END) as devueltos,
        COUNT(CASE WHEN fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < NOW() THEN 1 END) as vencidos
    FROM Prestamos;
END //
DELIMITER ;

-- Verificar que el procedimiento se creó correctamente
SHOW PROCEDURE STATUS WHERE Name = 'sp_prestamo_obtener_estadisticas';

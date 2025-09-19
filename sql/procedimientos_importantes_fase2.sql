-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS IMPORTANTES - FASE 2
-- Fecha: 18 de septiembre de 2025
-- Descripción: 9 procedimientos importantes para libros y préstamos
-- =====================================================

USE biblioteca_db;

-- 5. PROCEDIMIENTO PARA OBTENER LIBROS RECIENTES
DELIMITER //
DROP PROCEDURE IF EXISTS sp_libro_obtener_recientes//
CREATE PROCEDURE sp_libro_obtener_recientes(
    IN p_limite INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT 
        l.idLibro,
        l.idCategoria,
        l.titulo,
        l.autor,
        l.editorial,
        l.anio,
        l.isbn,
        l.stock,
        l.disponible,
        l.descripcion,
        l.archivo_pdf,
        l.numero_paginas,
        l.tamano_archivo,
        l.fechaRegistro,
        c.nombre as categoria
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    ORDER BY l.idLibro DESC 
    LIMIT p_limite;
END//

-- 6. PROCEDIMIENTO PARA ACTUALIZAR STOCK AL PRESTAR
DELIMITER //
DROP PROCEDURE IF EXISTS sp_libro_actualizar_stock_prestamo//
CREATE PROCEDURE sp_libro_actualizar_stock_prestamo(
    IN p_libro_id INT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Verificar disponibilidad
    SELECT disponible INTO v_disponible 
    FROM Libros 
    WHERE idLibro = p_libro_id;

    IF v_disponible > 0 THEN
        UPDATE Libros 
        SET disponible = disponible - 1 
        WHERE idLibro = p_libro_id;
        
        SET v_affected_rows = ROW_COUNT();
        COMMIT;
        SELECT 'success' as status, v_affected_rows as affected_rows, 'Stock actualizado correctamente' as message;
    ELSE
        ROLLBACK;
        SELECT 'error' as status, 0 as affected_rows, 'No hay ejemplares disponibles' as message;
    END IF;
END//

-- 7. PROCEDIMIENTO PARA ACTUALIZAR STOCK AL DEVOLVER
DELIMITER //
DROP PROCEDURE IF EXISTS sp_libro_actualizar_stock_devolucion//
CREATE PROCEDURE sp_libro_actualizar_stock_devolucion(
    IN p_libro_id INT
)
BEGIN
    DECLARE v_stock_total INT DEFAULT 0;
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Verificar que no exceda el stock total
    SELECT stock, disponible INTO v_stock_total, v_disponible 
    FROM Libros 
    WHERE idLibro = p_libro_id;

    IF v_disponible < v_stock_total THEN
        UPDATE Libros 
        SET disponible = disponible + 1 
        WHERE idLibro = p_libro_id;
        
        SET v_affected_rows = ROW_COUNT();
        COMMIT;
        SELECT 'success' as status, v_affected_rows as affected_rows, 'Stock actualizado correctamente' as message;
    ELSE
        ROLLBACK;
        SELECT 'error' as status, 0 as affected_rows, 'No se puede incrementar: stock ya está completo' as message;
    END IF;
END//

-- 8. PROCEDIMIENTO PARA INSERTAR PRÉSTAMO COMPLETO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_insertar_completo//
CREATE PROCEDURE sp_prestamo_insertar_completo(
    IN p_usuario_id INT,
    IN p_libro_id INT,
    IN p_fecha_devolucion_prevista DATE,
    IN p_estado VARCHAR(20),
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_libro_disponible INT DEFAULT 0;
    DECLARE v_prestamo_id INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Verificar disponibilidad del libro
    SELECT disponible INTO v_libro_disponible 
    FROM Libros 
    WHERE idLibro = p_libro_id;

    IF v_libro_disponible > 0 THEN
        -- Insertar el préstamo
        INSERT INTO Prestamos (
            idUsuario, 
            idLibro, 
            fechaPrestamo, 
            fechaDevolucionPrevista, 
            estado, 
            observaciones
        ) VALUES (
            p_usuario_id,
            p_libro_id,
            NOW(),
            p_fecha_devolucion_prevista,
            COALESCE(p_estado, 'Activo'),
            p_observaciones
        );

        SET v_prestamo_id = LAST_INSERT_ID();
        SET v_affected_rows = ROW_COUNT();

        -- Actualizar stock del libro
        UPDATE Libros 
        SET disponible = disponible - 1 
        WHERE idLibro = p_libro_id;

        COMMIT;
        SELECT 'success' as status, v_prestamo_id as prestamo_id, v_affected_rows as affected_rows, 'Préstamo creado exitosamente' as message;
    ELSE
        ROLLBACK;
        SELECT 'error' as status, 0 as prestamo_id, 0 as affected_rows, 'El libro no está disponible' as message;
    END IF;
END//

-- 9. PROCEDIMIENTO PARA DEVOLVER PRÉSTAMO COMPLETO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_devolver_completo//
CREATE PROCEDURE sp_prestamo_devolver_completo(
    IN p_prestamo_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_libro_id INT DEFAULT 0;
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE v_estado_actual VARCHAR(20) DEFAULT '';
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Obtener información del préstamo
    SELECT idLibro, estado INTO v_libro_id, v_estado_actual
    FROM Prestamos 
    WHERE idPrestamo = p_prestamo_id;

    IF v_libro_id > 0 AND v_estado_actual = 'Activo' THEN
        -- Actualizar el préstamo
        UPDATE Prestamos 
        SET fechaDevolucionReal = NOW(),
            estado = 'Devuelto',
            observaciones = CONCAT(COALESCE(observaciones, ''), 
                                 CASE WHEN observaciones IS NOT NULL THEN '\n' ELSE '' END,
                                 'Devuelto el: ', NOW(),
                                 CASE WHEN p_observaciones IS NOT NULL THEN CONCAT('\nObservaciones: ', p_observaciones) ELSE '' END)
        WHERE idPrestamo = p_prestamo_id;

        SET v_affected_rows = ROW_COUNT();

        -- Restaurar stock del libro
        UPDATE Libros 
        SET disponible = disponible + 1 
        WHERE idLibro = v_libro_id;

        COMMIT;
        SELECT 'success' as status, v_affected_rows as affected_rows, 'Préstamo devuelto exitosamente' as message;
    ELSE
        ROLLBACK;
        SELECT 'error' as status, 0 as affected_rows, 'Préstamo no encontrado o ya devuelto' as message;
    END IF;
END//

-- 10. PROCEDIMIENTO PARA OBTENER PRÉSTAMOS POR USUARIO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_por_usuario//
CREATE PROCEDURE sp_prestamo_obtener_por_usuario(
    IN p_usuario_id INT
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
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.editorial as libro_editorial,
        c.nombre as categoria_nombre,
        DATEDIFF(p.fechaDevolucionPrevista, NOW()) as dias_restantes
    FROM Prestamos p
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.idUsuario = p_usuario_id
    ORDER BY p.fechaPrestamo DESC;
END//

-- 11. PROCEDIMIENTO PARA OBTENER PRÉSTAMOS ACTIVOS
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_activos//
CREATE PROCEDURE sp_prestamo_obtener_activos()
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
        p.observaciones,
        p.estado,
        u.nombre as usuario_nombre,
        u.email as usuario_email,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        c.nombre as categoria_nombre,
        DATEDIFF(p.fechaDevolucionPrevista, NOW()) as dias_restantes,
        CASE 
            WHEN DATEDIFF(p.fechaDevolucionPrevista, NOW()) < 0 THEN 'Vencido'
            WHEN DATEDIFF(p.fechaDevolucionPrevista, NOW()) <= 3 THEN 'Por vencer'
            ELSE 'Vigente'
        END as status_devolucion
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.estado = 'Activo'
    ORDER BY p.fechaDevolucionPrevista ASC;
END//

-- 12. PROCEDIMIENTO PARA OBTENER PRÉSTAMOS VENCIDOS
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_vencidos//
CREATE PROCEDURE sp_prestamo_obtener_vencidos()
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
        p.observaciones,
        p.estado,
        u.nombre as usuario_nombre,
        u.email as usuario_email,
        u.telefono as usuario_telefono,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        c.nombre as categoria_nombre,
        ABS(DATEDIFF(p.fechaDevolucionPrevista, NOW())) as dias_vencido
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE p.estado = 'Activo' 
    AND p.fechaDevolucionPrevista < NOW()
    ORDER BY p.fechaDevolucionPrevista ASC;
END//

-- 13. PROCEDIMIENTO PARA OBTENER PRÉSTAMOS POR LIBRO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_por_libro//
CREATE PROCEDURE sp_prestamo_obtener_por_libro(
    IN p_libro_id INT
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
        CASE 
            WHEN p.estado = 'Activo' AND p.fechaDevolucionPrevista < NOW() THEN 'Vencido'
            WHEN p.estado = 'Activo' THEN 'Activo'
            ELSE p.estado
        END as estado_detallado
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    WHERE p.idLibro = p_libro_id
    ORDER BY p.fechaPrestamo DESC;
END//

DELIMITER ;

-- Verificar que los procedimientos se crearon correctamente
SELECT 'Procedimientos importantes creados exitosamente - Fase 2' as status;
SHOW PROCEDURE STATUS WHERE Db = 'biblioteca_db' AND Name IN (
    'sp_libro_obtener_recientes', 
    'sp_libro_actualizar_stock_prestamo', 
    'sp_libro_actualizar_stock_devolucion',
    'sp_prestamo_insertar_completo',
    'sp_prestamo_devolver_completo',
    'sp_prestamo_obtener_por_usuario',
    'sp_prestamo_obtener_activos',
    'sp_prestamo_obtener_vencidos',
    'sp_prestamo_obtener_por_libro'
);
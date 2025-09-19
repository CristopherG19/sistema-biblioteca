-- Script completo de procedimientos almacenados para el sistema de biblioteca
-- Ejecutar en phpMyAdmin o cliente MySQL

USE biblioteca_db;

-- Eliminar procedimientos existentes si existen
DROP PROCEDURE IF EXISTS sp_solicitudes_listar;
DROP PROCEDURE IF EXISTS sp_solicitudes_usuario;
DROP PROCEDURE IF EXISTS sp_solicitud_insertar;
DROP PROCEDURE IF EXISTS sp_solicitud_responder;
DROP PROCEDURE IF EXISTS sp_solicitudes_estadisticas;
DROP PROCEDURE IF EXISTS sp_verificar_solicitud_existente;
DROP PROCEDURE IF EXISTS sp_libros_disponibles_solicitud;
DROP PROCEDURE IF EXISTS sp_prestamos_listar;
DROP PROCEDURE IF EXISTS sp_prestamo_insertar;
DROP PROCEDURE IF EXISTS sp_prestamo_devolver;
DROP PROCEDURE IF EXISTS sp_prestamos_vencidos;
DROP PROCEDURE IF EXISTS sp_prestamos_estadisticas;

-- =====================================================
-- PROCEDIMIENTOS PARA SOLICITUDES DE PRÉSTAMO
-- =====================================================

-- Procedimiento para listar solicitudes con información completa
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
        '' as usuario_apellido,
        u.email as usuario_email,
        u.telefono as usuario_telefono,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        l.disponible as libro_disponible,
        c.nombre as categoria_nombre,
        b.nombre as bibliotecario_nombre,
        '' as bibliotecario_apellido
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
        '' as bibliotecario_apellido
    FROM solicitudes_prestamo s
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    LEFT JOIN Usuarios b ON s.bibliotecario_id = b.idUsuario
    WHERE s.usuario_id = p_usuario_id
    ORDER BY s.fecha_solicitud DESC;
END //

-- Procedimiento para insertar nueva solicitud
DELIMITER //
CREATE PROCEDURE sp_solicitud_insertar(
    IN p_usuario_id INT,
    IN p_libro_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE v_solicitud_existente INT DEFAULT 0;

    -- Verificar si el libro está disponible
    SELECT disponible INTO v_disponible
    FROM Libros
    WHERE idLibro = p_libro_id;

    -- Verificar si ya existe una solicitud pendiente o aprobada para este usuario y libro
    SELECT COUNT(*) INTO v_solicitud_existente
    FROM solicitudes_prestamo
    WHERE usuario_id = p_usuario_id 
    AND libro_id = p_libro_id 
    AND estado IN ('Pendiente', 'Aprobada');

    -- Validaciones
    IF v_disponible <= 0 THEN
        SELECT 0 as idSolicitud, 'no_disponible' as status, 'Libro no disponible' as message;
    ELSEIF v_solicitud_existente > 0 THEN
        SELECT 0 as idSolicitud, 'solicitud_existente' as status, 'Ya tienes una solicitud pendiente o aprobada para este libro' as message;
    ELSE
        INSERT INTO solicitudes_prestamo (
            usuario_id,
            libro_id,
            observaciones_usuario
        ) VALUES (
            p_usuario_id,
            p_libro_id,
            p_observaciones
        );

        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status, 'Solicitud creada exitosamente' as message;
    END IF;
END //

-- Procedimiento para verificar solicitud existente
DELIMITER //
CREATE PROCEDURE sp_verificar_solicitud_existente(
    IN p_usuario_id INT,
    IN p_libro_id INT
)
BEGIN
    SELECT COUNT(*) as existe
    FROM solicitudes_prestamo
    WHERE usuario_id = p_usuario_id 
    AND libro_id = p_libro_id 
    AND estado IN ('Pendiente', 'Aprobada');
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
    DECLARE v_affected_rows INT DEFAULT 0;
    
    UPDATE solicitudes_prestamo
    SET
        estado = p_estado,
        bibliotecario_id = p_bibliotecario_id,
        observaciones_bibliotecario = p_observaciones,
        fecha_respuesta = NOW()
    WHERE idSolicitud = p_solicitud_id
    AND estado = 'Pendiente';

    SET v_affected_rows = ROW_COUNT();
    SELECT v_affected_rows as affected_rows, 
           CASE WHEN v_affected_rows > 0 THEN 'success' ELSE 'error' END as status;
END //

-- Procedimiento para aprobar solicitud y crear préstamo
DELIMITER //
CREATE PROCEDURE sp_solicitud_aprobar_y_crear_prestamo(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_fecha_devolucion DATE,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_usuario_id INT;
    DECLARE v_libro_id INT;
    DECLARE v_disponible INT;
    DECLARE v_prestamo_id INT;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 as prestamo_id, 'error' as status, 'Error al procesar solicitud' as message;
    END;

    START TRANSACTION;

    -- Obtener datos de la solicitud
    SELECT usuario_id, libro_id INTO v_usuario_id, v_libro_id
    FROM solicitudes_prestamo s
    INNER JOIN Libros l ON s.libro_id = l.idLibro
    WHERE s.idSolicitud = p_solicitud_id 
    AND s.estado = 'Pendiente'
    AND l.disponible > 0;

    -- Verificar que se encontraron los datos
    IF v_usuario_id IS NULL THEN
        ROLLBACK;
        SELECT 0 as prestamo_id, 'error' as status, 'Solicitud no válida o libro no disponible' as message;
    ELSE
        -- Crear préstamo
        INSERT INTO Prestamos (idLibro, idUsuario, fecha_prestamo, fecha_devolucion_prevista, observaciones)
        VALUES (v_libro_id, v_usuario_id, NOW(), p_fecha_devolucion, p_observaciones);
        
        SET v_prestamo_id = LAST_INSERT_ID();
        
        -- Actualizar stock del libro
        UPDATE Libros SET disponible = disponible - 1 WHERE idLibro = v_libro_id;
        
        -- Actualizar solicitud
        UPDATE solicitudes_prestamo 
        SET estado = 'Convertida', 
            bibliotecario_id = p_bibliotecario_id, 
            observaciones_bibliotecario = p_observaciones,
            fecha_respuesta = NOW(), 
            prestamo_id = v_prestamo_id
        WHERE idSolicitud = p_solicitud_id;
        
        COMMIT;
        SELECT v_prestamo_id as prestamo_id, 'success' as status, 'Préstamo creado exitosamente' as message;
    END IF;
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

-- Procedimiento para obtener estadísticas por usuario
DELIMITER //
CREATE PROCEDURE sp_solicitudes_estadisticas_usuario(IN p_usuario_id INT)
BEGIN
    SELECT
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
    FROM solicitudes_prestamo
    WHERE usuario_id = p_usuario_id;
END //

-- Procedimiento para obtener libros disponibles para solicitud
DELIMITER //
CREATE PROCEDURE sp_libros_disponibles_solicitud()
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
        c.nombre as categoria
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.disponible > 0
    ORDER BY l.titulo;
END //

-- =====================================================
-- PROCEDIMIENTOS PARA PRÉSTAMOS
-- =====================================================

-- Procedimiento para listar préstamos
DELIMITER //
CREATE PROCEDURE sp_prestamos_listar(
    IN p_usuario_id INT DEFAULT NULL,
    IN p_estado VARCHAR(20) DEFAULT NULL
)
BEGIN
    SELECT 
        p.idPrestamo,
        p.idLibro,
        p.idUsuario,
        p.fecha_prestamo,
        p.fecha_devolucion_prevista,
        p.fecha_devolucion_real,
        p.observaciones,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        l.isbn as libro_isbn,
        u.nombre as usuario_nombre,
        u.email as usuario_email,
        c.nombre as categoria,
        CASE 
            WHEN p.fecha_devolucion_real IS NOT NULL THEN 'Devuelto'
            WHEN p.fecha_devolucion_prevista < CURDATE() THEN 'Vencido'
            ELSE 'Activo'
        END as estado
    FROM Prestamos p
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE (p_usuario_id IS NULL OR p.idUsuario = p_usuario_id)
    AND (p_estado IS NULL OR 
         (p_estado = 'Activo' AND p.fecha_devolucion_real IS NULL AND p.fecha_devolucion_prevista >= CURDATE()) OR
         (p_estado = 'Vencido' AND p.fecha_devolucion_real IS NULL AND p.fecha_devolucion_prevista < CURDATE()) OR
         (p_estado = 'Devuelto' AND p.fecha_devolucion_real IS NOT NULL))
    ORDER BY p.fecha_prestamo DESC;
END //

-- Procedimiento para insertar préstamo
DELIMITER //
CREATE PROCEDURE sp_prestamo_insertar(
    IN p_libro_id INT,
    IN p_usuario_id INT,
    IN p_fecha_devolucion DATE,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 as idPrestamo, 'error' as status, 'Error al crear préstamo' as message;
    END;

    START TRANSACTION;

    -- Verificar disponibilidad
    SELECT disponible INTO v_disponible
    FROM Libros
    WHERE idLibro = p_libro_id;

    IF v_disponible <= 0 THEN
        ROLLBACK;
        SELECT 0 as idPrestamo, 'no_disponible' as status, 'Libro no disponible' as message;
    ELSE
        -- Crear préstamo
        INSERT INTO Prestamos (idLibro, idUsuario, fecha_prestamo, fecha_devolucion_prevista, observaciones)
        VALUES (p_libro_id, p_usuario_id, NOW(), p_fecha_devolucion, p_observaciones);
        
        -- Actualizar stock
        UPDATE Libros SET disponible = disponible - 1 WHERE idLibro = p_libro_id;
        
        COMMIT;
        SELECT LAST_INSERT_ID() as idPrestamo, 'success' as status, 'Préstamo creado exitosamente' as message;
    END IF;
END //

-- Procedimiento para devolver préstamo
DELIMITER //
CREATE PROCEDURE sp_prestamo_devolver(
    IN p_prestamo_id INT,
    IN p_observaciones TEXT DEFAULT NULL
)
BEGIN
    DECLARE v_libro_id INT;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' as status, 'Error al procesar devolución' as message;
    END;

    START TRANSACTION;

    -- Obtener el libro_id del préstamo
    SELECT idLibro INTO v_libro_id
    FROM Prestamos
    WHERE idPrestamo = p_prestamo_id
    AND fecha_devolucion_real IS NULL;

    IF v_libro_id IS NULL THEN
        ROLLBACK;
        SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    ELSE
        -- Actualizar préstamo
        UPDATE Prestamos 
        SET fecha_devolucion_real = NOW(),
            observaciones = CONCAT(IFNULL(observaciones, ''), 
                                 CASE WHEN observaciones IS NOT NULL THEN '. ' ELSE '' END,
                                 IFNULL(p_observaciones, ''))
        WHERE idPrestamo = p_prestamo_id;
        
        -- Incrementar stock
        UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = v_libro_id;
        
        COMMIT;
        SELECT 'success' as status, 'Libro devuelto exitosamente' as message;
    END IF;
END //

-- Procedimiento para obtener préstamos vencidos
DELIMITER //
CREATE PROCEDURE sp_prestamos_vencidos()
BEGIN
    SELECT 
        p.idPrestamo,
        p.idLibro,
        p.idUsuario,
        p.fecha_prestamo,
        p.fecha_devolucion_prevista,
        p.observaciones,
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        u.nombre as usuario_nombre,
        u.email as usuario_email,
        u.telefono as usuario_telefono,
        DATEDIFF(CURDATE(), p.fecha_devolucion_prevista) as dias_vencido
    FROM Prestamos p
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    WHERE p.fecha_devolucion_real IS NULL
    AND p.fecha_devolucion_prevista < CURDATE()
    ORDER BY p.fecha_devolucion_prevista ASC;
END //

-- Procedimiento para estadísticas de préstamos
DELIMITER //
CREATE PROCEDURE sp_prestamos_estadisticas()
BEGIN
    SELECT
        COUNT(*) as total_prestamos,
        SUM(CASE WHEN fecha_devolucion_real IS NULL THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN fecha_devolucion_real IS NOT NULL THEN 1 ELSE 0 END) as devueltos,
        SUM(CASE WHEN fecha_devolucion_real IS NULL AND fecha_devolucion_prevista < CURDATE() THEN 1 ELSE 0 END) as vencidos,
        SUM(CASE WHEN DATE(fecha_prestamo) = CURDATE() THEN 1 ELSE 0 END) as prestamos_hoy
    FROM Prestamos;
END //

DELIMITER ;

-- Mensaje de confirmación
SELECT 'Todos los procedimientos almacenados han sido creados exitosamente' as mensaje;

COMMIT;
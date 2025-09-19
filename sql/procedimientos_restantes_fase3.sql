-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS RESTANTES - FASE 3
-- Fecha: 18 de septiembre de 2025
-- Descripción: Procedimientos restantes para usuarios, libros y solicitudes
-- =====================================================

USE biblioteca_db;

-- PROCEDIMIENTOS PARA USUARIOS
-- 14. PROCEDIMIENTO PARA OBTENER USUARIOS POR ROL
DELIMITER //
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_rol//
CREATE PROCEDURE sp_usuario_obtener_por_rol(
    IN p_rol_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT 
        u.idUsuario,
        u.nombre,
        u.usuario,
        u.rol,
        u.email,
        u.telefono,
        u.fechaRegistro,
        u.ultimoAcceso,
        r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.rol = p_rol_id
    ORDER BY u.nombre;
END//

-- 15. PROCEDIMIENTO PARA BUSCAR USUARIOS
DELIMITER //
DROP PROCEDURE IF EXISTS sp_usuario_buscar//
CREATE PROCEDURE sp_usuario_buscar(
    IN p_termino VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT 
        u.idUsuario,
        u.nombre,
        u.usuario,
        u.rol,
        u.email,
        u.telefono,
        u.fechaRegistro,
        u.ultimoAcceso,
        r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.nombre LIKE CONCAT('%', p_termino, '%')
       OR u.usuario LIKE CONCAT('%', p_termino, '%')
       OR u.email LIKE CONCAT('%', p_termino, '%')
    ORDER BY u.nombre;
END//

-- 16. PROCEDIMIENTO PARA VERIFICAR SI USUARIO EXISTE
DELIMITER //
DROP PROCEDURE IF EXISTS sp_usuario_verificar_existe//
CREATE PROCEDURE sp_usuario_verificar_existe(
    IN p_usuario VARCHAR(50),
    IN p_excluir_id INT
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT COUNT(*) INTO v_count
    FROM Usuarios 
    WHERE usuario = p_usuario 
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id);

    SELECT v_count as existe;
END//

-- 17. PROCEDIMIENTO PARA VERIFICAR SI EMAIL EXISTE
DELIMITER //
DROP PROCEDURE IF EXISTS sp_usuario_verificar_email//
CREATE PROCEDURE sp_usuario_verificar_email(
    IN p_email VARCHAR(100),
    IN p_excluir_id INT
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT COUNT(*) INTO v_count
    FROM Usuarios 
    WHERE email = p_email 
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id);

    SELECT v_count as existe;
END//

-- 18. PROCEDIMIENTO PARA OBTENER USUARIO POR USERNAME
DELIMITER //
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_username//
CREATE PROCEDURE sp_usuario_obtener_por_username(
    IN p_username VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT 
        u.idUsuario,
        u.nombre,
        u.usuario,
        u.clave,
        u.rol,
        u.email,
        u.telefono,
        u.fechaRegistro,
        u.ultimoAcceso,
        r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.usuario = p_username;
END//

-- 19. PROCEDIMIENTO PARA ACTUALIZAR ÚLTIMO ACCESO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_ultimo_acceso//
CREATE PROCEDURE sp_usuario_actualizar_ultimo_acceso(
    IN p_usuario_id INT
)
BEGIN
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    UPDATE Usuarios 
    SET ultimoAcceso = NOW() 
    WHERE idUsuario = p_usuario_id;

    SET v_affected_rows = ROW_COUNT();
    SELECT v_affected_rows as affected_rows;
END//

-- PROCEDIMIENTOS ADICIONALES PARA LIBROS
-- 20. PROCEDIMIENTO PARA BUSCAR LIBROS POR TÍTULO/AUTOR
DELIMITER //
DROP PROCEDURE IF EXISTS sp_libro_buscar_por_titulo_autor//
CREATE PROCEDURE sp_libro_buscar_por_titulo_autor(
    IN p_termino VARCHAR(255)
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
    WHERE l.titulo LIKE CONCAT('%', p_termino, '%')
       OR l.autor LIKE CONCAT('%', p_termino, '%')
       OR l.isbn LIKE CONCAT('%', p_termino, '%')
    ORDER BY l.titulo;
END//

-- 21. PROCEDIMIENTO PARA VERIFICAR SI ISBN EXISTE
DELIMITER //
DROP PROCEDURE IF EXISTS sp_libro_verificar_isbn_existe//
CREATE PROCEDURE sp_libro_verificar_isbn_existe(
    IN p_isbn VARCHAR(20),
    IN p_excluir_id INT
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT COUNT(*) INTO v_count
    FROM Libros 
    WHERE isbn = p_isbn 
    AND (p_excluir_id IS NULL OR idLibro != p_excluir_id);

    SELECT v_count as existe;
END//

-- 22. PROCEDIMIENTO PARA ACTUALIZAR PDF DE LIBRO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_libro_actualizar_pdf//
CREATE PROCEDURE sp_libro_actualizar_pdf(
    IN p_libro_id INT,
    IN p_archivo_pdf VARCHAR(255),
    IN p_numero_paginas INT,
    IN p_tamano_archivo BIGINT
)
BEGIN
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    UPDATE Libros 
    SET archivo_pdf = p_archivo_pdf,
        numero_paginas = p_numero_paginas,
        tamano_archivo = p_tamano_archivo
    WHERE idLibro = p_libro_id;

    SET v_affected_rows = ROW_COUNT();
    SELECT 'success' as status, v_affected_rows as affected_rows, 'PDF actualizado correctamente' as message;
END//

-- PROCEDIMIENTOS ADICIONALES PARA PRÉSTAMOS
-- 23. PROCEDIMIENTO PARA OBTENER PRÉSTAMO USUARIO-LIBRO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_usuario_libro//
CREATE PROCEDURE sp_prestamo_obtener_usuario_libro(
    IN p_usuario_id INT,
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
        l.titulo as libro_titulo,
        l.autor as libro_autor,
        u.nombre as usuario_nombre
    FROM Prestamos p
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    WHERE p.idUsuario = p_usuario_id 
    AND p.idLibro = p_libro_id
    AND p.estado = 'Activo'
    ORDER BY p.fechaPrestamo DESC
    LIMIT 1;
END//

-- 24. PROCEDIMIENTO PARA VALIDAR DISPONIBILIDAD
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_validar_disponibilidad//
CREATE PROCEDURE sp_prestamo_validar_disponibilidad(
    IN p_libro_id INT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT disponible INTO v_disponible
    FROM Libros 
    WHERE idLibro = p_libro_id;

    SELECT CASE WHEN v_disponible > 0 THEN 1 ELSE 0 END as disponible;
END//

-- 25. PROCEDIMIENTO PARA ACTUALIZAR OBSERVACIONES DE PRÉSTAMO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_actualizar_observaciones//
CREATE PROCEDURE sp_prestamo_actualizar_observaciones(
    IN p_prestamo_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    UPDATE Prestamos 
    SET observaciones = p_observaciones
    WHERE idPrestamo = p_prestamo_id;

    SET v_affected_rows = ROW_COUNT();
    SELECT v_affected_rows as affected_rows;
END//

-- 26. PROCEDIMIENTO PARA ELIMINAR PRÉSTAMO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_prestamo_eliminar//
CREATE PROCEDURE sp_prestamo_eliminar(
    IN p_prestamo_id INT
)
BEGIN
    DECLARE v_libro_id INT DEFAULT 0;
    DECLARE v_estado VARCHAR(20) DEFAULT '';
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Obtener información del préstamo antes de eliminar
    SELECT idLibro, estado INTO v_libro_id, v_estado
    FROM Prestamos 
    WHERE idPrestamo = p_prestamo_id;

    IF v_libro_id > 0 THEN
        -- Eliminar el préstamo
        DELETE FROM Prestamos WHERE idPrestamo = p_prestamo_id;
        SET v_affected_rows = ROW_COUNT();

        -- Si el préstamo estaba activo, restaurar stock
        IF v_estado = 'Activo' THEN
            UPDATE Libros 
            SET disponible = disponible + 1 
            WHERE idLibro = v_libro_id;
        END IF;

        COMMIT;
        SELECT 'success' as status, v_affected_rows as affected_rows, 'Préstamo eliminado correctamente' as message;
    ELSE
        ROLLBACK;
        SELECT 'error' as status, 0 as affected_rows, 'Préstamo no encontrado' as message;
    END IF;
END//

-- PROCEDIMIENTOS ADICIONALES PARA SOLICITUDES
-- 27. PROCEDIMIENTO PARA OBTENER ESTADÍSTICAS DE SOLICITUDES POR USUARIO
DELIMITER //
DROP PROCEDURE IF EXISTS sp_solicitudes_estadisticas_usuario//
CREATE PROCEDURE sp_solicitudes_estadisticas_usuario(
    IN p_usuario_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
    FROM solicitudes_prestamo
    WHERE usuario_id = p_usuario_id;
END//

-- 28. PROCEDIMIENTO PARA CANCELAR SOLICITUD
DELIMITER //
DROP PROCEDURE IF EXISTS sp_solicitud_cancelar//
CREATE PROCEDURE sp_solicitud_cancelar(
    IN p_solicitud_id INT,
    IN p_usuario_id INT
)
BEGIN
    DECLARE v_affected_rows INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    UPDATE solicitudes_prestamo 
    SET estado = 'Rechazada', 
        observaciones_bibliotecario = 'Cancelada por el usuario',
        fecha_respuesta = NOW()
    WHERE idSolicitud = p_solicitud_id 
    AND usuario_id = p_usuario_id 
    AND estado = 'Pendiente';

    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 'success' as status, v_affected_rows as affected_rows, 'Solicitud cancelada correctamente' as message;
    ELSE
        SELECT 'error' as status, 0 as affected_rows, 'No se pudo cancelar la solicitud' as message;
    END IF;
END//

DELIMITER ;

-- Verificar que los procedimientos se crearon correctamente
SELECT 'Procedimientos restantes creados exitosamente - Fase 3' as status;
SHOW PROCEDURE STATUS WHERE Db = 'biblioteca_db' AND Name IN (
    'sp_usuario_obtener_por_rol',
    'sp_usuario_buscar',
    'sp_usuario_verificar_existe',
    'sp_usuario_verificar_email',
    'sp_usuario_obtener_por_username',
    'sp_usuario_actualizar_ultimo_acceso',
    'sp_libro_buscar_por_titulo_autor',
    'sp_libro_verificar_isbn_existe',
    'sp_libro_actualizar_pdf',
    'sp_prestamo_obtener_usuario_libro',
    'sp_prestamo_validar_disponibilidad',
    'sp_prestamo_actualizar_observaciones',
    'sp_prestamo_eliminar',
    'sp_solicitudes_estadisticas_usuario',
    'sp_solicitud_cancelar'
);
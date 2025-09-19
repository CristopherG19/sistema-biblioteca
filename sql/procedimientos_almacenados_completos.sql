-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS COMPLETOS PARA SISTEMA BIBLIOTECA
-- Archivo: procedimientos_almacenados_completos.sql
-- Fecha: <?php echo date('Y-m-d H:i:s'); ?>

-- Migración completa de todas las sentencias SQL a procedimientos almacenados
-- =====================================================

USE biblioteca_db;

-- Eliminar procedimientos existentes si existen
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_id;
DROP PROCEDURE IF EXISTS sp_usuario_buscar;
DROP PROCEDURE IF EXISTS sp_usuario_verificar_existe;
DROP PROCEDURE IF EXISTS sp_usuario_verificar_email;
DROP PROCEDURE IF EXISTS sp_usuario_estadisticas;
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_username;
DROP PROCEDURE IF EXISTS sp_usuario_actualizar_ultimo_acceso;
DROP PROCEDURE IF EXISTS sp_usuario_obtener_por_rol;

DROP PROCEDURE IF EXISTS sp_libro_obtener_por_id;
DROP PROCEDURE IF EXISTS sp_libro_actualizar_pdf;
DROP PROCEDURE IF EXISTS sp_libro_obtener_disponibles;
DROP PROCEDURE IF EXISTS sp_libro_buscar_por_titulo_autor;
DROP PROCEDURE IF EXISTS sp_libro_verificar_isbn_existe;
DROP PROCEDURE IF EXISTS sp_libro_obtener_con_prestamos;
DROP PROCEDURE IF EXISTS sp_libro_registrar_lectura;

DROP PROCEDURE IF EXISTS sp_prestamo_obtener_todos;
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_por_usuario;
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_activos;
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_vencidos;
DROP PROCEDURE IF EXISTS sp_prestamo_insertar_completo;
DROP PROCEDURE IF EXISTS sp_prestamo_devolver_completo;
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_estadisticas;
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_por_libro;
DROP PROCEDURE IF EXISTS sp_prestamo_obtener_usuario_libro;
DROP PROCEDURE IF EXISTS sp_prestamo_validar_disponibilidad;
DROP PROCEDURE IF EXISTS sp_prestamo_actualizar_observaciones;
DROP PROCEDURE IF EXISTS sp_prestamo_eliminar;

DROP PROCEDURE IF EXISTS sp_ampliacion_solicitar;
DROP PROCEDURE IF EXISTS sp_ampliacion_obtener_solicitudes;
DROP PROCEDURE IF EXISTS sp_ampliacion_aprobar;
DROP PROCEDURE IF EXISTS sp_ampliacion_rechazar;

-- =====================================================
-- PROCEDIMIENTOS PARA USUARIOS
-- =====================================================

-- Obtener usuario por ID con información del rol
DELIMITER //
CREATE PROCEDURE sp_usuario_obtener_por_id(IN p_id INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre 
    FROM Usuarios u 
    INNER JOIN Roles r ON u.rol = r.idRol 
    WHERE u.idUsuario = p_id;
END //

-- Buscar usuarios por término
DELIMITER //
CREATE PROCEDURE sp_usuario_buscar(IN p_termino VARCHAR(255))
BEGIN
    SET p_termino = CONCAT('%', p_termino, '%');
    SELECT u.*, r.nombre as rol_nombre 
    FROM Usuarios u 
    INNER JOIN Roles r ON u.rol = r.idRol 
    WHERE (u.nombre LIKE p_termino OR u.usuario LIKE p_termino OR u.email LIKE p_termino)
    ORDER BY u.nombre;
END //

-- Verificar si usuario existe
DELIMITER //
CREATE PROCEDURE sp_usuario_verificar_existe(
    IN p_usuario VARCHAR(100), 
    IN p_excluir_id INT
)
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios 
    WHERE usuario = p_usuario
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id);
END //

-- Verificar si email existe
DELIMITER //
CREATE PROCEDURE sp_usuario_verificar_email(
    IN p_email VARCHAR(100), 
    IN p_excluir_id INT
)
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios 
    WHERE email = p_email
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id);
END //

-- Obtener estadísticas de usuarios
DELIMITER //
CREATE PROCEDURE sp_usuario_estadisticas()
BEGIN
    SELECT 
        COUNT(*) as total_usuarios,
        SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
        SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
        SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as nuevos_hoy
    FROM Usuarios;
END //

-- Obtener usuario por nombre de usuario (login)
DELIMITER //
CREATE PROCEDURE sp_usuario_obtener_por_username(IN p_username VARCHAR(100))
BEGIN
    SELECT u.*, r.nombre as rol_nombre 
    FROM Usuarios u 
    INNER JOIN Roles r ON u.rol = r.idRol 
    WHERE u.usuario = p_username;
END //

-- Actualizar último acceso
DELIMITER //
CREATE PROCEDURE sp_usuario_actualizar_ultimo_acceso(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET ultimo_acceso = NOW() 
    WHERE idUsuario = p_id;
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- Obtener usuarios por rol
DELIMITER //
CREATE PROCEDURE sp_usuario_obtener_por_rol(IN p_rol_id INT)
BEGIN
    SELECT u.*, 
           r.nombre as rol_nombre,
           u.nombre as nombreCompleto
    FROM Usuarios u 
    INNER JOIN Roles r ON u.rol = r.idRol 
    WHERE u.rol = p_rol_id
    ORDER BY u.nombre;
END //

-- =====================================================
-- PROCEDIMIENTOS PARA LIBROS
-- =====================================================

-- Obtener libro por ID con categoría
DELIMITER //
CREATE PROCEDURE sp_libro_obtener_por_id(IN p_id INT)
BEGIN
    SELECT l.*, c.nombre as categoria 
    FROM Libros l 
    JOIN Categorias c ON l.idCategoria = c.idCategoria 
    WHERE l.idLibro = p_id;
END //

-- Actualizar información de PDF de libro
DELIMITER //
CREATE PROCEDURE sp_libro_actualizar_pdf(
    IN p_id INT,
    IN p_archivo_pdf VARCHAR(255),
    IN p_numero_paginas INT,
    IN p_tamano_archivo DECIMAL(10,2)
)
BEGIN
    UPDATE Libros 
    SET archivo_pdf = p_archivo_pdf,
        numero_paginas = p_numero_paginas,
        tamano_archivo = p_tamano_archivo,
        fecha_subida = NOW()
    WHERE idLibro = p_id;
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- Obtener libros disponibles para préstamo
DELIMITER //
CREATE PROCEDURE sp_libro_obtener_disponibles()
BEGIN
    SELECT l.*, c.nombre as categoria 
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    WHERE l.disponible > 0
    ORDER BY l.titulo;
END //

-- Buscar libros por título o autor
DELIMITER //
CREATE PROCEDURE sp_libro_buscar_por_titulo_autor(IN p_termino VARCHAR(255))
BEGIN
    SET p_termino = CONCAT('%', p_termino, '%');
    SELECT l.*, c.nombre as categoria 
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    WHERE (l.titulo LIKE p_termino OR l.autor LIKE p_termino)
    ORDER BY l.titulo;
END //

-- Verificar si ISBN existe
DELIMITER //
CREATE PROCEDURE sp_libro_verificar_isbn_existe(
    IN p_isbn VARCHAR(20), 
    IN p_excluir_id INT
)
BEGIN
    SELECT COUNT(*) as existe
    FROM Libros 
    WHERE isbn = p_isbn
    AND (p_excluir_id IS NULL OR idLibro != p_excluir_id);
END //

-- Obtener libros con información de préstamos
DELIMITER //
CREATE PROCEDURE sp_libro_obtener_con_prestamos()
BEGIN
    SELECT l.*, c.nombre as categoria,
           COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
           COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
    GROUP BY l.idLibro, l.titulo, l.autor, l.editorial, l.anio, l.isbn, l.stock, l.disponible, l.descripcion, c.nombre
    ORDER BY l.titulo;
END //

-- Registrar lectura de libro
DELIMITER //
CREATE PROCEDURE sp_libro_registrar_lectura(
    IN p_libro_id INT,
    IN p_usuario_id INT
)
BEGIN
    INSERT INTO LibrosLecturas (idLibro, idUsuario, fecha_inicio) 
    VALUES (p_libro_id, p_usuario_id, NOW())
    ON DUPLICATE KEY UPDATE 
        fecha_inicio = NOW(),
        fecha_fin = NULL;
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- =====================================================
-- PROCEDIMIENTOS PARA PRÉSTAMOS
-- =====================================================

-- Obtener todos los préstamos con información completa
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_todos()
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre,
           u.email as usuario_email,
           l.titulo as libro_titulo, 
           l.autor as libro_autor,
           l.isbn as libro_isbn
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    ORDER BY p.fechaPrestamo DESC;
END //

-- Obtener préstamos por usuario
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_por_usuario(IN p_usuario_id INT)
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre,
           u.email as usuario_email,
           l.titulo as libro_titulo, 
           l.autor as libro_autor,
           l.isbn as libro_isbn
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    WHERE p.idUsuario = p_usuario_id
    ORDER BY p.fechaPrestamo DESC;
END //

-- Obtener préstamos activos (no devueltos)
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_activos()
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre,
           l.titulo as libro_titulo, 
           l.autor as libro_autor
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    WHERE p.estado = 'prestado' AND p.fechaDevolucionReal IS NULL
    ORDER BY p.fechaPrestamo DESC;
END //

-- Obtener préstamos vencidos
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_vencidos()
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre,
           l.titulo as libro_titulo, 
           l.autor as libro_autor,
           DATEDIFF(CURDATE(), p.fechaDevolucionEsperada) as dias_vencidos
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    WHERE p.estado = 'prestado' 
    AND p.fechaDevolucionReal IS NULL 
    AND p.fechaDevolucionEsperada < CURDATE()
    ORDER BY dias_vencidos DESC;
END //

-- Insertar nuevo préstamo completo
DELIMITER //
CREATE PROCEDURE sp_prestamo_insertar_completo(
    IN p_libro_id INT,
    IN p_usuario_id INT,
    IN p_fecha_prestamo DATE,
    IN p_fecha_devolucion_esperada DATE,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_disponible INT DEFAULT 0;
    DECLARE v_prestamo_id INT;
    DECLARE v_ya_prestado INT DEFAULT 0;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 as idPrestamo, 'error' as status, 'Error al insertar préstamo' as message;
    END;

    START TRANSACTION;

    -- Verificar disponibilidad
    SELECT disponible INTO v_disponible
    FROM Libros
    WHERE idLibro = p_libro_id;

    -- Verificar si el usuario ya tiene un préstamo activo de ese libro
    SELECT COUNT(*) INTO v_ya_prestado
    FROM Prestamos
    WHERE idLibro = p_libro_id
      AND idUsuario = p_usuario_id
      AND estado = 'prestado'
      AND fechaDevolucionReal IS NULL;

    IF v_disponible <= 0 THEN
        ROLLBACK;
        SELECT 0 as idPrestamo, 'no_disponible' as status, 'Libro no disponible' as message;
    ELSEIF v_ya_prestado > 0 THEN
        ROLLBACK;
        SELECT 0 as idPrestamo, 'ya_prestado' as status, 'El usuario ya tiene este libro prestado' as message;
    ELSE
        -- Insertar préstamo CON observaciones
        INSERT INTO Prestamos (idLibro, idUsuario, fechaPrestamo, fechaDevolucionEsperada, estado, observaciones)
        VALUES (p_libro_id, p_usuario_id, p_fecha_prestamo, p_fecha_devolucion_esperada, 'prestado', p_observaciones);

        SET v_prestamo_id = LAST_INSERT_ID();

        -- Actualizar stock del libro
        UPDATE Libros SET disponible = disponible - 1 WHERE idLibro = p_libro_id;

        COMMIT;
        SELECT v_prestamo_id as idPrestamo, 'success' as status, 'Préstamo creado exitosamente' as message;
    END IF;
END //

-- Devolver préstamo completo
DELIMITER //
CREATE PROCEDURE sp_prestamo_devolver_completo(
    IN p_prestamo_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_libro_id INT;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' as status, 'Error al procesar devolución' as message;
    END;

    START TRANSACTION;

    -- Obtener libro del préstamo
    SELECT idLibro INTO v_libro_id
    FROM Prestamos
    WHERE idPrestamo = p_prestamo_id
    AND fechaDevolucionReal IS NULL;

    IF v_libro_id IS NULL THEN
        ROLLBACK;
        SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    ELSE
        -- Actualizar préstamo CON observaciones
        UPDATE Prestamos 
        SET fechaDevolucionReal = NOW(),
            estado = 'devuelto',
            observaciones = CASE 
                WHEN observaciones IS NOT NULL AND p_observaciones IS NOT NULL 
                THEN CONCAT(observaciones, ' | Devolución: ', p_observaciones)
                WHEN observaciones IS NOT NULL 
                THEN observaciones
                WHEN p_observaciones IS NOT NULL 
                THEN CONCAT('Devolución: ', p_observaciones)
                ELSE NULL
            END
        WHERE idPrestamo = p_prestamo_id;
        
        -- Incrementar stock
        UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = v_libro_id;
        
        COMMIT;
        SELECT 'success' as status, 'Libro devuelto exitosamente' as message;
    END IF;
END //

-- Obtener estadísticas de préstamos
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_estadisticas()
BEGIN
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN fechaDevolucionReal IS NULL THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 ELSE 0 END) as devueltos,
        SUM(CASE WHEN fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < CURDATE() THEN 1 ELSE 0 END) as vencidos
    FROM Prestamos;
END //

-- Obtener préstamos por libro
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_por_libro(IN p_libro_id INT)
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre,
           l.titulo as libro_titulo
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    WHERE p.idLibro = p_libro_id
    ORDER BY p.fechaPrestamo DESC;
END //

-- Obtener préstamo específico usuario-libro
DELIMITER //
CREATE PROCEDURE sp_prestamo_obtener_usuario_libro(
    IN p_usuario_id INT,
    IN p_libro_id INT
)
BEGIN
    SELECT p.*, 
           u.nombre as usuario_nombre,
           l.titulo as libro_titulo
    FROM Prestamos p
    INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    WHERE p.idUsuario = p_usuario_id AND p.idLibro = p_libro_id
    AND p.fechaDevolucionReal IS NULL
    ORDER BY p.fechaPrestamo DESC
    LIMIT 1;
END //

-- Validar disponibilidad para préstamo
DELIMITER //
CREATE PROCEDURE sp_prestamo_validar_disponibilidad(IN p_libro_id INT)
BEGIN
    SELECT disponible > 0 as disponible, disponible as stock
    FROM Libros 
    WHERE idLibro = p_libro_id;
END //

-- Actualizar observaciones de préstamo
DELIMITER //
CREATE PROCEDURE sp_prestamo_actualizar_observaciones(
    IN p_prestamo_id INT,
    IN p_observaciones TEXT
)
BEGIN
    UPDATE Prestamos 
    SET observaciones = p_observaciones
    WHERE idPrestamo = p_prestamo_id;
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- Eliminar préstamo (solo si no está activo)
DELIMITER //
CREATE PROCEDURE sp_prestamo_eliminar(IN p_prestamo_id INT)
BEGIN
    DECLARE v_libro_id INT;
    DECLARE v_devuelto BOOLEAN DEFAULT FALSE;
    
    -- Verificar si el préstamo ya fue devuelto
    SELECT idLibro, (fechaDevolucionReal IS NOT NULL) INTO v_libro_id, v_devuelto
    FROM Prestamos
    WHERE idPrestamo = p_prestamo_id;
    
    IF v_devuelto THEN
        DELETE FROM Prestamos WHERE idPrestamo = p_prestamo_id;
        SELECT 'success' as status, 'Préstamo eliminado' as message;
    ELSE
        SELECT 'error' as status, 'No se puede eliminar un préstamo activo' as message;
    END IF;
END //

-- =====================================================
-- PROCEDIMIENTOS PARA AMPLIACIONES
-- =====================================================

-- Solicitar ampliación de préstamo
DELIMITER //
CREATE PROCEDURE sp_ampliacion_solicitar(
    IN p_prestamo_id INT,
    IN p_dias_adicionales INT,
    IN p_motivo TEXT
)
BEGIN
    DECLARE v_usuario_id INT;
    DECLARE v_solicitud_existente INT DEFAULT 0;
    
    -- Obtener usuario del préstamo
    SELECT idUsuario INTO v_usuario_id
    FROM Prestamos
    WHERE idPrestamo = p_prestamo_id
    AND fechaDevolucionReal IS NULL;
    
    -- Verificar si ya existe una solicitud pendiente
    SELECT COUNT(*) INTO v_solicitud_existente
    FROM SolicitudesAmpliacion
    WHERE idPrestamo = p_prestamo_id
    AND estado = 'Pendiente';
    
    IF v_usuario_id IS NULL THEN
        SELECT 0 as idSolicitud, 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
    ELSEIF v_solicitud_existente > 0 THEN
        SELECT 0 as idSolicitud, 'duplicada' as status, 'Ya existe una solicitud pendiente para este préstamo' as message;
    ELSE
        INSERT INTO SolicitudesAmpliacion (idPrestamo, idUsuario, diasAdicionales, motivo, estado, fechaSolicitud)
        VALUES (p_prestamo_id, v_usuario_id, p_dias_adicionales, p_motivo, 'Pendiente', NOW());
        
        SELECT LAST_INSERT_ID() as idSolicitud, 'success' as status, 'Solicitud de ampliación creada exitosamente' as message;
    END IF;
END //

-- Obtener solicitudes de ampliación
DELIMITER //
CREATE PROCEDURE sp_ampliacion_obtener_solicitudes(IN p_estado VARCHAR(20))
BEGIN
    SELECT sa.*,
           u.nombre as usuario_nombre,
           u.email as usuario_email,
           l.titulo as libro_titulo,
           l.autor as libro_autor,
           p.fechaDevolucionEsperada,
           b.nombre as bibliotecario_nombre
    FROM SolicitudesAmpliacion sa
    INNER JOIN Prestamos p ON sa.idPrestamo = p.idPrestamo
    INNER JOIN Usuarios u ON sa.idUsuario = u.idUsuario
    INNER JOIN Libros l ON p.idLibro = l.idLibro
    LEFT JOIN Usuarios b ON sa.idBibliotecario = b.idUsuario
    WHERE (p_estado IS NULL OR sa.estado = p_estado)
    ORDER BY sa.fechaSolicitud DESC;
END //

-- Aprobar solicitud de ampliación
DELIMITER //
CREATE PROCEDURE sp_ampliacion_aprobar(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    DECLARE v_prestamo_id INT;
    DECLARE v_dias_adicionales INT;
    DECLARE v_fecha_actual DATE;
    DECLARE v_nueva_fecha DATE;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' as status, 'Error al aprobar solicitud' as message;
    END;

    START TRANSACTION;

    -- Obtener datos de la solicitud
    SELECT sa.idPrestamo, sa.diasAdicionales, p.fechaDevolucionEsperada
    INTO v_prestamo_id, v_dias_adicionales, v_fecha_actual
    FROM SolicitudesAmpliacion sa
    INNER JOIN Prestamos p ON sa.idPrestamo = p.idPrestamo
    WHERE sa.idSolicitud = p_solicitud_id AND sa.estado = 'Pendiente';

    IF v_prestamo_id IS NULL THEN
        ROLLBACK;
        SELECT 'error' as status, 'Solicitud no encontrada o ya procesada' as message;
    ELSE
        -- Calcular nueva fecha
        SET v_nueva_fecha = DATE_ADD(v_fecha_actual, INTERVAL v_dias_adicionales DAY);
        
        -- Actualizar fecha de devolución del préstamo
        UPDATE Prestamos 
        SET fechaDevolucionEsperada = v_nueva_fecha
        WHERE idPrestamo = v_prestamo_id;
        
        -- Actualizar solicitud
        UPDATE SolicitudesAmpliacion 
        SET estado = 'Aprobada',
            fechaRespuesta = NOW(),
            respuestaBibliotecario = p_respuesta,
            idBibliotecario = p_bibliotecario_id
        WHERE idSolicitud = p_solicitud_id;
        
        COMMIT;
        SELECT 'success' as status, 'Ampliación aprobada exitosamente' as message;
    END IF;
END //

-- Rechazar solicitud de ampliación
DELIMITER //
CREATE PROCEDURE sp_ampliacion_rechazar(
    IN p_solicitud_id INT,
    IN p_bibliotecario_id INT,
    IN p_respuesta TEXT
)
BEGIN
    UPDATE SolicitudesAmpliacion 
    SET estado = 'Rechazada',
        fechaRespuesta = NOW(),
        respuestaBibliotecario = p_respuesta,
        idBibliotecario = p_bibliotecario_id
    WHERE idSolicitud = p_solicitud_id 
    AND estado = 'Pendiente';
    
    IF ROW_COUNT() > 0 THEN
        SELECT 'success' as status, 'Solicitud rechazada correctamente' as message;
    ELSE
        SELECT 'error' as status, 'No se pudo rechazar la solicitud' as message;
    END IF;
END //

DELIMITER ;

-- Mensaje de confirmación
SELECT 'Todos los procedimientos almacenados han sido creados exitosamente' as mensaje;

COMMIT;
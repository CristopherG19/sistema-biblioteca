-- =====================================================
-- SISTEMA DE BIBLIOTECA COMPLETO - SCRIPT UNIFICADO
-- Generado automáticamente: 2025-09-21 21:27:21
-- Total de procedimientos: 66
-- =====================================================

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. CREACIÓN DE BASE DE DATOS
-- =====================================================

CREATE DATABASE IF NOT EXISTS `biblioteca_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `biblioteca_db`;

-- =====================================================
-- 2. CREACIÓN DE TABLAS
-- =====================================================

-- Tabla Roles
CREATE TABLE IF NOT EXISTS `Roles` (
  `idRol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Usuarios
CREATE TABLE IF NOT EXISTS `Usuarios` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `rol` int(11) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `telefono` varchar(20),
  `activo` boolean DEFAULT TRUE,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` timestamp NULL,
  PRIMARY KEY (`idUsuario`),
  FOREIGN KEY (`rol`) REFERENCES `Roles`(`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Categorias
CREATE TABLE IF NOT EXISTS `Categorias` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Libros
CREATE TABLE IF NOT EXISTS `Libros` (
  `idLibro` int(11) NOT NULL AUTO_INCREMENT,
  `idCategoria` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `editorial` varchar(100),
  `anio` int(4),
  `isbn` varchar(20) UNIQUE,
  `stock` int(11) DEFAULT 0,
  `disponible` int(11) DEFAULT 0,
  `descripcion` text,
  `portada` varchar(255),
  `archivo_pdf` varchar(255),
  `numero_paginas` int(11),
  `tamano_archivo` bigint(20),
  `fecha_subida` timestamp NULL,
  `fecha_registro` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLibro`),
  FOREIGN KEY (`idCategoria`) REFERENCES `Categorias`(`idCategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Prestamos
CREATE TABLE IF NOT EXISTS `Prestamos` (
  `idPrestamo` int(11) NOT NULL AUTO_INCREMENT,
  `idLibro` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fechaPrestamo` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fechaDevolucionEsperada` date NOT NULL,
  `fechaDevolucionReal` timestamp NULL,
  `estado` enum('Activo','Devuelto','Vencido') DEFAULT 'Activo',
  `observaciones` text,
  PRIMARY KEY (`idPrestamo`),
  FOREIGN KEY (`idLibro`) REFERENCES `Libros`(`idLibro`),
  FOREIGN KEY (`idUsuario`) REFERENCES `Usuarios`(`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Solicitudes de Préstamo
CREATE TABLE IF NOT EXISTS `solicitudes_prestamo` (
  `idSolicitud` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fecha_solicitud` timestamp DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','Aprobada','Rechazada','Convertida') DEFAULT 'Pendiente',
  `observaciones_usuario` text,
  `bibliotecario_id` int(11),
  `fecha_respuesta` timestamp NULL,
  `observaciones_bibliotecario` text,
  PRIMARY KEY (`idSolicitud`),
  FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios`(`idUsuario`),
  FOREIGN KEY (`libro_id`) REFERENCES `Libros`(`idLibro`),
  FOREIGN KEY (`bibliotecario_id`) REFERENCES `Usuarios`(`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 3. DATOS INICIALES
-- =====================================================

-- Insertar roles
INSERT INTO `Roles` (`idRol`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema'),
(2, 'Bibliotecario', 'Gestión de libros y préstamos'),
(3, 'Lector', 'Solo consulta y solicitudes');

-- Insertar usuario administrador
INSERT INTO `Usuarios` (`nombre`, `apellido`, `usuario`, `password`, `rol`, `email`, `telefono`) VALUES
('Administrador', 'Sistema', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin@biblioteca.com', '123456789');

-- Insertar categorías
INSERT INTO `Categorias` (`nombre`, `descripcion`) VALUES
('Ficción', 'Novelas y literatura de ficción'),
('Ciencia', 'Libros de ciencias naturales'),
('Historia', 'Libros de historia y biografías'),
('Tecnología', 'Libros de tecnología e informática'),
('Filosofía', 'Libros de filosofía y pensamiento'),
('Matemáticas', 'Libros de matemáticas y estadística'),
('Literatura', 'Literatura clásica y contemporánea'),
('Deportes', 'Libros de deportes y actividad física');

-- =====================================================
-- 4. PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento: sp_actualizar_categoria
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_categoria`(
                IN p_id INT,
                IN p_nombre VARCHAR(100),
                IN p_descripcion TEXT
            )
BEGIN
                UPDATE Categorias 
                SET nombre = p_nombre,
                    descripcion = p_descripcion,
                    fecha_actualizacion = NOW()
                WHERE idCategoria = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_actualizar_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_libro`(
                IN p_id INT,
                IN p_idCategoria INT,
                IN p_titulo VARCHAR(200),
                IN p_autor VARCHAR(100),
                IN p_editorial VARCHAR(100),
                IN p_anio INT,
                IN p_isbn VARCHAR(20),
                IN p_stock INT,
                IN p_disponible INT,
                IN p_descripcion TEXT
            )
BEGIN
                UPDATE Libros 
                SET idCategoria = p_idCategoria,
                    titulo = p_titulo,
                    autor = p_autor,
                    editorial = p_editorial,
                    anio = p_anio,
                    isbn = p_isbn,
                    stock = p_stock,
                    disponible = p_disponible,
                    descripcion = p_descripcion,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_actualizar_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_usuario`(
                IN p_id INT,
                IN p_nombre VARCHAR(100),
                IN p_usuario VARCHAR(50),
                IN p_password VARCHAR(255),
                IN p_rol INT,
                IN p_email VARCHAR(100),
                IN p_telefono VARCHAR(20)
            )
BEGIN
                UPDATE Usuarios 
                SET nombre = p_nombre,
                    usuario = p_usuario,
                    password = p_password,
                    rol = p_rol,
                    email = p_email,
                    telefono = p_telefono,
                    fecha_actualizacion = NOW()
                WHERE idUsuario = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_ampliacion_aprobar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_aprobar`(
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
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | Ampliación: Ampliado por ', v_dias_adicionales, ' días. Motivo: ', p_respuesta)
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
END

-- Procedimiento: sp_ampliacion_obtener_solicitudes
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_obtener_solicitudes`(IN p_estado VARCHAR(20))
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
END

-- Procedimiento: sp_ampliacion_rechazar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_rechazar`(
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
END

-- Procedimiento: sp_ampliacion_solicitar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ampliacion_solicitar`(
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
END

-- Procedimiento: sp_categoria_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_categoria_obtener_por_id`(IN p_id INT)
BEGIN
                SELECT idCategoria, nombre, descripcion, fecha_creacion, activa
                FROM Categorias
                WHERE idCategoria = p_id AND activa = TRUE;
            END

-- Procedimiento: sp_eliminar_categoria
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_categoria`(IN p_id INT)
BEGIN
                UPDATE Categorias 
                SET activa = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idCategoria = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_eliminar_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_libro`(IN p_id INT)
BEGIN
                UPDATE Libros 
                SET activo = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_eliminar_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_usuario`(IN p_id INT)
BEGIN
                UPDATE Usuarios 
                SET activo = FALSE,
                    fecha_actualizacion = NOW()
                WHERE idUsuario = p_id;
                SELECT ROW_COUNT() as affected_rows;
            END

-- Procedimiento: sp_insertar_categoria
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_categoria`(
                IN p_nombre VARCHAR(100),
                IN p_descripcion TEXT
            )
BEGIN
                INSERT INTO Categorias (nombre, descripcion)
                VALUES (p_nombre, p_descripcion);
                SELECT LAST_INSERT_ID() as idCategoria;
            END

-- Procedimiento: sp_insertar_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_libro`(
                IN p_idCategoria INT,
                IN p_titulo VARCHAR(200),
                IN p_autor VARCHAR(100),
                IN p_editorial VARCHAR(100),
                IN p_anio INT,
                IN p_isbn VARCHAR(20),
                IN p_stock INT,
                IN p_disponible INT,
                IN p_descripcion TEXT
            )
BEGIN
                INSERT INTO Libros (idCategoria, titulo, autor, editorial, anio, isbn, stock, disponible, descripcion)
                VALUES (p_idCategoria, p_titulo, p_autor, p_editorial, p_anio, p_isbn, p_stock, p_disponible, p_descripcion);
                SELECT LAST_INSERT_ID() as idLibro;
            END

-- Procedimiento: sp_insertar_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_usuario`(
                IN p_nombre VARCHAR(100),
                IN p_usuario VARCHAR(50),
                IN p_password VARCHAR(255),
                IN p_rol INT,
                IN p_email VARCHAR(100),
                IN p_telefono VARCHAR(20)
            )
BEGIN
                INSERT INTO Usuarios (nombre, usuario, password, rol, email, telefono)
                VALUES (p_nombre, p_usuario, p_password, p_rol, p_email, p_telefono);
                SELECT LAST_INSERT_ID() as idUsuario;
            END

-- Procedimiento: sp_libros_disponibles_solicitud
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libros_disponibles_solicitud`()
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
            c.nombre as categoria_nombre
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.disponible > 0
        ORDER BY l.titulo;
    END

-- Procedimiento: sp_libro_actualizar_pdf
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_pdf`(
        IN p_id INT,
        IN p_archivo_pdf VARCHAR(255),
        IN p_numero_paginas INT,
        IN p_tamano_archivo VARCHAR(50)
    )
BEGIN
        UPDATE Libros 
        SET archivo_pdf = p_archivo_pdf,
            numero_paginas = p_numero_paginas,
            tamano_archivo = p_tamano_archivo,
            fecha_subida = NOW(),
            fecha_actualizacion = NOW()
        WHERE idLibro = p_id;
        
        SELECT ROW_COUNT() as affected_rows;
    END

-- Procedimiento: sp_libro_actualizar_stock_devolucion
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_stock_devolucion`(IN p_id INT)
BEGIN
                UPDATE Libros 
                SET disponible = disponible + 1,
                    fecha_actualizacion = NOW()
                WHERE idLibro = p_id;
                
                SELECT 'success' as status, 'Stock actualizado' as message;
            END

-- Procedimiento: sp_libro_actualizar_stock_prestamo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_actualizar_stock_prestamo`(IN p_id INT)
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                
                SELECT disponible INTO v_disponible
                FROM Libros
                WHERE idLibro = p_id;
                
                IF v_disponible > 0 THEN
                    UPDATE Libros 
                    SET disponible = disponible - 1,
                        fecha_actualizacion = NOW()
                    WHERE idLibro = p_id;
                    
                    SELECT 'success' as status, 'Stock actualizado' as message;
                ELSE
                    SELECT 'error' as status, 'No hay ejemplares disponibles' as message;
                END IF;
            END

-- Procedimiento: sp_libro_buscar_por_titulo_autor
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_buscar_por_titulo_autor`(IN p_termino VARCHAR(100))
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.activo = TRUE
    AND (l.titulo LIKE CONCAT('%', p_termino, '%')
         OR l.autor LIKE CONCAT('%', p_termino, '%')
         OR l.isbn LIKE CONCAT('%', p_termino, '%'))
    ORDER BY l.titulo;
END

-- Procedimiento: sp_libro_obtener_con_detalle_pdf
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_con_detalle_pdf`(IN p_id INT)
BEGIN
        SELECT l.*, c.nombre as categoria_nombre,
               CASE WHEN l.archivo_pdf IS NOT NULL AND l.archivo_pdf <> '' THEN 1 ELSE 0 END as tiene_pdf,
               l.archivo_pdf, 
               l.numero_paginas, 
               l.tamano_archivo, 
               l.fecha_subida
        FROM Libros l
        INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
        WHERE l.idLibro = p_id AND l.activo = TRUE;
    END

-- Procedimiento: sp_libro_obtener_con_prestamos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_con_prestamos`()
BEGIN
    SELECT l.*, c.nombre as categoria,
           COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
           COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
    FROM Libros l 
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
    LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
    WHERE l.activo = TRUE
    GROUP BY l.idLibro
    ORDER BY l.titulo;
END

-- Procedimiento: sp_libro_obtener_disponibles
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_disponibles`(IN p_limite INT)
BEGIN
    DECLARE v_limite INT DEFAULT 1000;
    
    -- Asignar el valor de p_limite a v_limite, usando 1000 si p_limite es NULL
    SET v_limite = COALESCE(p_limite, 1000);
    
    SELECT l.*, c.nombre AS categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.disponible > 0 AND l.activo = TRUE
    ORDER BY l.titulo
    LIMIT v_limite;
END

-- Procedimiento: sp_libro_obtener_estadisticas_pdf
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_estadisticas_pdf`()
BEGIN
        SELECT 
            COUNT(*) as total_libros,
            COUNT(CASE WHEN archivo_pdf IS NOT NULL AND archivo_pdf <> '' THEN 1 END) as libros_con_pdf,
            COUNT(CASE WHEN archivo_pdf IS NULL OR archivo_pdf = '' THEN 1 END) as libros_sin_pdf,
            AVG(CASE WHEN numero_paginas IS NOT NULL THEN numero_paginas END) as promedio_paginas,
            SUM(CASE WHEN tamano_archivo IS NOT NULL THEN 
                CAST(REPLACE(REPLACE(tamano_archivo, 'MB', ''), 'KB', '') AS DECIMAL(10,2)) 
            END) as tamano_total_mb
        FROM Libros 
        WHERE activo = TRUE;
    END

-- Procedimiento: sp_libro_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT l.*, c.nombre as categoria_nombre
    FROM Libros l
    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
    WHERE l.idLibro = p_id AND l.activo = TRUE;
END

-- Procedimiento: sp_libro_obtener_por_isbn
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_por_isbn`(IN p_isbn VARCHAR(20))
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.isbn = p_isbn AND l.activo = TRUE;
            END

-- Procedimiento: sp_libro_obtener_recientes
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_obtener_recientes`(IN p_limite INT)
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.activo = TRUE
                ORDER BY l.idLibro DESC
                LIMIT p_limite;
            END

-- Procedimiento: sp_libro_registrar_lectura
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_registrar_lectura`(IN p_libro_id INT, IN p_usuario_id INT)
BEGIN
    INSERT INTO HistorialLectura (idUsuario, idLibro, tipo)
    VALUES (p_usuario_id, p_libro_id, 'Lectura');
    
    SELECT LAST_INSERT_ID() as id_historial;
END

-- Procedimiento: sp_libro_verificar_isbn_existe
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_libro_verificar_isbn_existe`(IN p_isbn VARCHAR(20), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Libros
    WHERE isbn = p_isbn 
    AND (p_excluir_id IS NULL OR idLibro != p_excluir_id)
    AND activo = TRUE;
END

-- Procedimiento: sp_listar_categorias
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_categorias`()
BEGIN
                SELECT idCategoria, nombre, descripcion, fecha_creacion, activa
                FROM Categorias
                WHERE activa = TRUE
                ORDER BY nombre;
            END

-- Procedimiento: sp_listar_libros
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_libros`()
BEGIN
                SELECT l.*, c.nombre as categoria_nombre
                FROM Libros l
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                WHERE l.activo = TRUE
                ORDER BY l.titulo;
            END

-- Procedimiento: sp_listar_roles
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_roles`()
BEGIN
                SELECT idRol, nombre, descripcion, fecha_creacion
                FROM Roles
                ORDER BY idRol;
            END

-- Procedimiento: sp_listar_usuarios
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_usuarios`()
BEGIN
                SELECT u.*, r.nombre as rol_nombre
                FROM Usuarios u
                INNER JOIN Roles r ON u.rol = r.idRol
                WHERE u.activo = TRUE
                ORDER BY u.nombre, u.apellido;
            END

-- Procedimiento: sp_prestamo_actualizar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_actualizar`(
            IN p_id INT,
            IN p_fecha_devolucion_esperada DATE,
            IN p_estado VARCHAR(20),
            IN p_observaciones TEXT,
            IN p_fecha_devolucion_real DATETIME
        )
BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            UPDATE Prestamos 
            SET fechaDevolucionEsperada = COALESCE(p_fecha_devolucion_esperada, fechaDevolucionEsperada),
                estado = COALESCE(p_estado, estado),
                observaciones = COALESCE(p_observaciones, observaciones),
                fechaDevolucionReal = COALESCE(p_fecha_devolucion_real, fechaDevolucionReal)
            WHERE idPrestamo = p_id;
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Préstamo actualizado exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo actualizar el préstamo' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_prestamo_actualizar_observaciones
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_actualizar_observaciones`(
        IN p_prestamo_id INT,
        IN p_observaciones TEXT
    )
BEGIN
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        UPDATE Prestamos 
        SET observaciones = p_observaciones
        WHERE idPrestamo = p_prestamo_id;
        
        IF ROW_COUNT() > 0 THEN
            SELECT 'success' as status, 'Observaciones actualizadas exitosamente' as message;
        ELSE
            SELECT 'error' as status, 'No se pudo actualizar las observaciones' as message;
        END IF;
        
        COMMIT;
    END

-- Procedimiento: sp_prestamo_auto_devolver
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_auto_devolver`(IN p_prestamo_id INT)
BEGIN
            DECLARE v_libro_id INT;
            DECLARE v_estado VARCHAR(20);
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            -- Verificar que el préstamo existe y está activo
            SELECT idLibro, estado INTO v_libro_id, v_estado 
            FROM Prestamos 
            WHERE idPrestamo = p_prestamo_id;
            
            IF v_libro_id IS NULL THEN
                SELECT 'error' as status, 'Préstamo no encontrado' as message;
            ELSEIF v_estado != 'Activo' THEN
                SELECT 'error' as status, 'El préstamo no está activo' as message;
            ELSE
                -- Marcar como devuelto automáticamente
                UPDATE Prestamos 
                SET estado = 'Devuelto',
                    fechaDevolucionReal = NOW(),
                    observaciones = CONCAT(COALESCE(observaciones, ''), ' [Auto-devolución]')
                WHERE idPrestamo = p_prestamo_id;
                
                -- Restaurar stock del libro
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
                
                SELECT 'success' as status, 'Préstamo devuelto automáticamente' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_prestamo_buscar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_buscar`(IN p_termino VARCHAR(255))
BEGIN
            SELECT 
                p.idPrestamo,
                p.idLibro,
                p.idUsuario,
                p.fechaPrestamo,
                p.fechaDevolucionEsperada,
                p.fechaDevolucionReal,
                p.estado,
                p.observaciones,
                u.nombre as usuario_nombre,
                u.apellido as usuario_apellido,
                l.titulo as libro_titulo,
                l.autor as libro_autor
            FROM Prestamos p
            INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
            INNER JOIN Libros l ON p.idLibro = l.idLibro
            WHERE p.idPrestamo LIKE CONCAT('%', p_termino, '%')
               OR u.nombre LIKE CONCAT('%', p_termino, '%')
               OR u.apellido LIKE CONCAT('%', p_termino, '%')
               OR l.titulo LIKE CONCAT('%', p_termino, '%')
               OR l.autor LIKE CONCAT('%', p_termino, '%')
               OR p.estado LIKE CONCAT('%', p_termino, '%')
            ORDER BY p.fechaPrestamo DESC;
        END

-- Procedimiento: sp_prestamo_devolver_completo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_devolver_completo`(IN p_id INT)
BEGIN
                DECLARE v_libro_id INT;
                DECLARE v_affected_rows INT DEFAULT 0;
                
                -- Obtener el ID del libro
                SELECT idLibro INTO v_libro_id
                FROM Prestamos
                WHERE idPrestamo = p_id AND fechaDevolucionReal IS NULL;
                
                IF v_libro_id IS NOT NULL THEN
                    -- Marcar préstamo como devuelto
                    UPDATE Prestamos 
                    SET fechaDevolucionReal = NOW(),
                        estado = 'Devuelto'
                    WHERE idPrestamo = p_id;
                    
                    SET v_affected_rows = ROW_COUNT();
                    
                    -- Incrementar stock disponible
                    UPDATE Libros 
                    SET disponible = disponible + 1
                    WHERE idLibro = v_libro_id;
                    
                    SELECT 'success' as status, 'Préstamo devuelto exitosamente' as message, v_affected_rows as affected_rows;
                ELSE
                    SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message, 0 as affected_rows;
                END IF;
            END

-- Procedimiento: sp_prestamo_eliminar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_eliminar`(
        IN p_prestamo_id INT
    )
BEGIN
        DECLARE v_libro_id INT;
        DECLARE v_estado VARCHAR(20);
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        -- Obtener información del préstamo
        SELECT idLibro, estado INTO v_libro_id, v_estado
        FROM Prestamos 
        WHERE idPrestamo = p_prestamo_id;
        
        IF v_libro_id IS NULL THEN
            SELECT 'error' as status, 'Préstamo no encontrado' as message;
        ELSE
            -- Si el préstamo está activo, restaurar stock del libro
            IF v_estado = 'Activo' THEN
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
            END IF;
            
            -- Eliminar el préstamo
            DELETE FROM Prestamos 
            WHERE idPrestamo = p_prestamo_id;
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Préstamo eliminado exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo eliminar el préstamo' as message;
            END IF;
        END IF;
        
        COMMIT;
    END

-- Procedimiento: sp_prestamo_insertar_completo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_insertar_completo`(
                IN p_libro_id INT,
                IN p_usuario_id INT,
                IN p_fecha_prestamo DATE,
                IN p_fecha_devolucion_esperada DATE,
                IN p_observaciones TEXT
            )
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                DECLARE v_affected_rows INT DEFAULT 0;
                DECLARE v_prestamo_id INT;
                
                -- Verificar disponibilidad
                SELECT disponible INTO v_disponible
                FROM Libros
                WHERE idLibro = p_libro_id AND activo = 1;
                
                IF v_disponible > 0 THEN
                    -- Insertar préstamo
                    INSERT INTO Prestamos (idLibro, idUsuario, fechaPrestamo, fechaDevolucionEsperada, observaciones, estado)
                    VALUES (p_libro_id, p_usuario_id, p_fecha_prestamo, p_fecha_devolucion_esperada, p_observaciones, 'Activo');
                    
                    SET v_prestamo_id = LAST_INSERT_ID();
                    SET v_affected_rows = ROW_COUNT();
                    
                    -- Decrementar stock disponible
                    UPDATE Libros 
                    SET disponible = disponible - 1
                    WHERE idLibro = p_libro_id;
                    
                    SELECT 'success' as status, 'Préstamo creado exitosamente' as message, v_prestamo_id as idPrestamo;
                ELSE
                    SELECT 'error' as status, 'El libro no está disponible para préstamo' as message, 0 as idPrestamo;
                END IF;
            END

-- Procedimiento: sp_prestamo_obtener_activos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_activos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_estadisticas
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_estadisticas`()
BEGIN
                SELECT 
                    COUNT(*) as total_prestamos,
                    COUNT(CASE WHEN fechaDevolucionReal IS NULL THEN 1 END) as prestamos_activos,
                    COUNT(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 END) as prestamos_devueltos,
                    COUNT(CASE WHEN fechaDevolucionReal IS NULL AND fechaVencimiento < NOW() THEN 1 END) as prestamos_vencidos
                FROM Prestamos;
            END

-- Procedimiento: sp_prestamo_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_id`(IN p_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idPrestamo = p_id;
            END

-- Procedimiento: sp_prestamo_obtener_por_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_libro`(IN p_libro_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                WHERE p.idLibro = p_libro_id
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_por_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_por_usuario`(IN p_usuario_id INT)
BEGIN
                SELECT p.*, l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idUsuario = p_usuario_id
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_todos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_todos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                ORDER BY p.fechaPrestamo DESC;
            END

-- Procedimiento: sp_prestamo_obtener_usuario_libro
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_usuario_libro`(IN p_usuario_id INT, IN p_libro_id INT)
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                WHERE p.idUsuario = p_usuario_id 
                AND p.idLibro = p_libro_id
                AND p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC
                LIMIT 1;
            END

-- Procedimiento: sp_prestamo_obtener_vencidos
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_obtener_vencidos`()
BEGIN
                SELECT p.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido,
                       l.titulo as libro_titulo, l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.fechaDevolucionReal IS NULL 
                AND p.fechaDevolucionEsperada < NOW()
                ORDER BY p.fechaDevolucionEsperada ASC;
            END

-- Procedimiento: sp_prestamo_registrar_devolucion
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_registrar_devolucion`(
            IN p_prestamo_id INT,
            IN p_observaciones TEXT
        )
BEGIN
            DECLARE v_libro_id INT;
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            -- Obtener el libro del préstamo
            SELECT idLibro INTO v_libro_id FROM Prestamos WHERE idPrestamo = p_prestamo_id AND estado = 'Activo';
            
            IF v_libro_id IS NULL THEN
                SELECT 'error' as status, 'Préstamo no encontrado o ya devuelto' as message;
            ELSE
                -- Actualizar el préstamo
                UPDATE Prestamos 
                SET estado = 'Devuelto',
                    fechaDevolucionReal = NOW(),
                    observaciones = COALESCE(p_observaciones, observaciones)
                WHERE idPrestamo = p_prestamo_id;
                
                -- Restaurar stock del libro
                UPDATE Libros 
                SET disponible = disponible + 1
                WHERE idLibro = v_libro_id;
                
                SELECT 'success' as status, 'Devolución registrada exitosamente' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_prestamo_validar_disponibilidad
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_prestamo_validar_disponibilidad`(IN p_libro_id INT)
BEGIN
                DECLARE v_disponible INT DEFAULT 0;
                DECLARE v_activo TINYINT DEFAULT 0;
                
                SELECT disponible, activo INTO v_disponible, v_activo
                FROM Libros
                WHERE idLibro = p_libro_id;
                
                IF v_disponible > 0 AND v_activo = 1 THEN
                    SELECT 1 as disponible;
                ELSE
                    SELECT 0 as disponible;
                END IF;
            END

-- Procedimiento: sp_solicitudes_estadisticas
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total_solicitudes,
        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
        SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas,
        SUM(CASE WHEN DATE(fecha_solicitud) = CURDATE() THEN 1 ELSE 0 END) as solicitudes_hoy
    FROM solicitudes_prestamo;
END

-- Procedimiento: sp_solicitudes_estadisticas_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_estadisticas_usuario`(IN p_usuario_id INT)
BEGIN
            SELECT 
                COUNT(*) as total_solicitudes,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
            FROM solicitudes_prestamo
            WHERE usuario_id = p_usuario_id;
        END

-- Procedimiento: sp_solicitudes_listar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_listar`(IN p_estado VARCHAR(20))
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
END

-- Procedimiento: sp_solicitudes_usuario
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitudes_usuario`(IN p_usuario_id INT)
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
END

-- Procedimiento: sp_solicitud_aprobar_y_crear_prestamo
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_aprobar_y_crear_prestamo`(
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
END

-- Procedimiento: sp_solicitud_cancelar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_cancelar`(
            IN p_solicitud_id INT,
            IN p_usuario_id INT
        )
BEGIN
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;
            
            START TRANSACTION;
            
            UPDATE solicitudes_prestamo 
            SET estado = 'Rechazada',
                observaciones_bibliotecario = 'Cancelada por el usuario',
                fecha_respuesta = NOW()
            WHERE idSolicitud = p_solicitud_id 
            AND usuario_id = p_usuario_id 
            AND estado = 'Pendiente';
            
            IF ROW_COUNT() > 0 THEN
                SELECT 'success' as status, 'Solicitud cancelada exitosamente' as message;
            ELSE
                SELECT 'error' as status, 'No se pudo cancelar la solicitud' as message;
            END IF;
            
            COMMIT;
        END

-- Procedimiento: sp_solicitud_insertar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_insertar`(
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
END

-- Procedimiento: sp_solicitud_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_obtener_por_id`(IN p_id INT)
BEGIN
            SELECT 
                s.idSolicitud,
                s.usuario_id,
                s.libro_id,
                s.fecha_solicitud,
                s.observaciones_usuario,
                s.estado,
                s.bibliotecario_id,
                s.fecha_respuesta,
                s.observaciones_bibliotecario,
                u.nombre as usuario_nombre,
                u.apellido as usuario_apellido,
                u.email as usuario_email,
                l.titulo as libro_titulo,
                l.autor as libro_autor,
                c.nombre as categoria_nombre
            FROM solicitudes_prestamo s
            INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
            INNER JOIN Libros l ON s.libro_id = l.idLibro
            INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
            WHERE s.idSolicitud = p_id;
        END

-- Procedimiento: sp_solicitud_responder
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_solicitud_responder`(
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
END

-- Procedimiento: sp_usuario_actualizar_ultimo_acceso
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_actualizar_ultimo_acceso`(IN p_id INT)
BEGIN
    UPDATE Usuarios 
    SET ultimo_acceso = NOW() 
    WHERE idUsuario = p_id;
END

-- Procedimiento: sp_usuario_buscar
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_buscar`(IN p_termino VARCHAR(100))
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
END

-- Procedimiento: sp_usuario_estadisticas
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_estadisticas`()
BEGIN
    SELECT 
        COUNT(*) as total_usuarios,
        SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
        SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
        SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as nuevos_hoy
    FROM Usuarios
    WHERE activo = TRUE;
END

-- Procedimiento: sp_usuario_obtener_por_id
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_id`(IN p_id INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.idUsuario = p_id AND u.activo = TRUE;
END

-- Procedimiento: sp_usuario_obtener_por_rol
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_rol`(IN p_rol INT)
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.rol = p_rol AND u.activo = TRUE
    ORDER BY u.nombre, u.apellido;
END

-- Procedimiento: sp_usuario_obtener_por_username
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_obtener_por_username`(IN p_usuario VARCHAR(50))
BEGIN
    SELECT u.*, r.nombre as rol_nombre
    FROM Usuarios u
    INNER JOIN Roles r ON u.rol = r.idRol
    WHERE u.usuario = p_usuario AND u.activo = TRUE;
END

-- Procedimiento: sp_usuario_verificar_email
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_verificar_email`(IN p_email VARCHAR(100), IN p_excluir_id INT)
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE email = p_email 
    AND (p_excluir_id IS NULL OR idUsuario != p_excluir_id)
    AND activo = TRUE;
END

-- Procedimiento: sp_usuario_verificar_existe
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_usuario_verificar_existe`(IN p_usuario VARCHAR(50))
BEGIN
    SELECT COUNT(*) as existe
    FROM Usuarios
    WHERE usuario = p_usuario AND activo = TRUE;
END

DELIMITER ;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
COMMIT;

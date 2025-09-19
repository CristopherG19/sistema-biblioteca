-- =====================================================
-- SCRIPT DE LIMPIEZA FINAL - BASE DE DATOS
-- Mantiene usuarios de login y resetea contadores ID
-- Fecha: 2025-09-18
-- =====================================================

USE biblioteca_db;

-- =====================================================
-- 1. MOSTRAR ESTADO ACTUAL ANTES DE LIMPIEZA
-- =====================================================
SELECT 'ESTADO ANTES DE LIMPIEZA:' AS info;
SELECT 'Usuarios:' AS tabla, COUNT(*) AS registros FROM usuarios
UNION ALL
SELECT 'Categorias:' AS tabla, COUNT(*) AS registros FROM categorias
UNION ALL
SELECT 'Libros:' AS tabla, COUNT(*) AS registros FROM libros
UNION ALL
SELECT 'Prestamos:' AS tabla, COUNT(*) AS registros FROM prestamos
UNION ALL
SELECT 'Solicitudes:' AS tabla, COUNT(*) AS registros FROM solicitudes_prestamo
UNION ALL
SELECT 'Historial:' AS tabla, COUNT(*) AS registros FROM historiallectura;

-- =====================================================
-- 2. DESHABILITAR VERIFICACIONES DE CLAVES FORÁNEAS
-- =====================================================
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 3. LIMPIAR DATOS ESPECÍFICOS
-- =====================================================

-- Limpiar historial de lectura
TRUNCATE TABLE historiallectura;

-- Limpiar intereses de usuario (mantener solo de usuarios 2 y 3)
DELETE FROM interesesusuario WHERE idUsuario NOT IN (2, 3);

-- Limpiar libros favoritos (mantener solo de usuarios 2 y 3)
DELETE FROM librosfavoritos WHERE idUsuario NOT IN (2, 3);

-- Limpiar lecturas de libros (mantener solo de usuarios 2 y 3)
DELETE FROM libroslecturas WHERE idUsuario NOT IN (2, 3);

-- Limpiar versiones de libros
TRUNCATE TABLE librosversiones;

-- Limpiar todas las multas (están relacionadas con préstamos)
TRUNCATE TABLE multas;

-- Limpiar solicitudes de ampliación
TRUNCATE TABLE solicitudesampliacion;

-- Limpiar préstamos (mantener solo de usuarios 2 y 3)
DELETE FROM prestamos WHERE idUsuario NOT IN (2, 3);

-- Limpiar solicitudes de préstamo (mantener solo de usuarios 2 y 3)
DELETE FROM solicitudes_prestamo WHERE idUsuario NOT IN (2, 3);

-- Limpiar todos los libros
TRUNCATE TABLE libros;

-- Limpiar todas las categorías
TRUNCATE TABLE categorias;

-- Limpiar usuarios (mantener solo admin=2 y cris1996=3)
DELETE FROM usuarios WHERE idUsuario NOT IN (2, 3);

-- =====================================================
-- 4. RESETEAR CONTADORES AUTO_INCREMENT
-- =====================================================

ALTER TABLE categorias AUTO_INCREMENT = 1;
ALTER TABLE libros AUTO_INCREMENT = 1;
ALTER TABLE prestamos AUTO_INCREMENT = 1;
ALTER TABLE solicitudes_prestamo AUTO_INCREMENT = 1;
ALTER TABLE solicitudesampliacion AUTO_INCREMENT = 1;
ALTER TABLE multas AUTO_INCREMENT = 1;
ALTER TABLE historiallectura AUTO_INCREMENT = 1;
ALTER TABLE librosversiones AUTO_INCREMENT = 1;
ALTER TABLE interesesusuario AUTO_INCREMENT = 1;
ALTER TABLE librosfavoritos AUTO_INCREMENT = 1;
ALTER TABLE libroslecturas AUTO_INCREMENT = 1;

-- =====================================================
-- 5. REACTIVAR VERIFICACIONES DE CLAVES FORÁNEAS
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 6. INSERTAR CATEGORÍAS BÁSICAS
-- =====================================================
INSERT INTO categorias (nombre, descripcion) VALUES
('Ficción', 'Novelas, cuentos y literatura de ficción'),
('Técnico', 'Libros técnicos, manuales y especializados'),
('Académico', 'Libros de texto y material académico'),
('Referencia', 'Diccionarios, enciclopedias y obras de consulta'),
('Ciencia', 'Libros de ciencias naturales y exactas'),
('Historia', 'Libros de historia, biografías y acontecimientos'),
('Arte', 'Libros de arte, música y cultura'),
('Infantil', 'Literatura infantil y juvenil');

-- =====================================================
-- 7. VERIFICACIÓN FINAL
-- =====================================================
SELECT 'ESTADO DESPUÉS DE LIMPIEZA:' AS info;
SELECT 'Usuarios:' AS tabla, COUNT(*) AS registros FROM usuarios
UNION ALL
SELECT 'Categorias:' AS tabla, COUNT(*) AS registros FROM categorias
UNION ALL
SELECT 'Libros:' AS tabla, COUNT(*) AS registros FROM libros
UNION ALL
SELECT 'Prestamos:' AS tabla, COUNT(*) AS registros FROM prestamos
UNION ALL
SELECT 'Solicitudes:' AS tabla, COUNT(*) AS registros FROM solicitudes_prestamo
UNION ALL
SELECT 'Historial:' AS tabla, COUNT(*) AS registros FROM historiallectura;

-- Mostrar usuarios mantenidos
SELECT 'USUARIOS PRESERVADOS:' AS titulo;
SELECT idUsuario, usuario, email, rol, fecha_registro 
FROM usuarios 
ORDER BY idUsuario;

-- Mostrar categorías creadas
SELECT 'CATEGORÍAS CREADAS:' AS titulo;
SELECT idCategoria, nombre, descripcion 
FROM categorias 
ORDER BY idCategoria;

SELECT '✅ LIMPIEZA COMPLETADA EXITOSAMENTE' AS resultado;
SELECT '🔄 Contadores AUTO_INCREMENT reseteados' AS detalle1;
SELECT '👥 Usuarios de login preservados (admin y cris1996)' AS detalle2;
SELECT '📚 8 categorías básicas creadas' AS detalle3;
SELECT '🗃️ Todas las demás tablas limpiadas' AS detalle4;
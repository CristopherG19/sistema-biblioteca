-- =====================================================
-- SCRIPT DE LIMPIEZA DE BASE DE DATOS
-- Mantiene usuarios de login y resetea contadores ID
-- Fecha: 2025-09-18
-- =====================================================

USE biblioteca_db;

-- =====================================================
-- 1. DESHABILITAR VERIFICACIONES DE CLAVES FORÁNEAS
-- =====================================================
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 2. LIMPIEZA DE DATOS (MANTENIENDO USUARIOS DE LOGIN)
-- =====================================================

-- Eliminar registros de historial de lectura
DELETE FROM historiallectura;

-- Eliminar intereses de usuario (excepto de usuarios de login)
DELETE FROM interesesusuario 
WHERE idUsuario NOT IN (SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996'));

-- Eliminar libros favoritos (excepto de usuarios de login)
DELETE FROM librosfavoritos 
WHERE idUsuario NOT IN (SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996'));

-- Eliminar lecturas de libros (excepto de usuarios de login)
DELETE FROM libroslecturas 
WHERE idUsuario NOT IN (SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996'));

-- Eliminar versiones de libros
DELETE FROM librosversiones;

-- Eliminar multas (excepto de usuarios de login)
DELETE FROM multas 
WHERE idUsuario NOT IN (SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996'));

-- Eliminar solicitudes de ampliación
DELETE FROM solicitudesampliacion;

-- Eliminar préstamos (excepto de usuarios de login)
DELETE FROM prestamos 
WHERE idUsuario NOT IN (SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996'));

-- Eliminar solicitudes de préstamo (excepto de usuarios de login)
DELETE FROM solicitudes_prestamo 
WHERE idUsuario NOT IN (SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996'));

-- Eliminar todos los libros (esto también limpiará las referencias)
DELETE FROM libros;

-- Eliminar todas las categorías
DELETE FROM categorias;

-- Eliminar usuarios que NO son de login
DELETE FROM usuarios 
WHERE idUsuario NOT IN (
    SELECT t.idUsuario FROM (
        SELECT idUsuario FROM usuarios WHERE rol = 1 OR usuario IN ('admin', 'cris1996')
    ) AS t
);

-- =====================================================
-- 3. RESETEAR CONTADORES AUTO_INCREMENT
-- =====================================================

-- Resetear contador de categorías
ALTER TABLE categorias AUTO_INCREMENT = 1;

-- Resetear contador de libros
ALTER TABLE libros AUTO_INCREMENT = 1;

-- Resetear contador de préstamos
ALTER TABLE prestamos AUTO_INCREMENT = 1;

-- Resetear contador de solicitudes de préstamo
ALTER TABLE solicitudes_prestamo AUTO_INCREMENT = 1;

-- Resetear contador de solicitudes de ampliación
ALTER TABLE solicitudesampliacion AUTO_INCREMENT = 1;

-- Resetear contador de multas
ALTER TABLE multas AUTO_INCREMENT = 1;

-- Resetear contador de historial de lectura
ALTER TABLE historiallectura AUTO_INCREMENT = 1;

-- Resetear contador de versiones de libros
ALTER TABLE librosversiones AUTO_INCREMENT = 1;

-- NOTA: NO reseteamos el contador de usuarios para mantener los IDs existentes

-- =====================================================
-- 4. REACTIVAR VERIFICACIONES DE CLAVES FORÁNEAS
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 5. INSERTAR CATEGORÍAS BÁSICAS PARA EMPEZAR
-- =====================================================
INSERT INTO categorias (nombre, descripcion) VALUES
('Ficción', 'Libros de ficción y literatura'),
('Técnico', 'Libros técnicos y especializados'),
('Académico', 'Libros académicos y de estudio'),
('Referencia', 'Libros de consulta y referencia');

-- =====================================================
-- 6. VERIFICACIÓN DE LIMPIEZA
-- =====================================================
SELECT 'VERIFICACIÓN DE LIMPIEZA COMPLETADA' AS estado;

SELECT 'Usuarios mantenidos:' AS info, COUNT(*) AS cantidad FROM usuarios;
SELECT 'Categorías insertadas:' AS info, COUNT(*) AS cantidad FROM categorias;
SELECT 'Libros restantes:' AS info, COUNT(*) AS cantidad FROM libros;
SELECT 'Préstamos restantes:' AS info, COUNT(*) AS cantidad FROM prestamos;
SELECT 'Solicitudes restantes:' AS info, COUNT(*) AS cantidad FROM solicitudes_prestamo;

-- Mostrar usuarios que se mantuvieron
SELECT 'USUARIOS MANTENIDOS:' AS info;
SELECT idUsuario, usuario, email, rol, fecha_registro FROM usuarios ORDER BY idUsuario;

SELECT '¡LIMPIEZA COMPLETADA EXITOSAMENTE!' AS resultado;
SELECT 'Los contadores AUTO_INCREMENT han sido reseteados' AS nota;
SELECT 'Los usuarios de login han sido preservados' AS nota2;
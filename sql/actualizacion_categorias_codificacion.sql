-- =====================================================
-- SCRIPT PARA ACTUALIZAR CATEGORÍAS CON CODIFICACIÓN CORRECTA
-- Arregla problemas de caracteres especiales
-- Fecha: 2025-09-18
-- =====================================================

USE biblioteca_db;

-- =====================================================
-- ACTUALIZAR CATEGORÍAS SIN CARACTERES ESPECIALES
-- =====================================================

UPDATE categorias SET 
    nombre = 'Ficcion', 
    descripcion = 'Novelas, cuentos y literatura de ficcion' 
WHERE idCategoria = 1;

UPDATE categorias SET 
    nombre = 'Tecnico', 
    descripcion = 'Libros tecnicos, manuales y especializados' 
WHERE idCategoria = 2;

UPDATE categorias SET 
    nombre = 'Academico', 
    descripcion = 'Libros de texto y material academico' 
WHERE idCategoria = 3;

UPDATE categorias SET 
    nombre = 'Referencia', 
    descripcion = 'Diccionarios, enciclopedias y obras de consulta' 
WHERE idCategoria = 4;

UPDATE categorias SET 
    nombre = 'Ciencia', 
    descripcion = 'Libros de ciencias naturales y exactas' 
WHERE idCategoria = 5;

UPDATE categorias SET 
    nombre = 'Historia', 
    descripcion = 'Libros de historia, biografias y acontecimientos' 
WHERE idCategoria = 6;

UPDATE categorias SET 
    nombre = 'Arte', 
    descripcion = 'Libros de arte, musica y cultura' 
WHERE idCategoria = 7;

UPDATE categorias SET 
    nombre = 'Infantil', 
    descripcion = 'Literatura infantil y juvenil' 
WHERE idCategoria = 8;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 'CATEGORÍAS ACTUALIZADAS CORRECTAMENTE:' AS resultado;
SELECT idCategoria, nombre, descripcion FROM categorias ORDER BY idCategoria;

-- =====================================================
-- RECOMENDACIONES PARA EVITAR PROBLEMAS FUTUROS
-- =====================================================
SELECT 'RECOMENDACIONES:' AS info;
SELECT 'Usar --default-character-set=utf8 en comandos mysql' AS consejo1;
SELECT 'Evitar caracteres especiales en nombres de categorías' AS consejo2;
SELECT 'Usar codificación UTF-8 en aplicaciones PHP' AS consejo3;
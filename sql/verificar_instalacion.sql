-- =====================================================
-- SCRIPT DE VERIFICACIÓN DE INSTALACIÓN
-- Sistema de Gestión Bibliotecaria
-- =====================================================
-- Este script verifica que la instalación se haya completado correctamente

USE biblioteca_db;

-- =====================================================
-- 1. VERIFICAR TABLAS CREADAS
-- =====================================================

SELECT 'VERIFICACIÓN DE TABLAS' as seccion;

SELECT 
    table_name as tabla,
    table_rows as filas,
    data_length as tamaño_datos,
    index_length as tamaño_indices
FROM information_schema.tables 
WHERE table_schema = 'biblioteca_db'
ORDER BY table_name;

-- =====================================================
-- 2. VERIFICAR PROCEDIMIENTOS ALMACENADOS
-- =====================================================

SELECT 'VERIFICACIÓN DE PROCEDIMIENTOS' as seccion;

SELECT 
    routine_name as procedimiento,
    routine_type as tipo,
    created as fecha_creacion
FROM information_schema.routines 
WHERE routine_schema = 'biblioteca_db' 
AND routine_type = 'PROCEDURE'
ORDER BY routine_name;

-- =====================================================
-- 3. VERIFICAR VISTAS CREADAS
-- =====================================================

SELECT 'VERIFICACIÓN DE VISTAS' as seccion;

SELECT 
    table_name as vista,
    view_definition as definicion
FROM information_schema.views 
WHERE table_schema = 'biblioteca_db'
ORDER BY table_name;

-- =====================================================
-- 4. VERIFICAR ÍNDICES
-- =====================================================

SELECT 'VERIFICACIÓN DE ÍNDICES' as seccion;

SELECT 
    table_name as tabla,
    index_name as indice,
    column_name as columna,
    non_unique as no_unico
FROM information_schema.statistics 
WHERE table_schema = 'biblioteca_db'
ORDER BY table_name, index_name;

-- =====================================================
-- 5. VERIFICAR DATOS INICIALES
-- =====================================================

SELECT 'VERIFICACIÓN DE DATOS INICIALES' as seccion;

-- Verificar roles
SELECT 'Roles creados:' as tipo, COUNT(*) as cantidad FROM Roles;
SELECT * FROM Roles;

-- Verificar categorías
SELECT 'Categorías creadas:' as tipo, COUNT(*) as cantidad FROM Categorias;
SELECT * FROM Categorias;

-- Verificar usuario administrador
SELECT 'Usuarios creados:' as tipo, COUNT(*) as cantidad FROM Usuarios;
SELECT idUsuario, nombre, apellido, usuario, email, rol FROM Usuarios;

-- =====================================================
-- 6. PROBAR PROCEDIMIENTOS PRINCIPALES
-- =====================================================

SELECT 'PRUEBA DE PROCEDIMIENTOS' as seccion;

-- Probar estadísticas de usuarios
CALL sp_usuario_estadisticas();

-- Probar estadísticas de préstamos
CALL sp_prestamo_obtener_estadisticas();

-- Probar estadísticas de solicitudes
CALL sp_solicitudes_estadisticas();

-- =====================================================
-- 7. VERIFICAR CONFIGURACIÓN DE CHARSET
-- =====================================================

SELECT 'VERIFICACIÓN DE CHARSET' as seccion;

SELECT 
    table_name as tabla,
    table_collation as collation
FROM information_schema.tables 
WHERE table_schema = 'biblioteca_db'
ORDER BY table_name;

-- =====================================================
-- 8. VERIFICAR TRIGGERS
-- =====================================================

SELECT 'VERIFICACIÓN DE TRIGGERS' as seccion;

SELECT 
    trigger_name as trigger,
    event_manipulation as evento,
    event_object_table as tabla,
    action_timing as timing
FROM information_schema.triggers 
WHERE trigger_schema = 'biblioteca_db'
ORDER BY event_object_table, trigger_name;

-- =====================================================
-- 9. RESUMEN DE INSTALACIÓN
-- =====================================================

SELECT 'RESUMEN DE INSTALACIÓN' as seccion;

SELECT 
    'Tablas' as componente,
    COUNT(*) as cantidad
FROM information_schema.tables 
WHERE table_schema = 'biblioteca_db'

UNION ALL

SELECT 
    'Procedimientos' as componente,
    COUNT(*) as cantidad
FROM information_schema.routines 
WHERE routine_schema = 'biblioteca_db' 
AND routine_type = 'PROCEDURE'

UNION ALL

SELECT 
    'Vistas' as componente,
    COUNT(*) as cantidad
FROM information_schema.views 
WHERE table_schema = 'biblioteca_db'

UNION ALL

SELECT 
    'Triggers' as componente,
    COUNT(*) as cantidad
FROM information_schema.triggers 
WHERE trigger_schema = 'biblioteca_db';

-- =====================================================
-- 10. INSTRUCCIONES FINALES
-- =====================================================

SELECT 'INSTRUCCIONES FINALES' as seccion;

SELECT 
    'Instalación completada exitosamente' as mensaje,
    'Configurar config/database.php' as siguiente_paso_1,
    'Iniciar sesión con admin/password' as siguiente_paso_2,
    'Crear categorías y libros de prueba' as siguiente_paso_3,
    'Registrar usuarios de prueba' as siguiente_paso_4,
    'Probar funcionalidades del sistema' as siguiente_paso_5;

-- =====================================================
-- FIN DEL SCRIPT DE VERIFICACIÓN
-- =====================================================

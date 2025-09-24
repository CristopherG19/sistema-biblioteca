-- Script para verificar y corregir el problema de fecha_actualizacion
-- Ejecuta estos comandos uno por uno en phpMyAdmin

-- 1. Verificar la base de datos actual
SELECT DATABASE();

-- 2. Verificar la estructura actual de categorias
DESCRIBE categorias;

-- 3. Verificar si la columna ya existe
SHOW COLUMNS FROM categorias LIKE 'fecha_actualizacion';

-- 4. Agregar la columna (solo si no existe)
ALTER TABLE categorias 
ADD COLUMN fecha_actualizacion datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
AFTER fecha_creacion;

-- 5. Verificar que se agregó correctamente
DESCRIBE categorias;

-- 6. Actualizar registros existentes
UPDATE categorias 
SET fecha_actualizacion = fecha_creacion 
WHERE fecha_actualizacion IS NULL;

-- 7. Verificar el resultado final
SELECT idCategoria, nombre, fecha_creacion, fecha_actualizacion 
FROM categorias 
LIMIT 5;

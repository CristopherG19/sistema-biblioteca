-- Script para corregir el error de fecha_actualizacion en categorias
-- Este script agrega la columna faltante a la tabla categorias

USE biblioteca_db;

-- Verificar la estructura actual de la tabla categorias
DESCRIBE categorias;

-- Agregar la columna fecha_actualizacion a la tabla categorias
ALTER TABLE categorias 
ADD COLUMN fecha_actualizacion datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
AFTER fecha_creacion;

-- Actualizar los registros existentes para que tengan fecha_actualizacion
UPDATE categorias 
SET fecha_actualizacion = fecha_creacion 
WHERE fecha_actualizacion IS NULL;

-- Verificar que la columna se agregó correctamente
DESCRIBE categorias;

-- Mostrar algunos registros para verificar
SELECT idCategoria, nombre, fecha_creacion, fecha_actualizacion 
FROM categorias 
LIMIT 5;

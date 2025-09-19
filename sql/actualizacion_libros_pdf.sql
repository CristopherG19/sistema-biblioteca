-- Script para actualizar la tabla Libros con soporte para archivos PDF
-- Fecha: 18 de septiembre de 2025

-- Agregar nuevas columnas a la tabla Libros
ALTER TABLE Libros ADD COLUMN archivo_pdf VARCHAR(255) NULL COMMENT 'Ruta del archivo PDF del libro';
ALTER TABLE Libros ADD COLUMN numero_paginas INT NULL COMMENT 'Número de páginas del libro digital';
ALTER TABLE Libros ADD COLUMN tamano_archivo BIGINT NULL COMMENT 'Tamaño del archivo en bytes';
ALTER TABLE Libros ADD COLUMN fecha_subida DATETIME NULL COMMENT 'Fecha de subida del archivo PDF';
ALTER TABLE Libros ADD COLUMN descripcion TEXT NULL COMMENT 'Descripción del libro';
ALTER TABLE Libros ADD COLUMN fecha_publicacion DATE NULL COMMENT 'Fecha de publicación del libro';

-- Crear tabla para versiones de archivos (por si se actualizan los PDFs)
CREATE TABLE IF NOT EXISTS LibrosVersiones (
    idVersion INT AUTO_INCREMENT PRIMARY KEY,
    idLibro INT NOT NULL,
    archivo_pdf VARCHAR(255) NOT NULL,
    numero_paginas INT NULL,
    tamano_archivo BIGINT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    version_numero VARCHAR(10) DEFAULT '1.0',
    es_version_actual BOOLEAN DEFAULT FALSE,
    comentarios TEXT NULL,
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro) ON DELETE CASCADE,
    INDEX idx_libro_version (idLibro, es_version_actual)
) ENGINE=InnoDB;

-- Crear tabla para tracking de descargas/lecturas
CREATE TABLE IF NOT EXISTS LibrosLecturas (
    idLectura INT AUTO_INCREMENT PRIMARY KEY,
    idLibro INT NOT NULL,
    idUsuario INT NOT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_lectura DATETIME NULL,
    pagina_actual INT DEFAULT 1,
    tiempo_lectura_minutos INT DEFAULT 0,
    completado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro) ON DELETE CASCADE,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_libro (idUsuario, idLibro),
    INDEX idx_usuario_lecturas (idUsuario),
    INDEX idx_libro_lecturas (idLibro)
) ENGINE=InnoDB;

-- Crear directorio para almacenar PDFs (esta parte se hará desde PHP)
-- El directorio será: public/uploads/libros/

-- Agregar índices para mejorar rendimiento
ALTER TABLE Libros ADD INDEX idx_archivo_pdf (archivo_pdf);
ALTER TABLE Libros ADD INDEX idx_fecha_subida (fecha_subida);

-- Actualizar libros existentes con valores por defecto
UPDATE Libros SET 
    descripcion = CONCAT('Descripción de ', titulo),
    fecha_publicacion = CONCAT(anio, '-01-01')
WHERE descripcion IS NULL OR fecha_publicacion IS NULL;
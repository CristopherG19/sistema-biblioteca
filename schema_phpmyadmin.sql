-- Script completo para crear la base de datos y todas las tablas en phpMyAdmin
-- Nombre sugerido: biblioteca_db

CREATE DATABASE IF NOT EXISTS biblioteca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_db;

-- Tabla de Roles
CREATE TABLE Roles (
    idRol INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    idUsuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    rol INT NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol) REFERENCES Roles(idRol)
);

-- Tabla de Categorias
CREATE TABLE Categorias (
    idCategoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);

-- Tabla de Libros
CREATE TABLE Libros (
    idLibro INT PRIMARY KEY AUTO_INCREMENT,
    idCategoria INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    editorial VARCHAR(100),
    anio INT,
    isbn VARCHAR(20) UNIQUE,
    stock INT NOT NULL DEFAULT 0,
    disponible INT NOT NULL DEFAULT 0,
    descripcion TEXT,
    fecha_adicion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idCategoria) REFERENCES Categorias(idCategoria)
);

-- Tabla de Prestamos
CREATE TABLE Prestamos (
    idPrestamo INT PRIMARY KEY AUTO_INCREMENT,
    idLibro INT NOT NULL,
    idUsuario INT NOT NULL,
    fechaPrestamo DATETIME DEFAULT CURRENT_TIMESTAMP,
    fechaDevolucionEsperada DATETIME NOT NULL,
    fechaDevolucionReal DATETIME NULL,
    estado ENUM('prestado', 'devuelto', 'atrasado') DEFAULT 'prestado',
    multa DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

-- Tabla de Multas (opcional, para historial detallado)
CREATE TABLE Multas (
    idMulta INT PRIMARY KEY AUTO_INCREMENT,
    idPrestamo INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    pagada BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idPrestamo) REFERENCES Prestamos(idPrestamo)
);

CREATE INDEX idx_usuarios_usuario ON Usuarios(usuario);
CREATE INDEX idx_libros_titulo ON Libros(titulo);
CREATE INDEX idx_libros_autor ON Libros(autor);
CREATE INDEX idx_prestamos_estado ON Prestamos(estado);
CREATE INDEX idx_prestamos_fecha ON Prestamos(fechaPrestamo);

-- Tabla de InteresesUsuario: categorías de interés por usuario
CREATE TABLE InteresesUsuario (
    idInteresUsuario INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idCategoria INT NOT NULL,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    FOREIGN KEY (idCategoria) REFERENCES Categorias(idCategoria)
);

-- Tabla de LibrosFavoritos: libros favoritos por usuario
CREATE TABLE LibrosFavoritos (
    idFavorito INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idLibro INT NOT NULL,
    fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro)
);

-- Tabla de HistorialLectura: historial de libros leídos/prestados por usuario
CREATE TABLE HistorialLectura (
    idHistorial INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    idLibro INT NOT NULL,
    fechaLectura DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    FOREIGN KEY (idLibro) REFERENCES Libros(idLibro)
);

-- Aquí comienzan los procedimientos almacenados
DELIMITER //

-- Multas
CREATE PROCEDURE sp_insertar_multa(
    IN p_idPrestamo INT,
    IN p_monto DECIMAL(10,2),
    IN p_descripcion TEXT
)
BEGIN
    INSERT INTO Multas (idPrestamo, monto, descripcion) VALUES (p_idPrestamo, p_monto, p_descripcion);
END //
CREATE PROCEDURE sp_actualizar_multa(
    IN p_idMulta INT,
    IN p_monto DECIMAL(10,2),
    IN p_descripcion TEXT,
    IN p_pagada BOOLEAN
)
BEGIN
    UPDATE Multas SET monto = p_monto, descripcion = p_descripcion, pagada = p_pagada WHERE idMulta = p_idMulta;
END //
CREATE PROCEDURE sp_eliminar_multa(IN p_idMulta INT)
BEGIN
    DELETE FROM Multas WHERE idMulta = p_idMulta;
END //

-- InteresesUsuario
CREATE PROCEDURE sp_listar_intereses_usuario(IN p_idUsuario INT)
BEGIN
    SELECT iu.*, c.nombre FROM InteresesUsuario iu JOIN Categorias c ON iu.idCategoria = c.idCategoria WHERE iu.idUsuario = p_idUsuario;
END //
CREATE PROCEDURE sp_insertar_interes_usuario(
    IN p_idUsuario INT,
    IN p_idCategoria INT
)
BEGIN
    INSERT INTO InteresesUsuario (idUsuario, idCategoria) VALUES (p_idUsuario, p_idCategoria);
END //
CREATE PROCEDURE sp_eliminar_interes_usuario(IN p_idInteresUsuario INT)
BEGIN
    DELETE FROM InteresesUsuario WHERE idInteresUsuario = p_idInteresUsuario;
END //

-- LibrosFavoritos
CREATE PROCEDURE sp_listar_libros_favoritos(IN p_idUsuario INT)
BEGIN
    SELECT lf.*, l.titulo FROM LibrosFavoritos lf JOIN Libros l ON lf.idLibro = l.idLibro WHERE lf.idUsuario = p_idUsuario;
END //
CREATE PROCEDURE sp_insertar_libro_favorito(
    IN p_idUsuario INT,
    IN p_idLibro INT
)
BEGIN
    INSERT INTO LibrosFavoritos (idUsuario, idLibro) VALUES (p_idUsuario, p_idLibro);
END //
CREATE PROCEDURE sp_eliminar_libro_favorito(IN p_idFavorito INT)
BEGIN
    DELETE FROM LibrosFavoritos WHERE idFavorito = p_idFavorito;
END //

-- HistorialLectura
CREATE PROCEDURE sp_listar_historial_lectura(IN p_idUsuario INT)
BEGIN
    SELECT hl.*, l.titulo FROM HistorialLectura hl JOIN Libros l ON hl.idLibro = l.idLibro WHERE hl.idUsuario = p_idUsuario;
END //
CREATE PROCEDURE sp_insertar_historial_lectura(
    IN p_idUsuario INT,
    IN p_idLibro INT
)
BEGIN
    INSERT INTO HistorialLectura (idUsuario, idLibro) VALUES (p_idUsuario, p_idLibro);
END //
CREATE PROCEDURE sp_eliminar_historial_lectura(IN p_idHistorial INT)
BEGIN
    DELETE FROM HistorialLectura WHERE idHistorial = p_idHistorial;
END //

-- Roles
CREATE PROCEDURE sp_listar_roles()
BEGIN
    SELECT * FROM Roles;
END //
CREATE PROCEDURE sp_insertar_rol(IN p_nombre VARCHAR(50))
BEGIN
    INSERT INTO Roles (nombre) VALUES (p_nombre);
END //
CREATE PROCEDURE sp_actualizar_rol(IN p_idRol INT, IN p_nombre VARCHAR(50))
BEGIN
    UPDATE Roles SET nombre = p_nombre WHERE idRol = p_idRol;
END //
CREATE PROCEDURE sp_eliminar_rol(IN p_idRol INT)
BEGIN
    DELETE FROM Roles WHERE idRol = p_idRol;
END //

DELIMITER ;

-- Procedimientos almacenados para Libros
DELIMITER //
CREATE PROCEDURE sp_listar_libros()
BEGIN
    SELECT l.*, c.nombre AS categoria FROM Libros l JOIN Categorias c ON l.idCategoria = c.idCategoria;
END //
CREATE PROCEDURE sp_insertar_libro(
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
END //
CREATE PROCEDURE sp_actualizar_libro(
    IN p_idLibro INT,
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
    UPDATE Libros SET idCategoria = p_idCategoria, titulo = p_titulo, autor = p_autor, editorial = p_editorial, anio = p_anio, isbn = p_isbn, stock = p_stock, disponible = p_disponible, descripcion = p_descripcion WHERE idLibro = p_idLibro;
END //
CREATE PROCEDURE sp_eliminar_libro(IN p_idLibro INT)
BEGIN
    DELETE FROM Libros WHERE idLibro = p_idLibro;
END //

-- Procedimientos almacenados para Categorías
CREATE PROCEDURE sp_listar_categorias()
BEGIN
    SELECT * FROM Categorias;
END //
CREATE PROCEDURE sp_insertar_categoria(
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT
)
BEGIN
    INSERT INTO Categorias (nombre, descripcion) VALUES (p_nombre, p_descripcion);
END //
CREATE PROCEDURE sp_actualizar_categoria(
    IN p_idCategoria INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT
)
BEGIN
    UPDATE Categorias SET nombre = p_nombre, descripcion = p_descripcion WHERE idCategoria = p_idCategoria;
END //
CREATE PROCEDURE sp_eliminar_categoria(IN p_idCategoria INT)
BEGIN
    DELETE FROM Categorias WHERE idCategoria = p_idCategoria;
END //

-- Procedimientos almacenados para Usuarios
CREATE PROCEDURE sp_listar_usuarios()
BEGIN
    SELECT u.*, r.nombre AS rol_nombre FROM Usuarios u JOIN Roles r ON u.rol = r.idRol;
END //
CREATE PROCEDURE sp_insertar_usuario(
    IN p_nombre VARCHAR(100),
    IN p_usuario VARCHAR(50),
    IN p_clave VARCHAR(255),
    IN p_rol INT,
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20)
)
BEGIN
    INSERT INTO Usuarios (nombre, usuario, clave, rol, email, telefono) VALUES (p_nombre, p_usuario, p_clave, p_rol, p_email, p_telefono);
END //
CREATE PROCEDURE sp_actualizar_usuario(
    IN p_idUsuario INT,
    IN p_nombre VARCHAR(100),
    IN p_usuario VARCHAR(50),
    IN p_clave VARCHAR(255),
    IN p_rol INT,
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(20)
)
BEGIN
    UPDATE Usuarios SET nombre = p_nombre, usuario = p_usuario, clave = p_clave, rol = p_rol, email = p_email, telefono = p_telefono WHERE idUsuario = p_idUsuario;
END //
CREATE PROCEDURE sp_eliminar_usuario(IN p_idUsuario INT)
BEGIN
    DELETE FROM Usuarios WHERE idUsuario = p_idUsuario;
END //

-- Procedimientos almacenados para Préstamos
CREATE PROCEDURE sp_listar_prestamos()
BEGIN
    SELECT p.*, l.titulo, u.nombre AS usuario FROM Prestamos p JOIN Libros l ON p.idLibro = l.idLibro JOIN Usuarios u ON p.idUsuario = u.idUsuario;
END //
CREATE PROCEDURE sp_insertar_prestamo(
    IN p_idLibro INT,
    IN p_idUsuario INT,
    IN p_fechaPrestamo DATETIME,
    IN p_fechaDevolucionEsperada DATETIME,
    IN p_estado ENUM('prestado', 'devuelto', 'atrasado'),
    IN p_multa DECIMAL(10,2)
)
BEGIN
    INSERT INTO Prestamos (idLibro, idUsuario, fechaPrestamo, fechaDevolucionEsperada, estado, multa) VALUES (p_idLibro, p_idUsuario, p_fechaPrestamo, p_fechaDevolucionEsperada, p_estado, p_multa);
END //
CREATE PROCEDURE sp_actualizar_prestamo(
    IN p_idPrestamo INT,
    IN p_fechaDevolucionReal DATETIME,
    IN p_estado ENUM('prestado', 'devuelto', 'atrasado'),
    IN p_multa DECIMAL(10,2)
)
BEGIN
    UPDATE Prestamos SET fechaDevolucionReal = p_fechaDevolucionReal, estado = p_estado, multa = p_multa WHERE idPrestamo = p_idPrestamo;
END //
CREATE PROCEDURE sp_eliminar_prestamo(IN p_idPrestamo INT)
BEGIN
    DELETE FROM Prestamos WHERE idPrestamo = p_idPrestamo;
END //

DELIMITER ;

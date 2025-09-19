-- Tabla para solicitudes de ampliación de préstamos
CREATE TABLE IF NOT EXISTS SolicitudesAmpliacion (
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    idPrestamo INT NOT NULL,
    diasAdicionales INT NOT NULL DEFAULT 7,
    motivo TEXT,
    fechaSolicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaRespuesta DATETIME NULL,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL DEFAULT 'Pendiente',
    respuestaBibliotecario TEXT,
    idBibliotecario INT NULL,
    
    FOREIGN KEY (idPrestamo) REFERENCES Prestamos(idPrestamo) ON DELETE CASCADE,
    FOREIGN KEY (idBibliotecario) REFERENCES Usuarios(idUsuario) ON DELETE SET NULL,
    
    INDEX idx_prestamo (idPrestamo),
    INDEX idx_estado (estado),
    INDEX idx_fecha_solicitud (fechaSolicitud)
);
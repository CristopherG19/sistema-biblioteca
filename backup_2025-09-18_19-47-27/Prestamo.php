<?php
require_once __DIR__ . '/../../config/database.php';

class Prestamo {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    // Obtener todos los préstamos
    public function getAll() {
        try {
            $stmt = $this->conexion->prepare("
                SELECT p.*, 
                       u.nombre as usuario_nombre,
                       u.email as usuario_email,
                       l.titulo as libro_titulo, 
                       l.autor as libro_autor,
                       l.isbn as libro_isbn
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                ORDER BY p.fechaPrestamo DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener préstamos por usuario
    public function getByUsuario($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT p.*, 
                       u.nombre as usuario_nombre,
                       u.email as usuario_email,
                       l.titulo as libro_titulo, 
                       l.autor as libro_autor,
                       l.isbn as libro_isbn
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idUsuario = ?
                ORDER BY p.fechaPrestamo DESC
            ");
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos por usuario: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener préstamos activos (no devueltos)
    public function getPrestamosActivos() {
        try {
            $stmt = $this->conexion->prepare("
                SELECT p.*, 
                       u.nombre as usuario_nombre,
                       l.titulo as libro_titulo, 
                       l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.estado = 'prestado' AND p.fechaDevolucionReal IS NULL
                ORDER BY p.fechaPrestamo DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos activos: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener préstamos vencidos
    public function getPrestamosVencidos() {
        try {
            $stmt = $this->conexion->prepare("
                SELECT p.*, 
                       u.nombre as usuario_nombre,
                       l.titulo as libro_titulo, 
                       l.autor as libro_autor,
                       DATEDIFF(CURDATE(), p.fechaDevolucionEsperada) as dias_vencidos
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.estado = 'prestado' 
                AND p.fechaDevolucionReal IS NULL 
                AND p.fechaDevolucionEsperada < CURDATE()
                ORDER BY dias_vencidos DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos vencidos: " . $e->getMessage());
            return [];
        }
    }
    
    // Insertar nuevo préstamo
    public function insertar($datos) {
        try {
            $this->conexion->beginTransaction();
            
            $stmt = $this->conexion->prepare("
                INSERT INTO Prestamos (idLibro, idUsuario, fechaPrestamo, fechaDevolucionEsperada, estado)
                VALUES (?, ?, ?, ?, 'prestado')
            ");
            
            $resultado = $stmt->execute([
                $datos['libro_id'],
                $datos['usuario_id'],
                $datos['fecha_prestamo'],
                $datos['fecha_devolucion_esperada']
            ]);
            
            if ($resultado) {
                // Actualizar disponibilidad del libro
                $stmt = $this->conexion->prepare("UPDATE Libros SET disponible = disponible - 1 WHERE idLibro = ?");
                $stmt->execute([$datos['libro_id']]);
                
                $this->conexion->commit();
                return ['success' => true, 'id' => $this->conexion->lastInsertId()];
            } else {
                $this->conexion->rollback();
                return ['success' => false, 'message' => 'Error al insertar préstamo'];
            }
        } catch (PDOException $e) {
            $this->conexion->rollback();
            error_log("Error al insertar préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
    
    // Devolver préstamo
    public function devolver($prestamo_id, $fecha_devolucion = null, $observaciones = null) {
        try {
            $this->conexion->beginTransaction();
            
            // Obtener datos del préstamo
            $stmt = $this->conexion->prepare("SELECT * FROM Prestamos WHERE idPrestamo = ?");
            $stmt->execute([$prestamo_id]);
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado");
            }
            
            // Actualizar préstamo
            $stmt = $this->conexion->prepare("
                UPDATE Prestamos 
                SET fechaDevolucionReal = ?, estado = 'devuelto'
                WHERE idPrestamo = ?
            ");
            
            $fecha_devolucion = $fecha_devolucion ?: date('Y-m-d H:i:s');
            $stmt->execute([$fecha_devolucion, $prestamo_id]);
            
            // Actualizar disponibilidad del libro
            $stmt = $this->conexion->prepare("UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = ?");
            $stmt->execute([$prestamo['idLibro']]);
            
            $this->conexion->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->conexion->rollback();
            error_log("Error al devolver préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Obtener estadísticas
    public function getEstadisticas() {
        try {
            $stmt = $this->conexion->prepare("
                SELECT
                    COUNT(*) as total_prestamos,
                    SUM(CASE WHEN fechaDevolucionReal IS NULL THEN 1 ELSE 0 END) as prestamos_activos,
                    SUM(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 ELSE 0 END) as prestamos_devueltos,
                    SUM(CASE WHEN fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < CURDATE() THEN 1 ELSE 0 END) as prestamos_vencidos,
                    SUM(CASE WHEN DATE(fechaPrestamo) = CURDATE() THEN 1 ELSE 0 END) as prestamos_hoy
                FROM Prestamos
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_prestamos' => 0,
                'prestamos_activos' => 0,
                'prestamos_devueltos' => 0,
                'prestamos_vencidos' => 0,
                'prestamos_hoy' => 0
            ];
        }
    }
    
    // Buscar préstamos
    public function buscar($termino) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT p.*, 
                       u.nombre as usuario_nombre,
                       l.titulo as libro_titulo, 
                       l.autor as libro_autor
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE (u.nombre LIKE ? OR l.titulo LIKE ? OR l.autor LIKE ?)
                ORDER BY p.fechaPrestamo DESC
            ");
            
            $busqueda = "%{$termino}%";
            $stmt->execute([$busqueda, $busqueda, $busqueda]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar préstamos: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener préstamo por ID
    public function getById($id) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT p.*, 
                       u.nombre as usuario_nombre, u.email as usuario_email,
                       l.titulo as libro_titulo, l.autor as libro_autor, l.isbn as libro_isbn
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idPrestamo = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener préstamo por ID: " . $e->getMessage());
            return null;
        }
    }
    
    // Validar disponibilidad de libro
    public function validarDisponibilidad($libro_id) {
        try {
            $stmt = $this->conexion->prepare("SELECT disponible FROM Libros WHERE idLibro = ?");
            $stmt->execute([$libro_id]);
            $libro = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $libro && $libro['disponible'] > 0;
        } catch (PDOException $e) {
            error_log("Error al validar disponibilidad: " . $e->getMessage());
            return false;
        }
    }
    
    // Método alias para compatibilidad
    public function verificarDisponibilidad($libro_id) {
        return $this->validarDisponibilidad($libro_id);
    }
    
    // Actualizar préstamo
    public function actualizar($id, $datos) {
        try {
            $stmt = $this->conexion->prepare("
                UPDATE Prestamos 
                SET fechaDevolucionEsperada = ?
                WHERE idPrestamo = ?
            ");
            
            $resultado = $stmt->execute([
                $datos['fecha_devolucion_esperada'],
                $id
            ]);
            
            return ['success' => $resultado];
        } catch (PDOException $e) {
            error_log("Error al actualizar préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
    
    // Registrar devolución
    public function registrarDevolucion($prestamo_id, $observaciones = null) {
        return $this->devolver($prestamo_id, date('Y-m-d H:i:s'), $observaciones);
    }
    
    // Eliminar préstamo
    public function eliminar($id) {
        try {
            $this->conexion->beginTransaction();
            
            // Obtener datos del préstamo
            $stmt = $this->conexion->prepare("SELECT * FROM Prestamos WHERE idPrestamo = ?");
            $stmt->execute([$id]);
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) {
                return ['success' => false, 'message' => 'Préstamo no encontrado'];
            }
            
            // Si el préstamo está activo, devolver el libro al inventario
            if ($prestamo['estado'] == 'prestado' && is_null($prestamo['fechaDevolucionReal'])) {
                $stmt = $this->conexion->prepare("UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = ?");
                $stmt->execute([$prestamo['idLibro']]);
            }
            
            // Eliminar préstamo
            $stmt = $this->conexion->prepare("DELETE FROM Prestamos WHERE idPrestamo = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                $this->conexion->commit();
                return ['success' => true];
            } else {
                $this->conexion->rollback();
                return ['success' => false, 'message' => 'Error al eliminar préstamo'];
            }
        } catch (PDOException $e) {
            $this->conexion->rollback();
            error_log("Error al eliminar préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
    
    public static function tienePrestamoActivo($idUsuario, $idLibro) {
        try {
            $pdo = obtenerConexion();
            $sql = "SELECT COUNT(*) as count FROM Prestamos 
                   WHERE idUsuario = :idUsuario 
                   AND idLibro = :idLibro 
                   AND fechaDevolucion IS NULL 
                   AND estado IN ('activo', 'pendiente')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario);
            $stmt->bindParam(':idLibro', $idLibro);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar préstamo activo: " . $e->getMessage());
            return false;
        }
    }

    // Marcar préstamo como devuelto por el usuario (autodevolucion)
    public function autodevolverPrestamo($prestamo_id, $usuario_id) {
        try {
            $this->conexion->beginTransaction();
            
            // Verificar que el préstamo pertenece al usuario
            $stmt = $this->conexion->prepare("
                SELECT p.*, l.titulo, l.autor 
                FROM Prestamos p 
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idPrestamo = ? AND p.idUsuario = ? AND p.fechaDevolucionReal IS NULL
            ");
            $stmt->execute([$prestamo_id, $usuario_id]);
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado o no pertenece al usuario");
            }
            
            // Marcar como devuelto
            $stmt = $this->conexion->prepare("
                UPDATE Prestamos 
                SET fechaDevolucionReal = NOW(), 
                    estado = 'devuelto',
                    observaciones = CONCAT(COALESCE(observaciones, ''), 'Autodevuelto por el usuario el ', NOW())
                WHERE idPrestamo = ?
            ");
            $stmt->execute([$prestamo_id]);
            
            // Aumentar disponibilidad del libro
            $stmt = $this->conexion->prepare("UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = ?");
            $stmt->execute([$prestamo['idLibro']]);
            
            $this->conexion->commit();
            return ['success' => true, 'message' => "Devolución registrada para: {$prestamo['titulo']}"];
        } catch (Exception $e) {
            $this->conexion->rollback();
            error_log("Error al autodevolver préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Solicitar ampliación de préstamo
    public function solicitarAmpliacion($prestamo_id, $usuario_id, $dias_adicionales, $motivo = '') {
        try {
            // Verificar que el préstamo pertenece al usuario y está activo
            $stmt = $this->conexion->prepare("
                SELECT p.*, l.titulo, l.autor 
                FROM Prestamos p 
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE p.idPrestamo = ? AND p.idUsuario = ? AND p.fechaDevolucionReal IS NULL
            ");
            $stmt->execute([$prestamo_id, $usuario_id]);
            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prestamo) {
                throw new Exception("Préstamo no encontrado o no pertenece al usuario");
            }
            
            // Verificar si ya hay una solicitud pendiente
            $stmt = $this->conexion->prepare("
                SELECT COUNT(*) as count FROM SolicitudesAmpliacion 
                WHERE idPrestamo = ? AND estado = 'Pendiente'
            ");
            $stmt->execute([$prestamo_id]);
            $yaExiste = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($yaExiste['count'] > 0) {
                throw new Exception("Ya existe una solicitud de ampliación pendiente para este préstamo");
            }
            
            // Crear registro de solicitud de ampliación
            $stmt = $this->conexion->prepare("
                INSERT INTO SolicitudesAmpliacion (idPrestamo, diasAdicionales, motivo, fechaSolicitud, estado)
                VALUES (?, ?, ?, NOW(), 'Pendiente')
            ");
            $stmt->execute([$prestamo_id, $dias_adicionales, $motivo]);
            
            return ['success' => true, 'message' => "Solicitud de ampliación enviada para: {$prestamo['titulo']}"];
        } catch (Exception $e) {
            error_log("Error al solicitar ampliación: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Obtener solicitudes de ampliación
    public function getSolicitudesAmpliacion($estado = null) {
        try {
            $sql = "
                SELECT sa.*, 
                       p.fechaDevolucionEsperada, p.fechaPrestamo,
                       l.titulo as libro_titulo, l.autor as libro_autor,
                       u.nombre as usuario_nombre, u.email as usuario_email,
                       b.nombre as bibliotecario_nombre
                FROM SolicitudesAmpliacion sa
                INNER JOIN Prestamos p ON sa.idPrestamo = p.idPrestamo
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                LEFT JOIN Usuarios b ON sa.idBibliotecario = b.idUsuario
            ";
            
            if ($estado) {
                $sql .= " WHERE sa.estado = ?";
                $stmt = $this->conexion->prepare($sql . " ORDER BY sa.fechaSolicitud DESC");
                $stmt->execute([$estado]);
            } else {
                $stmt = $this->conexion->prepare($sql . " ORDER BY sa.fechaSolicitud DESC");
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes de ampliación: " . $e->getMessage());
            return [];
        }
    }

    // Aprobar solicitud de ampliación
    public function aprobarSolicitudAmpliacion($solicitud_id, $bibliotecario_id, $respuesta = '') {
        try {
            $this->conexion->beginTransaction();
            
            // Obtener datos de la solicitud
            $stmt = $this->conexion->prepare("
                SELECT sa.*, p.fechaDevolucionEsperada, l.titulo
                FROM SolicitudesAmpliacion sa
                INNER JOIN Prestamos p ON sa.idPrestamo = p.idPrestamo
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                WHERE sa.idSolicitud = ? AND sa.estado = 'Pendiente'
            ");
            $stmt->execute([$solicitud_id]);
            $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$solicitud) {
                throw new Exception("Solicitud no encontrada o ya procesada");
            }
            
            // Calcular nueva fecha de devolución
            $fechaActual = new DateTime($solicitud['fechaDevolucionEsperada']);
            $fechaActual->add(new DateInterval("P{$solicitud['diasAdicionales']}D"));
            $nuevaFecha = $fechaActual->format('Y-m-d H:i:s');
            
            // Actualizar la fecha de devolución del préstamo
            $stmt = $this->conexion->prepare("
                UPDATE Prestamos 
                SET fechaDevolucionEsperada = ? 
                WHERE idPrestamo = ?
            ");
            $stmt->execute([$nuevaFecha, $solicitud['idPrestamo']]);
            
            // Actualizar la solicitud como aprobada
            $stmt = $this->conexion->prepare("
                UPDATE SolicitudesAmpliacion 
                SET estado = 'Aprobada', 
                    fechaRespuesta = NOW(), 
                    respuestaBibliotecario = ?,
                    idBibliotecario = ?
                WHERE idSolicitud = ?
            ");
            $stmt->execute([$respuesta, $bibliotecario_id, $solicitud_id]);
            
            $this->conexion->commit();
            return ['success' => true, 'message' => "Ampliación aprobada para: {$solicitud['titulo']}"];
        } catch (Exception $e) {
            $this->conexion->rollback();
            error_log("Error al aprobar ampliación: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Rechazar solicitud de ampliación
    public function rechazarSolicitudAmpliacion($solicitud_id, $bibliotecario_id, $respuesta = '') {
        try {
            $stmt = $this->conexion->prepare("
                UPDATE SolicitudesAmpliacion 
                SET estado = 'Rechazada', 
                    fechaRespuesta = NOW(), 
                    respuestaBibliotecario = ?,
                    idBibliotecario = ?
                WHERE idSolicitud = ? AND estado = 'Pendiente'
            ");
            $resultado = $stmt->execute([$respuesta, $bibliotecario_id, $solicitud_id]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Solicitud rechazada correctamente'];
            } else {
                throw new Exception("No se pudo rechazar la solicitud");
            }
        } catch (Exception $e) {
            error_log("Error al rechazar ampliación: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
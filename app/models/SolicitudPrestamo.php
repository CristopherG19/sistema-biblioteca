<?php
require_once __DIR__ . '/../../config/database.php';

class SolicitudPrestamo {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    // Obtener todas las solicitudes con filtro opcional por estado
    public function getAll($estado = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitudes_listar(?)");
            $stmt->execute([$estado]);
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener solicitudes de un usuario específico
    public function getByUsuario($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitudes_usuario(?)");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    // Insertar nueva solicitud
    public function insertar($usuario_id, $libro_id, $observaciones = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitud_insertar(?, ?, ?)");
            $stmt->execute([$usuario_id, $libro_id, $observaciones]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            if ($resultado['status'] === 'success') {
                return ['success' => true, 'id' => $resultado['idSolicitud'], 'message' => $resultado['message']];
            } else {
                return ['success' => false, 'message' => $resultado['message']];
            }
        } catch (PDOException $e) {
            error_log("Error al insertar solicitud: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar solicitud'];
        }
    }
    
    // Responder a una solicitud (ACTUALIZADO A PROCEDIMIENTO)
    public function responder($solicitud_id, $estado, $bibliotecario_id, $observaciones = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitud_responder(?, ?, ?, ?)");
            $stmt->execute([$solicitud_id, $estado, $bibliotecario_id, $observaciones]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return $resultado['status'] === 'success';
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $sql = "UPDATE solicitudes_prestamo 
                    SET estado = ?, bibliotecario_id = ?, observaciones_bibliotecario = ?, fecha_respuesta = NOW()
                    WHERE idSolicitud = ? AND estado = 'Pendiente'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$estado, $bibliotecario_id, $observaciones, $solicitud_id]);
            
            return $stmt->rowCount() > 0;
        }
    }
    
    // Aprobar solicitud y crear préstamo
    public function aprobarYCrearPrestamo($solicitud_id, $bibliotecario_id, $fecha_devolucion, $observaciones = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitud_aprobar_y_crear_prestamo(?, ?, ?, ?)");
            $stmt->execute([$solicitud_id, $bibliotecario_id, $fecha_devolucion, $observaciones]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor(); // Cerrar cursor para evitar errores PDO
            
            if ($resultado['status'] === 'success') {
                return ['success' => true, 'prestamo_id' => $resultado['prestamo_id'], 'message' => $resultado['message']];
            } else {
                return ['success' => false, 'message' => $resultado['message']];
            }
        } catch (PDOException $e) {
            error_log("Error al aprobar solicitud: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar aprobación: ' . $e->getMessage()];
        }
    }
    
    // Obtener estadísticas de solicitudes
    public function getEstadisticas() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitudes_estadisticas()");
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_solicitudes' => 0,
                'pendientes' => 0,
                'rechazadas' => 0,
                'convertidas' => 0,
                'solicitudes_hoy' => 0
            ];
        }
    }
    
    // Obtener estadísticas por usuario (ACTUALIZADO A PROCEDIMIENTO)
    public function getEstadisticasUsuario($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitudes_estadisticas_usuario(?)");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $sql = "
                SELECT
                    COUNT(*) as total_solicitudes,
                    SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
                FROM solicitudes_prestamo
                WHERE usuario_id = ?
            ";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$usuario_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Obtener una solicitud por ID (ACTUALIZADO A PROCEDIMIENTO)
    public function getById($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitud_obtener_por_id(?)");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $sql = "
                SELECT
                    s.*,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor
                FROM solicitudes_prestamo s
                INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
                INNER JOIN Libros l ON s.libro_id = l.idLibro
                WHERE s.idSolicitud = ?
            ";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Cancelar una solicitud (ACTUALIZADO A PROCEDIMIENTO)
    public function cancelar($solicitud_id, $usuario_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_solicitud_cancelar(?, ?)");
            $stmt->execute([$solicitud_id, $usuario_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return $resultado['status'] === 'success';
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $sql = "UPDATE solicitudes_prestamo 
                    SET estado = 'Rechazada', 
                        observaciones_bibliotecario = 'Cancelada por el usuario',
                        fecha_respuesta = NOW()
                    WHERE idSolicitud = ? 
                    AND usuario_id = ? 
                    AND estado = 'Pendiente'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$solicitud_id, $usuario_id]);
            
            return $stmt->rowCount() > 0;
        }
    }
    
    // Obtener libros disponibles para solicitud usando procedimiento almacenado
    public function getLibrosDisponibles() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_libros_disponibles_solicitud()");
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al obtener libros disponibles: " . $e->getMessage());
            // Fallback a consulta directa si falla el SP
            return $this->getLibrosDisponiblesDirect();
        }
    }
    
    // Método de fallback para obtener libros disponibles
    private function getLibrosDisponiblesDirect() {
        try {
            $sql = "SELECT 
                        l.idLibro,
                        l.titulo,
                        l.autor,
                        l.editorial,
                        l.anio,
                        l.isbn,
                        l.disponible,
                        l.descripcion,
                        c.nombre as categoria_nombre
                    FROM Libros l
                    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
                    WHERE l.disponible > 0
                    ORDER BY l.titulo";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en fallback de libros disponibles: " . $e->getMessage());
            return [];
        }
    }
}
?>
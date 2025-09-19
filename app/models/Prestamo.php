<?php
require_once __DIR__ . '/../../config/database.php';

class Prestamo {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    // Obtener todos los préstamos (fallback incluido)
    public function getAll() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_todos()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
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
        }
    }
    
    // Obtener préstamos por usuario (actualizado a procedimiento)
    public function getByUsuario($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_por_usuario(?)");
            $stmt->execute([$usuario_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Procedimiento sp_prestamo_obtener_por_usuario falló, usando fallback: " . $e->getMessage());
            
            // Fallback a consulta directa
            try {
                $stmt = $this->conexion->prepare("
                    SELECT p.*, 
                           l.titulo as libro_titulo, 
                           l.autor as libro_autor, 
                           l.isbn as libro_isbn, 
                           u.nombre as usuario_nombre
                    FROM prestamos p
                    INNER JOIN libros l ON p.idLibro = l.idLibro
                    INNER JOIN usuarios u ON p.idUsuario = u.idUsuario
                    WHERE p.idUsuario = ?
                    ORDER BY p.fechaPrestamo DESC
                ");
                $stmt->execute([$usuario_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                error_log("Error en fallback getByUsuario: " . $e2->getMessage());
                return [];
            }
        }
    }
    
    // Obtener préstamos activos (actualizado a procedimiento)
    public function getPrestamosActivos() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_activos()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos activos: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener préstamos vencidos (actualizado a procedimiento)
    public function getPrestamosVencidos() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_vencidos()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos vencidos: " . $e->getMessage());
            return [];
        }
    }
    
    // Insertar nuevo préstamo (actualizado a procedimiento)
    public function insertar($datos) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_insertar_completo(?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['libro_id'],
                $datos['usuario_id'],
                $datos['fecha_prestamo'],
                $datos['fecha_devolucion_esperada'],
                $datos['observaciones'] ?? null
            ]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            // Debug: registrar resultado del procedimiento SQL
            error_log("[DEBUG Prestamo.php] Resultado procedimiento insertar: " . print_r($resultado, true));
            $success = isset($resultado['status']) && $resultado['status'] === 'success';
            $idPrestamo = isset($resultado['idPrestamo']) ? $resultado['idPrestamo'] : null;
            $message = isset($resultado['message']) ? $resultado['message'] : 'Error desconocido en el procedimiento';
            return [
                'success' => $success,
                'id' => $idPrestamo,
                'message' => $message
            ];
        } catch (PDOException $e) {
            error_log("Error al insertar préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }
    
    // Devolver préstamo (actualizado a procedimiento)
    public function devolver($prestamo_id, $observaciones = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_devolver_completo(?, ?)");
            $stmt->execute([$prestamo_id, $observaciones]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return [
                'success' => $resultado['status'] === 'success',
                'message' => $resultado['message']
            ];
        } catch (PDOException $e) {
            error_log("Error al devolver préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }
    
    // Obtener estadísticas (actualizado a procedimiento)
    public function getEstadisticas() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_estadisticas()");
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return $resultado ?: [
                'total' => 0,
                'activos' => 0,
                'devueltos' => 0,
                'vencidos' => 0
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total' => 0,
                'activos' => 0,
                'devueltos' => 0,
                'vencidos' => 0
            ];
        }
    }
    
    // Obtener préstamos por libro (actualizado a procedimiento)
    public function getByLibro($libro_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_por_libro(?)");
            $stmt->execute([$libro_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener préstamos por libro: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener préstamo específico usuario-libro (actualizado a procedimiento)
    public function getByUsuarioLibro($usuario_id, $libro_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_usuario_libro(?, ?)");
            $stmt->execute([$usuario_id, $libro_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener préstamo usuario-libro: " . $e->getMessage());
            return null;
        }
    }

    // Métodos estático de compatibilidad para llamadas desde vistas/otros lugares
    // Comprueba si un usuario tiene un préstamo activo para un libro
    public static function tienePrestamoActivo($usuario_id, $libro_id) {
        try {
            $inst = new self();
            $prestamo = $inst->getByUsuarioLibro($usuario_id, $libro_id);
            if (!$prestamo) return false;

            // Si existe y la fecha de devolución real es NULL -> activo
            if (array_key_exists('fechaDevolucionReal', $prestamo)) {
                return $prestamo['fechaDevolucionReal'] === null || $prestamo['fechaDevolucionReal'] === '';
            }

            // Si el registro no contiene ese campo, considerar activo si fue encontrado
            return true;
        } catch (Exception $e) {
            error_log("Error en tienePrestamoActivo: " . $e->getMessage());
            return false;
        }
    }
    
    // Validar disponibilidad (actualizado a procedimiento)
    public function validarDisponibilidad($libro_id) {
        try {
            error_log("DEBUG DISPONIBILIDAD: ID recibido = " . print_r($libro_id, true));
            $stmt = $this->conexion->prepare("CALL sp_prestamo_validar_disponibilidad(?)");
            $stmt->execute([$libro_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("DEBUG DISPONIBILIDAD: Resultado procedimiento = " . print_r($resultado, true));
            $stmt->closeCursor();
            if (!$resultado) {
                error_log("DEBUG DISPONIBILIDAD: No se encontró el libro con ese ID");
                return false;
            }
            return ($resultado['disponible'] == 1 || $resultado['disponible'] === "1");
        } catch (PDOException $e) {
            error_log("DEBUG DISPONIBILIDAD: Error al validar disponibilidad: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar observaciones (actualizado a procedimiento)
    public function actualizarObservaciones($prestamo_id, $observaciones) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_actualizar_observaciones(?, ?)");
            $stmt->execute([$prestamo_id, $observaciones]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['affected_rows'] > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar observaciones: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar préstamo (actualizado a procedimiento)
    public function eliminar($prestamo_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_eliminar(?)");
            $stmt->execute([$prestamo_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return [
                'success' => $resultado['status'] === 'success',
                'message' => $resultado['message']
            ];
        } catch (PDOException $e) {
            error_log("Error al eliminar préstamo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }
    
    // =====================================================
    // MÉTODOS PARA AMPLIACIONES (NUEVOS CON PROCEDIMIENTOS)
    // =====================================================
    
    // Solicitar ampliación (actualizado a procedimiento)
    public function solicitarAmpliacion($prestamo_id, $dias_adicionales, $motivo) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_ampliacion_solicitar(?, ?, ?)");
            $stmt->execute([$prestamo_id, $dias_adicionales, $motivo]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return [
                'success' => $resultado['status'] === 'success',
                'id' => $resultado['idSolicitud'],
                'message' => $resultado['message']
            ];
        } catch (PDOException $e) {
            error_log("Error al solicitar ampliación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }
    
    // Obtener solicitudes de ampliación (actualizado a procedimiento)
    public function getSolicitudesAmpliacion($estado = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_ampliacion_obtener_solicitudes(?)");
            $stmt->execute([$estado]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes de ampliación: " . $e->getMessage());
            return [];
        }
    }
    
    // Aprobar solicitud de ampliación (actualizado a procedimiento)
    public function aprobarSolicitudAmpliacion($solicitud_id, $bibliotecario_id, $respuesta = '') {
        try {
            $stmt = $this->conexion->prepare("CALL sp_ampliacion_aprobar(?, ?, ?)");
            $stmt->execute([$solicitud_id, $bibliotecario_id, $respuesta]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return [
                'success' => $resultado['status'] === 'success',
                'message' => $resultado['message']
            ];
        } catch (PDOException $e) {
            error_log("Error al aprobar ampliación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }
    
    // Rechazar solicitud de ampliación (actualizado a procedimiento)
    public function rechazarSolicitudAmpliacion($solicitud_id, $bibliotecario_id, $respuesta = '') {
        try {
            $stmt = $this->conexion->prepare("CALL sp_ampliacion_rechazar(?, ?, ?)");
            $stmt->execute([$solicitud_id, $bibliotecario_id, $respuesta]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            
            return [
                'success' => $resultado['status'] === 'success',
                'message' => $resultado['message']
            ];
        } catch (PDOException $e) {
            error_log("Error al rechazar ampliación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del sistema'];
        }
    }
    
    // =====================================================
    // MÉTODOS DE COMPATIBILIDAD (MANTENER FUNCIONALIDAD EXISTENTE)
    // =====================================================
    
    // Obtener por ID (ACTUALIZADO A PROCEDIMIENTO)
    public function getById($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_prestamo_obtener_por_id(?)");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $this->conexion->prepare("SELECT * FROM Prestamos WHERE idPrestamo = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
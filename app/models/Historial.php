<?php
require_once __DIR__ . '/../../config/database.php';

class Historial {
    private $conexion;

    public function __construct() {
        $this->conexion = obtenerConexion();
    }

    /**
     * Registrar una actividad en el historial
     */
    public function registrarActividad($usuario_id, $tipo_actividad, $idLibro = null, $idPrestamo = null, $descripcion = '', $ip_address = null, $user_agent = null) {
        try {
            // Obtener IP y User Agent si no se proporcionan
            if ($ip_address === null) {
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            }
            if ($user_agent === null) {
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Sistema Biblioteca';
            }

            $stmt = $this->conexion->prepare("CALL sp_registrar_actividad(?, ?, ?, ?, ?, ?, ?)");
            $resultado = $stmt->execute([
                $usuario_id, 
                $tipo_actividad, 
                $idLibro, 
                $idPrestamo, 
                $descripcion, 
                $ip_address, 
                $user_agent
            ]);
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en registrarActividad: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener historial de un usuario
     */
    public function getHistorialUsuario($usuario_id, $limite = 50) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_obtener_historial_usuario(?, ?)");
            $stmt->execute([$usuario_id, $limite]);
            $resultado = $stmt->fetchAll();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getHistorialUsuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas del historial de un usuario
     */
    public function getEstadisticasHistorial($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_estadisticas_historial_usuario(?)");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getEstadisticasHistorial: " . $e->getMessage());
            return [
                'total_actividades' => 0,
                'total_prestamos' => 0,
                'total_devoluciones' => 0,
                'total_solicitudes' => 0,
                'total_visualizaciones' => 0,
                'total_busquedas' => 0,
                'ultima_actividad' => null
            ];
        }
    }

    /**
     * Registrar visualización de un libro
     */
    public function registrarVisualizacion($usuario_id, $libro_id, $libro_titulo) {
        return $this->registrarActividad(
            $usuario_id, 
            'visualizacion', 
            $libro_id, 
            null, 
            "Visualizó el libro: " . $libro_titulo
        );
    }

    /**
     * Registrar búsqueda
     */
    public function registrarBusqueda($usuario_id, $termino_busqueda) {
        return $this->registrarActividad(
            $usuario_id, 
            'busqueda', 
            null, 
            null, 
            "Buscó: " . $termino_busqueda
        );
    }

    /**
     * Registrar préstamo
     */
    public function registrarPrestamo($usuario_id, $libro_id, $prestamo_id, $libro_titulo) {
        return $this->registrarActividad(
            $usuario_id, 
            'prestamo', 
            $libro_id, 
            $prestamo_id, 
            "Solicitó préstamo del libro: " . $libro_titulo
        );
    }

    /**
     * Registrar devolución
     */
    public function registrarDevolucion($usuario_id, $libro_id, $prestamo_id, $libro_titulo) {
        return $this->registrarActividad(
            $usuario_id, 
            'devolucion', 
            $libro_id, 
            $prestamo_id, 
            "Devolvió el libro: " . $libro_titulo
        );
    }

    /**
     * Registrar solicitud
     */
    public function registrarSolicitud($usuario_id, $libro_id, $libro_titulo) {
        return $this->registrarActividad(
            $usuario_id, 
            'solicitud', 
            $libro_id, 
            null, 
            "Solicitó préstamo del libro: " . $libro_titulo
        );
    }

    /**
     * Obtener actividades recientes (últimas 10)
     */
    public function getActividadesRecientes($usuario_id, $limite = 10) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT 
                    h.tipo_actividad,
                    h.descripcion,
                    h.fecha_actividad,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor
                FROM historial_actividad h
                LEFT JOIN libros l ON h.idLibro = l.idLibro
                WHERE h.idUsuario = ?
                ORDER BY h.fecha_actividad DESC
                LIMIT ?
            ");
            $stmt->execute([$usuario_id, $limite]);
            $resultado = $stmt->fetchAll();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getActividadesRecientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener historial filtrado por tipo de actividad
     */
    public function getHistorialPorTipo($usuario_id, $tipo_actividad, $limite = 20) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT 
                    h.idHistorial,
                    h.tipo_actividad,
                    h.descripcion,
                    h.fecha_actividad,
                    l.titulo as libro_titulo,
                    l.autor as libro_autor,
                    l.isbn as libro_isbn
                FROM historial_actividad h
                LEFT JOIN libros l ON h.idLibro = l.idLibro
                WHERE h.idUsuario = ? AND h.tipo_actividad = ?
                ORDER BY h.fecha_actividad DESC
                LIMIT ?
            ");
            $stmt->execute([$usuario_id, $tipo_actividad, $limite]);
            $resultado = $stmt->fetchAll();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getHistorialPorTipo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpiar historial antiguo (más de 1 año)
     */
    public function limpiarHistorialAntiguo($usuario_id = null) {
        try {
            if ($usuario_id) {
                $stmt = $this->conexion->prepare("
                    DELETE FROM historial_actividad 
                    WHERE idUsuario = ? AND fecha_actividad < DATE_SUB(NOW(), INTERVAL 1 YEAR)
                ");
                $stmt->execute([$usuario_id]);
            } else {
                $stmt = $this->conexion->prepare("
                    DELETE FROM historial_actividad 
                    WHERE fecha_actividad < DATE_SUB(NOW(), INTERVAL 1 YEAR)
                ");
                $stmt->execute();
            }
            $stmt->closeCursor();
            
            return true;
        } catch (Exception $e) {
            error_log("Error en limpiarHistorialAntiguo: " . $e->getMessage());
            return false;
        }
    }
}
?>

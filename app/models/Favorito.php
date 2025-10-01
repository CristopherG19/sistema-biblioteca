<?php
require_once __DIR__ . '/../../config/database.php';

class Favorito {
    private $conexion;

    public function __construct() {
        $this->conexion = obtenerConexion();
    }

    /**
     * Agregar o quitar un libro de favoritos
     */
    public function toggleFavorito($usuario_id, $libro_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_toggle_favorito(?, ?)");
            $stmt->execute([$usuario_id, $libro_id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            
            return $resultado['accion']; // 'agregado' o 'removido'
        } catch (Exception $e) {
            error_log("Error en toggleFavorito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un libro está en favoritos
     */
    public function esFavorito($usuario_id, $libro_id) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT COUNT(*) as count 
                FROM favoritos 
                WHERE idUsuario = ? AND idLibro = ? AND activo = TRUE
            ");
            $stmt->execute([$usuario_id, $libro_id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            
            return $resultado['count'] > 0;
        } catch (Exception $e) {
            error_log("Error en esFavorito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los favoritos de un usuario
     */
    public function getFavoritosUsuario($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_listar_favoritos_usuario(?)");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetchAll();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getFavoritosUsuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de favoritos de un usuario
     */
    public function getEstadisticasFavoritos($usuario_id) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT 
                    COUNT(*) as total_favoritos,
                    COUNT(CASE WHEN l.disponible > 0 THEN 1 END) as favoritos_disponibles,
                    COUNT(CASE WHEN l.disponible = 0 THEN 1 END) as favoritos_no_disponibles
                FROM favoritos f
                INNER JOIN libros l ON f.idLibro = l.idLibro
                WHERE f.idUsuario = ? AND f.activo = TRUE AND l.activo = TRUE
            ");
            $stmt->execute([$usuario_id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getEstadisticasFavoritos: " . $e->getMessage());
            return [
                'total_favoritos' => 0,
                'favoritos_disponibles' => 0,
                'favoritos_no_disponibles' => 0
            ];
        }
    }

    /**
     * Eliminar un favorito específico
     */
    public function eliminarFavorito($usuario_id, $libro_id) {
        try {
            $stmt = $this->conexion->prepare("
                UPDATE favoritos 
                SET activo = FALSE 
                WHERE idUsuario = ? AND idLibro = ?
            ");
            $resultado = $stmt->execute([$usuario_id, $libro_id]);
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en eliminarFavorito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener favoritos recientes (últimos 5)
     */
    public function getFavoritosRecientes($usuario_id, $limite = 5) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT 
                    f.idFavorito,
                    f.fecha_agregado,
                    l.idLibro,
                    l.titulo,
                    l.autor,
                    l.portada,
                    c.nombre as categoria_nombre
                FROM favoritos f
                INNER JOIN libros l ON f.idLibro = l.idLibro
                INNER JOIN categorias c ON l.idCategoria = c.idCategoria
                WHERE f.idUsuario = ? 
                AND f.activo = TRUE 
                AND l.activo = TRUE
                ORDER BY f.fecha_agregado DESC
                LIMIT ?
            ");
            $stmt->execute([$usuario_id, $limite]);
            $resultado = $stmt->fetchAll();
            $stmt->closeCursor();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en getFavoritosRecientes: " . $e->getMessage());
            return [];
        }
    }
}
?>

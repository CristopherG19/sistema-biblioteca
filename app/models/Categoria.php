<?php
require_once __DIR__ . '/../../config/database.php';
class Categoria {
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query('CALL sp_listar_categorias()');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public static function getById($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_categoria_obtener_por_id(?)');
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa si el procedimiento falla
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $pdo->prepare('SELECT * FROM Categorias WHERE idCategoria = ?');
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
    }

    public static function insertar($datos) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_insertar_categoria(?,?)');
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion']
        ]);
    }

    public static function actualizar($id, $datos) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_actualizar_categoria(?,?,?)');
        return $stmt->execute([
            $id,
            $datos['nombre'],
            $datos['descripcion']
        ]);
    }

    public static function eliminar($id) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_eliminar_categoria(?)');
        return $stmt->execute([$id]);
    }
}

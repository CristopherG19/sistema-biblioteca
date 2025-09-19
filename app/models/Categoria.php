<?php
require_once __DIR__ . '/../../config/database.php';

class Categoria {
    public static function getAll() {
        $conexion = obtenerConexion();
        $stmt = $conexion->query('CALL sp_listar_categorias()');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public static function getById($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_categoria_obtener_por_id(?)');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function insertar($datos) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_insertar_categoria(?,?)');
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion']
        ]);
    }

    public static function actualizar($id, $datos) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_actualizar_categoria(?,?,?)');
        return $stmt->execute([
            $id,
            $datos['nombre'],
            $datos['descripcion']
        ]);
    }

    public static function eliminar($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_eliminar_categoria(?)');
        return $stmt->execute([$id]);
    }
}
?>
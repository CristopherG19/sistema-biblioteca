<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    // Obtener todos los usuarios
    public function getAll() {
        $stmt = $this->conexion->prepare("CALL sp_listar_usuarios()");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    
    // Obtener usuario por ID
    public function getById($id) {
        $stmt = $this->conexion->prepare("CALL sp_usuario_obtener_por_id(?)");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    
    // Insertar nuevo usuario
    public function insertar($datos) {
        $stmt = $this->conexion->prepare("CALL sp_insertar_usuario(?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $datos['nombre'],
            $datos['usuario'],
            $datos['password'],
            $datos['rol'],
            $datos['email'],
            $datos['telefono']
        ]);
    }
    
    // Actualizar usuario (usa procedimiento existente)
    public function actualizar($id, $datos) {
        
            $stmt = $this->conexion->prepare("CALL sp_actualizar_usuario(?, ?, ?, ?, ?, ?, ?)");
            
            $resultado = $stmt->execute([
                $id,
                $datos['nombre'],
                $datos['usuario'],
                $datos['password'],
                $datos['rol'],
                $datos['email'],
                $datos['telefono']
            ]);
            
            return $resultado;
        
    }
    
    // Eliminar usuario (usa procedimiento existente)
    public function eliminar($id) {
        
            $stmt = $this->conexion->prepare("CALL sp_eliminar_usuario(?)");
            return $stmt->execute([$id]);
        
    }
    
    // Obtener usuarios por rol (actualizado a procedimiento)
    public function getByRol($rol_id) {
        
            $stmt = $this->conexion->prepare("CALL sp_usuario_obtener_por_rol(?)");
            $stmt->execute([$rol_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        
    }
    
    // Obtener todos los roles
    public function getRoles() {
        $stmt = $this->conexion->prepare("CALL sp_listar_roles()");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    
    // Buscar usuarios (actualizado a procedimiento)
    public function buscar($termino) {
        
            $stmt = $this->conexion->prepare("CALL sp_usuario_buscar(?)");
            $stmt->execute([$termino]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        
    }
    
    // Verificar si usuario ya existe
    public function usuarioExiste($usuario, $excluir_id = null) {
        $stmt = $this->conexion->prepare("CALL sp_usuario_verificar_existe(?)");
        $stmt->execute([$usuario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $resultado['existe'] > 0;
    }
    
    // Verificar si email ya existe (actualizado a procedimiento)
    public function emailExiste($email, $excluir_id = null) {
        
            $stmt = $this->conexion->prepare("CALL sp_usuario_verificar_email(?, ?)");
            $stmt->execute([$email, $excluir_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['existe'] > 0;
        
    }
    
    // Obtener estadísticas de usuarios (actualizado a procedimiento)
    public function getEstadisticas() {
        
            $stmt = $this->conexion->prepare("CALL sp_usuario_estadisticas()");
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: [
                'total_usuarios' => 0,
                'total_lectores' => 0,
                'total_bibliotecarios' => 0,
                'nuevos_hoy' => 0
            ];
        
    }
    
    // Obtener usuario por nombre de usuario
    public function getByUsername($username) {
        $stmt = $this->conexion->prepare("CALL sp_usuario_obtener_por_username(?)");
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    
    // Actualizar último acceso del usuario (actualizado a procedimiento)
    public function actualizarUltimoAcceso($id) {
        
            $stmt = $this->conexion->prepare("CALL sp_usuario_actualizar_ultimo_acceso(?)");

            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['affected_rows'] > 0;
        
    }
    
    // Activar usuario
    public function activar($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_activar(?)");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['affected_rows'] > 0;
        } catch (Exception $e) {
            error_log("Error al activar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Desactivar usuario
    public function desactivar($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_desactivar(?)");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['affected_rows'] > 0;
        } catch (Exception $e) {
            error_log("Error al desactivar usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>

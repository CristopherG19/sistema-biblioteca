<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    // Obtener todos los usuarios (fallback incluido)
    public function getAll() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_listar_usuarios()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $this->conexion->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                ORDER BY u.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    // Obtener usuario por ID (fallback incluido)
    public function getById($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_obtener_por_id(?)");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $this->conexion->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                WHERE u.idUsuario = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Insertar nuevo usuario (usa procedimiento existente)
    public function insertar($datos) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_insertar_usuario(?, ?, ?, ?, ?, ?)");
            
            $resultado = $stmt->execute([
                $datos['nombre'],
                $datos['usuario'],
                $datos['clave'],
                $datos['rol'],
                $datos['email'],
                $datos['telefono']
            ]);
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al insertar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar usuario (usa procedimiento existente)
    public function actualizar($id, $datos) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_actualizar_usuario(?, ?, ?, ?, ?, ?, ?)");
            
            $resultado = $stmt->execute([
                $id,
                $datos['nombre'],
                $datos['usuario'],
                $datos['clave'],
                $datos['rol'],
                $datos['email'],
                $datos['telefono']
            ]);
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar usuario (usa procedimiento existente)
    public function eliminar($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_eliminar_usuario(?)");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener usuarios por rol (actualizado a procedimiento)
    public function getByRol($rol_id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_obtener_por_rol(?)");
            $stmt->execute([$rol_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios por rol: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener todos los roles (usa procedimiento existente)
    public function getRoles() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_listar_roles()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }
    
    // Buscar usuarios (actualizado a procedimiento)
    public function buscar($termino) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_buscar(?)");
            $stmt->execute([$termino]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al buscar usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    // Verificar si usuario ya existe (actualizado a procedimiento)
    public function usuarioExiste($usuario, $excluir_id = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_verificar_existe(?, ?)");
            $stmt->execute([$usuario, $excluir_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['existe'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si email ya existe (actualizado a procedimiento)
    public function emailExiste($email, $excluir_id = null) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_verificar_email(?, ?)");
            $stmt->execute([$email, $excluir_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['existe'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar email: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener estadísticas de usuarios (actualizado a procedimiento)
    public function getEstadisticas() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_estadisticas()");
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: [
                'total_usuarios' => 0,
                'total_lectores' => 0,
                'total_bibliotecarios' => 0,
                'nuevos_hoy' => 0
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_usuarios' => 0,
                'total_lectores' => 0,
                'total_bibliotecarios' => 0,
                'nuevos_hoy' => 0
            ];
        }
    }
    
    // Obtener usuario por nombre de usuario (actualizado a procedimiento)
    public function getByUsername($username) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_obtener_por_username(?)");
            $stmt->execute([$username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Procedimiento sp_usuario_obtener_por_username falló, usando fallback: " . $e->getMessage());
            
            // Fallback a consulta directa
            try {
                $stmt = $this->conexion->prepare("
                    SELECT u.*, r.nombre as rol_nombre 
                    FROM Usuarios u 
                    INNER JOIN Roles r ON u.rol = r.idRol 
                    WHERE u.usuario = ?
                ");
                $stmt->execute([$username]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                error_log("Error en fallback getByUsername: " . $e2->getMessage());
                return null;
            }
        }
    }
    
    // Actualizar último acceso del usuario (actualizado a procedimiento)
    public function actualizarUltimoAcceso($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_usuario_actualizar_ultimo_acceso(?)");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultado['affected_rows'] > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar último acceso: " . $e->getMessage());
            return false;
        }
    }
}
?>
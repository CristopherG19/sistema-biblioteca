<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    // Obtener todos los usuarios
    public function getAll() {
        try {
            $stmt = $this->conexion->prepare("CALL sp_listar_usuarios()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener usuario por ID
    public function getById($id) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                WHERE u.idUsuario = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return null;
        }
    }
    
    // Insertar nuevo usuario
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
    
    // Actualizar usuario
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
    
    // Eliminar usuario
    public function eliminar($id) {
        try {
            $stmt = $this->conexion->prepare("CALL sp_eliminar_usuario(?)");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener usuarios por rol
    public function getByRol($rol_id) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT u.*, 
                       r.nombre as rol_nombre,
                       u.nombre as nombreCompleto
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                WHERE u.rol = ?
                ORDER BY u.nombre
            ");
            $stmt->execute([$rol_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios por rol: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener todos los roles
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
    
    // Buscar usuarios
    public function buscar($termino) {
        try {
            $termino = "%$termino%";
            $stmt = $this->conexion->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                WHERE (u.nombre LIKE ? OR u.usuario LIKE ? OR u.email LIKE ?)
                ORDER BY u.nombre
            ");
            $stmt->execute([$termino, $termino, $termino]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    // Verificar si usuario ya existe
    public function usuarioExiste($usuario, $excluir_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM Usuarios WHERE usuario = ?";
            $params = [$usuario];
            
            if ($excluir_id) {
                $sql .= " AND idUsuario != ?";
                $params[] = $excluir_id;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si email ya existe
    public function emailExiste($email, $excluir_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM Usuarios WHERE email = ?";
            $params = [$email];
            
            if ($excluir_id) {
                $sql .= " AND idUsuario != ?";
                $params[] = $excluir_id;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar email: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener estadísticas de usuarios
    public function getEstadisticas() {
        try {
            $stmt = $this->conexion->prepare("
                SELECT 
                    COUNT(*) as total_usuarios,
                    SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
                    SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
                    SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as nuevos_hoy
                FROM Usuarios
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
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
    
    // Obtener usuario por nombre de usuario (para login)
    public function getByUsername($username) {
        try {
            $stmt = $this->conexion->prepare("
                SELECT u.*, r.nombre as rol_nombre 
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                WHERE u.usuario = ?
            ");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por username: " . $e->getMessage());
            return null;
        }
    }
    
    // Actualizar último acceso del usuario
    public function actualizarUltimoAcceso($id) {
        try {
            $stmt = $this->conexion->prepare("
                UPDATE Usuarios 
                SET ultimo_acceso = NOW() 
                WHERE idUsuario = ?
            ");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al actualizar último acceso: " . $e->getMessage());
            return false;
        }
    }
}
?>
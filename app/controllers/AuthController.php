<?php
// Iniciar sesión solo si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    // Mostrar formulario de login
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=dashboard');
            exit;
        }
        
        include __DIR__ . '/../views/auth/login.php';
    }
    
    // Procesar login
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario']);
            $password = $_POST['password'];
            $errores = [];
            
            // Validar campos
            if (empty($usuario)) {
                $errores[] = "El nombre de usuario es requerido";
            }
            
            if (empty($password)) {
                $errores[] = "La contraseña es requerida";
            }
            
            if (empty($errores)) {
                // Buscar usuario en la base de datos
                $userData = $this->usuarioModel->getByUsername($usuario);
                
                if ($userData && password_verify($password, $userData['clave'])) {
                    // Login exitoso - crear sesión
                    $_SESSION['usuario_id'] = $userData['idUsuario'];
                    $_SESSION['usuario_nombre'] = $userData['nombre'];
                    $_SESSION['usuario_username'] = $userData['usuario'];
                    $_SESSION['usuario_rol'] = $userData['rol'];
                    $_SESSION['usuario_rol_nombre'] = $userData['rol_nombre'];
                    $_SESSION['usuario_email'] = $userData['email'];
                    
                    // Actualizar último acceso
                    $this->usuarioModel->actualizarUltimoAcceso($userData['idUsuario']);
                    
                    // Redirigir al dashboard
                    header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=dashboard');
                    exit;
                } else {
                    $errores[] = "Usuario o contraseña incorrectos";
                }
            }
            
            // Si hay errores, mostrar formulario con errores
            include __DIR__ . '/../views/auth/login.php';
        }
    }
    
    // Cerrar sesión
    public function logout() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=login&mensaje=Sesión cerrada exitosamente');
        exit;
    }
    
    // Verificar si el usuario está autenticado
    public static function verificarAutenticacion() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=login&error=Debe iniciar sesión para acceder');
            exit;
        }
    }
    
    // Verificar si el usuario tiene el rol requerido
    public static function verificarRol($rolRequerido) {
        self::verificarAutenticacion();
        
        if ($_SESSION['usuario_rol'] != $rolRequerido) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=dashboard&error=No tienes permisos para acceder a esta sección');
            exit;
        }
    }
    
    // Verificar si es bibliotecario
    public static function esBibliotecario() {
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1;
    }
    
    // Verificar si es lector
    public static function esLector() {
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2;
    }
    
    // Obtener datos del usuario actual
    public static function getUsuarioActual() {
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'usuario' => $_SESSION['usuario_username'],
            'rol' => $_SESSION['usuario_rol'],
            'rol_nombre' => $_SESSION['usuario_rol_nombre'],
            'email' => $_SESSION['usuario_email']
        ];
    }
}
?>
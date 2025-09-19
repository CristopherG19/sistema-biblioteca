<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuariosController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    // Mostrar lista de usuarios
    public function index() {
        $usuarios = $this->usuarioModel->getAll();
        $roles = $this->usuarioModel->getRoles();
        $estadisticas = $this->usuarioModel->getEstadisticas();
        
        include __DIR__ . '/../views/usuarios/index.php';
    }
    
    // Mostrar formulario para agregar usuario
    public function agregar() {
        $roles = $this->usuarioModel->getRoles();
        include __DIR__ . '/../views/usuarios/agregar.php';
    }
    
    // Guardar nuevo usuario
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errores = $this->validarDatos($_POST);
            
            if (empty($errores)) {
                // Verificar si el usuario ya existe
                if ($this->usuarioModel->usuarioExiste($_POST['usuario'])) {
                    $errores[] = "El nombre de usuario ya está registrado en el sistema";
                } elseif ($this->usuarioModel->emailExiste($_POST['email'])) {
                    $errores[] = "El email ya está registrado en el sistema";
                } else {
                    // Preparar datos
                    $datos = [
                        'nombre' => trim($_POST['nombre']),
                        'usuario' => trim($_POST['usuario']),
                        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                        'rol' => $_POST['rol'],
                        'email' => trim($_POST['email']),
                        'telefono' => trim($_POST['telefono'])
                    ];
                    
                    if ($this->usuarioModel->insertar($datos)) {
                        header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&mensaje=Usuario agregado exitosamente');
                        exit;
                    } else {
                        $errores[] = "Error al agregar el usuario";
                    }
                }
            }
            
            // Si hay errores, mostrar formulario con errores
            $roles = $this->usuarioModel->getRoles();
            include __DIR__ . '/../views/usuarios/agregar.php';
        }
    }
    
    // Mostrar formulario para editar usuario
    public function editar() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $usuario = $this->usuarioModel->getById($id);
            $roles = $this->usuarioModel->getRoles();
            
            if ($usuario) {
                include __DIR__ . '/../views/usuarios/editar.php';
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&error=Usuario no encontrado');
                exit;
            }
        }
    }
    
    // Actualizar usuario
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            // Validar datos
            $errores = $this->validarDatos($_POST, true);
            
            if (empty($errores)) {
                // Verificar si el usuario ya existe (excluyendo el usuario actual)
                if ($this->usuarioModel->usuarioExiste($_POST['usuario'], $id)) {
                    $errores[] = "El nombre de usuario ya está registrado por otro usuario";
                } elseif ($this->usuarioModel->emailExiste($_POST['email'], $id)) {
                    $errores[] = "El email ya está registrado por otro usuario";
                } else {
                    // Preparar datos
                    $datos = [
                        'nombre' => trim($_POST['nombre']),
                        'usuario' => trim($_POST['usuario']),
                        'rol' => $_POST['rol'],
                        'email' => trim($_POST['email']),
                        'telefono' => trim($_POST['telefono'])
                    ];
                    
                    // Solo actualizar password si se proporcionó uno nuevo
                    if (!empty($_POST['password'])) {
                        $datos['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    } else {
                        // Mantener el password actual
                        $usuarioActual = $this->usuarioModel->getById($id);
                        $datos['password'] = $usuarioActual['password'];
                    }
                    
                    if ($this->usuarioModel->actualizar($id, $datos)) {
                        header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&mensaje=Usuario actualizado exitosamente');
                        exit;
                    } else {
                        $errores[] = "Error al actualizar el usuario";
                    }
                }
            }
            
            // Si hay errores, mostrar formulario con errores
            $usuario = $this->usuarioModel->getById($id);
            $roles = $this->usuarioModel->getRoles();
            include __DIR__ . '/../views/usuarios/editar.php';
        }
    }
    
    // Eliminar usuario
    public function eliminar() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            if ($this->usuarioModel->eliminar($id)) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&mensaje=Usuario eliminado exitosamente');
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&error=Error al eliminar el usuario');
            }
            exit;
        }
    }
    
    // Buscar usuarios
    public function buscar() {
        $termino = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        if (!empty($termino)) {
            $usuarios = $this->usuarioModel->buscar($termino);
        } else {
            $usuarios = $this->usuarioModel->getAll();
        }
        
        $roles = $this->usuarioModel->getRoles();
        $estadisticas = $this->usuarioModel->getEstadisticas();
        
        include __DIR__ . '/../views/usuarios/index.php';
    }
    
    // Filtrar usuarios por rol
    public function filtrarPorRol() {
        $rol_id = isset($_GET['rol']) ? $_GET['rol'] : null;
        
        if ($rol_id) {
            $usuarios = $this->usuarioModel->getByRol($rol_id);
        } else {
            $usuarios = $this->usuarioModel->getAll();
        }
        
        $roles = $this->usuarioModel->getRoles();
        $estadisticas = $this->usuarioModel->getEstadisticas();
        
        include __DIR__ . '/../views/usuarios/index.php';
    }
    
    // Validar datos del formulario
    private function validarDatos($datos, $esEdicion = false) {
        $errores = [];
        
        // Validar campos requeridos
        if (empty(trim($datos['nombre']))) {
            $errores[] = "El nombre es requerido";
        }
        
        if (empty(trim($datos['usuario']))) {
            $errores[] = "El nombre de usuario es requerido";
        } elseif (strlen(trim($datos['usuario'])) < 3) {
            $errores[] = "El nombre de usuario debe tener al menos 3 caracteres";
        }
        
        if (empty(trim($datos['email']))) {
            $errores[] = "El email es requerido";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no tiene un formato válido";
        }
        
        if (empty(trim($datos['telefono']))) {
            $errores[] = "El teléfono es requerido";
        }
        
        if (empty($datos['rol'])) {
            $errores[] = "El rol es requerido";
        }
        
        // Validar password solo si es necesario
        if (!$esEdicion && empty($datos['password'])) {
            $errores[] = "La contraseña es requerida";
        } elseif (!empty($datos['password']) && strlen($datos['password']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }
        
        return $errores;
    }
}
?>
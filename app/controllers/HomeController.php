<?php
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/AuthController.php';

class HomeController {
    public function index() {
        require_once '../app/views/home.php';
    }
    
    public function dashboard() {
        // Obtener datos para el dashboard según el rol del usuario
        $usuario = AuthController::getUsuarioActual();
        
        if (!$usuario) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=login');
            exit;
        }
        
        // Modelos para obtener estadísticas
        $libroModel = new Libro();
        $usuarioModel = new Usuario();
        $categoriaModel = new Categoria();
        
        // Estadísticas generales
        $estadisticas = [
            'libros' => $this->getEstadisticasLibros($libroModel),
            'usuarios' => $usuarioModel->getEstadisticas(),
            'categorias' => $this->getEstadisticasCategorias($categoriaModel)
        ];
        
        // Datos específicos según el rol
        if (AuthController::esBibliotecario()) {
            // Dashboard para bibliotecario
            $librosRecientes = $libroModel->getRecientes(5);
            $usuariosRecientes = $usuarioModel->getByRol(2); // Lectores
            
            require_once '../app/views/dashboard/bibliotecario.php';
        } else {
            // Dashboard para lector
            $librosDisponibles = $libroModel->getDisponibles(8);
            $misLibrosFavoritos = []; // TODO: Implementar favoritos
            
            require_once '../app/views/dashboard/lector.php';
        }
    }
    
    private function getEstadisticasLibros($libroModel) {
        try {
            $todos = $libroModel->getAll();
            $disponibles = 0;
            $prestados = 0;
            
            foreach ($todos as $libro) {
                if ($libro['disponible'] > 0) {
                    $disponibles++;
                } else {
                    $prestados++;
                }
            }
            
            return [
                'total' => count($todos),
                'disponibles' => $disponibles,
                'prestados' => $prestados
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'disponibles' => 0,
                'prestados' => 0
            ];
        }
    }
    
    private function getEstadisticasCategorias($categoriaModel) {
        try {
            $todas = $categoriaModel->getAll();
            return [
                'total' => count($todas)
            ];
        } catch (Exception $e) {
            return [
                'total' => 0
            ];
        }
    }
}

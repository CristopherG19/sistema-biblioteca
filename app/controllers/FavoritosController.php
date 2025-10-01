<?php
require_once __DIR__ . '/../models/Favorito.php';
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/AuthController.php';

class FavoritosController {
    private $favoritoModel;
    private $libroModel;

    public function __construct() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $this->favoritoModel = new Favorito();
        $this->libroModel = new Libro();
    }

    /**
     * Mostrar página principal de favoritos
     */
    public function index() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            
            // Obtener favoritos del usuario
            $favoritos = $this->favoritoModel->getFavoritosUsuario($usuario_id);
            
            // Obtener estadísticas
            $estadisticas = $this->favoritoModel->getEstadisticasFavoritos($usuario_id);
            
            include __DIR__ . '/../views/favoritos/index.php';
        } catch (Exception $e) {
            error_log("Error en FavoritosController::index: " . $e->getMessage());
            header('Location: index.php?page=dashboard&error=' . urlencode('Error al cargar favoritos'));
            exit;
        }
    }

    /**
     * Agregar/quitar favorito (AJAX)
     */
    public function toggle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        try {
            $usuario_id = $_SESSION['usuario_id'];
            $libro_id = $_POST['libro_id'] ?? null;

            if (!$libro_id) {
                echo json_encode(['success' => false, 'message' => 'ID de libro no válido']);
                exit;
            }

            // Verificar que el libro existe
            $libro = $this->libroModel->getById($libro_id);
            if (!$libro) {
                echo json_encode(['success' => false, 'message' => 'Libro no encontrado']);
                exit;
            }

            // Toggle favorito
            $accion = $this->favoritoModel->toggleFavorito($usuario_id, $libro_id);
            
            if ($accion) {
                // Registrar en historial
                $historial = new Historial();
                if ($accion === 'agregado') {
                    $historial->registrarActividad(
                        $usuario_id, 
                        'favorito_agregado', 
                        $libro_id, 
                        null, 
                        "Agregó a favoritos: " . $libro['titulo']
                    );
                } else {
                    $historial->registrarActividad(
                        $usuario_id, 
                        'favorito_removido', 
                        $libro_id, 
                        null, 
                        "Eliminó de favoritos: " . $libro['titulo']
                    );
                }

                echo json_encode([
                    'success' => true, 
                    'accion' => $accion,
                    'message' => $accion === 'agregado' ? 'Agregado a favoritos' : 'Eliminado de favoritos'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al procesar favorito']);
            }
        } catch (Exception $e) {
            error_log("Error en FavoritosController::toggle: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
        exit;
    }

    /**
     * Eliminar favorito específico
     */
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=favoritos&error=' . urlencode('Método no permitido'));
            exit;
        }

        try {
            $usuario_id = $_SESSION['usuario_id'];
            $libro_id = $_POST['libro_id'] ?? null;

            if (!$libro_id) {
                header('Location: index.php?page=favoritos&error=' . urlencode('ID de libro no válido'));
                exit;
            }

            $resultado = $this->favoritoModel->eliminarFavorito($usuario_id, $libro_id);
            
            if ($resultado) {
                header('Location: index.php?page=favoritos&mensaje=' . urlencode('Favorito eliminado exitosamente'));
            } else {
                header('Location: index.php?page=favoritos&error=' . urlencode('Error al eliminar favorito'));
            }
        } catch (Exception $e) {
            error_log("Error en FavoritosController::eliminar: " . $e->getMessage());
            header('Location: index.php?page=favoritos&error=' . urlencode('Error al eliminar favorito'));
        }
        exit;
    }

    /**
     * Obtener favoritos para AJAX (usado en dashboard)
     */
    public function getFavoritosRecientes() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limite = $_GET['limite'] ?? 5;
            
            $favoritos = $this->favoritoModel->getFavoritosRecientes($usuario_id, $limite);
            
            echo json_encode(['success' => true, 'favoritos' => $favoritos]);
        } catch (Exception $e) {
            error_log("Error en FavoritosController::getFavoritosRecientes: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al obtener favoritos']);
        }
        exit;
    }

    /**
     * Verificar si un libro es favorito (AJAX)
     */
    public function esFavorito() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $libro_id = $_GET['libro_id'] ?? null;

            if (!$libro_id) {
                echo json_encode(['success' => false, 'message' => 'ID de libro no válido']);
                exit;
            }

            $esFavorito = $this->favoritoModel->esFavorito($usuario_id, $libro_id);
            
            echo json_encode(['success' => true, 'esFavorito' => $esFavorito]);
        } catch (Exception $e) {
            error_log("Error en FavoritosController::esFavorito: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al verificar favorito']);
        }
        exit;
    }
}
?>

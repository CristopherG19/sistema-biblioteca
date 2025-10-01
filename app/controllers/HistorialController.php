<?php
require_once __DIR__ . '/../models/Historial.php';
require_once __DIR__ . '/AuthController.php';

class HistorialController {
    private $historialModel;

    public function __construct() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        $this->historialModel = new Historial();
    }

    /**
     * Mostrar página principal de historial
     */
    public function index() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limite = $_GET['limite'] ?? 50;
            $tipo_filtro = $_GET['tipo'] ?? 'todos';
            
            // Obtener historial del usuario
            if ($tipo_filtro === 'todos') {
                $historial = $this->historialModel->getHistorialUsuario($usuario_id, $limite);
            } else {
                $historial = $this->historialModel->getHistorialPorTipo($usuario_id, $tipo_filtro, $limite);
            }
            
            // Obtener estadísticas
            $estadisticas = $this->historialModel->getEstadisticasHistorial($usuario_id);
            
            // Obtener actividades recientes para el sidebar
            $actividadesRecientes = $this->historialModel->getActividadesRecientes($usuario_id, 10);
            
            include __DIR__ . '/../views/historial/index.php';
        } catch (Exception $e) {
            error_log("Error en HistorialController::index: " . $e->getMessage());
            header('Location: index.php?page=dashboard&error=' . urlencode('Error al cargar historial'));
            exit;
        }
    }

    /**
     * Mostrar historial de préstamos
     */
    public function prestamos() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limite = $_GET['limite'] ?? 30;
            
            $historial = $this->historialModel->getHistorialPorTipo($usuario_id, 'prestamo', $limite);
            $devoluciones = $this->historialModel->getHistorialPorTipo($usuario_id, 'devolucion', $limite);
            
            include __DIR__ . '/../views/historial/prestamos.php';
        } catch (Exception $e) {
            error_log("Error en HistorialController::prestamos: " . $e->getMessage());
            header('Location: index.php?page=historial&error=' . urlencode('Error al cargar historial de préstamos'));
            exit;
        }
    }

    /**
     * Mostrar historial de búsquedas
     */
    public function busquedas() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limite = $_GET['limite'] ?? 30;
            
            $busquedas = $this->historialModel->getHistorialPorTipo($usuario_id, 'busqueda', $limite);
            
            include __DIR__ . '/../views/historial/busquedas.php';
        } catch (Exception $e) {
            error_log("Error en HistorialController::busquedas: " . $e->getMessage());
            header('Location: index.php?page=historial&error=' . urlencode('Error al cargar historial de búsquedas'));
            exit;
        }
    }

    /**
     * Mostrar historial de visualizaciones
     */
    public function visualizaciones() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limite = $_GET['limite'] ?? 30;
            
            $visualizaciones = $this->historialModel->getHistorialPorTipo($usuario_id, 'visualizacion', $limite);
            
            include __DIR__ . '/../views/historial/visualizaciones.php';
        } catch (Exception $e) {
            error_log("Error en HistorialController::visualizaciones: " . $e->getMessage());
            header('Location: index.php?page=historial&error=' . urlencode('Error al cargar historial de visualizaciones'));
            exit;
        }
    }

    /**
     * Obtener estadísticas del historial (AJAX)
     */
    public function getEstadisticas() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $estadisticas = $this->historialModel->getEstadisticasHistorial($usuario_id);
            
            echo json_encode(['success' => true, 'estadisticas' => $estadisticas]);
        } catch (Exception $e) {
            error_log("Error en HistorialController::getEstadisticas: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al obtener estadísticas']);
        }
        exit;
    }

    /**
     * Obtener actividades recientes (AJAX)
     */
    public function getActividadesRecientes() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limite = $_GET['limite'] ?? 10;
            
            $actividades = $this->historialModel->getActividadesRecientes($usuario_id, $limite);
            
            echo json_encode(['success' => true, 'actividades' => $actividades]);
        } catch (Exception $e) {
            error_log("Error en HistorialController::getActividadesRecientes: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al obtener actividades recientes']);
        }
        exit;
    }

    /**
     * Limpiar historial antiguo
     */
    public function limpiar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=historial&error=' . urlencode('Método no permitido'));
            exit;
        }

        try {
            $usuario_id = $_SESSION['usuario_id'];
            $limpiar_todos = $_POST['limpiar_todos'] ?? false;
            
            if ($limpiar_todos && AuthController::esBibliotecario()) {
                // Solo bibliotecarios pueden limpiar todo el historial
                $resultado = $this->historialModel->limpiarHistorialAntiguo();
            } else {
                // Usuarios normales solo pueden limpiar su propio historial
                $resultado = $this->historialModel->limpiarHistorialAntiguo($usuario_id);
            }
            
            if ($resultado) {
                $mensaje = $limpiar_todos ? 'Historial antiguo limpiado exitosamente' : 'Tu historial antiguo ha sido limpiado';
                header('Location: index.php?page=historial&mensaje=' . urlencode($mensaje));
            } else {
                header('Location: index.php?page=historial&error=' . urlencode('Error al limpiar historial'));
            }
        } catch (Exception $e) {
            error_log("Error en HistorialController::limpiar: " . $e->getMessage());
            header('Location: index.php?page=historial&error=' . urlencode('Error al limpiar historial'));
        }
        exit;
    }

    /**
     * Exportar historial a CSV
     */
    public function exportar() {
        try {
            $usuario_id = $_SESSION['usuario_id'];
            $tipo = $_GET['tipo'] ?? 'todos';
            $formato = $_GET['formato'] ?? 'csv';
            
            if ($tipo === 'todos') {
                $historial = $this->historialModel->getHistorialUsuario($usuario_id, 1000);
            } else {
                $historial = $this->historialModel->getHistorialPorTipo($usuario_id, $tipo, 1000);
            }
            
            $filename = "historial_" . $tipo . "_" . date('Ymd_His') . "." . $formato;
            
            if ($formato === 'csv') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para UTF-8
                
                fputcsv($output, ['Fecha', 'Tipo', 'Descripción', 'Libro', 'Autor', 'ISBN']);
                
                foreach ($historial as $actividad) {
                    fputcsv($output, [
                        date('d/m/Y H:i', strtotime($actividad['fecha_actividad'])),
                        ucfirst($actividad['tipo_actividad']),
                        $actividad['descripcion'],
                        $actividad['libro_titulo'] ?? 'N/A',
                        $actividad['libro_autor'] ?? 'N/A',
                        $actividad['libro_isbn'] ?? 'N/A'
                    ]);
                }
                
                fclose($output);
            } else {
                header('Location: index.php?page=historial&error=' . urlencode('Formato no soportado'));
            }
        } catch (Exception $e) {
            error_log("Error en HistorialController::exportar: " . $e->getMessage());
            header('Location: index.php?page=historial&error=' . urlencode('Error al exportar historial'));
        }
        exit;
    }
}
?>

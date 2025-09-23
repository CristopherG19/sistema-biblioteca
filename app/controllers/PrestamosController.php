<?php
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/SolicitudPrestamo.php';

class PrestamosController {
    private $prestamoModel;
    private $usuarioModel;
    private $solicitudModel;
    
    public function __construct() {
        $this->prestamoModel = new Prestamo();
        $this->usuarioModel = new Usuario();
        $this->solicitudModel = new SolicitudPrestamo();
        
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=auth&action=login');
            exit;
        }
    }
    
    // Mostrar lista de préstamos
    public function index() {
        try {
            // Verificar permisos según el rol del usuario
            $rol_usuario = $_SESSION['usuario_rol'] ?? 0;
            
            if ($rol_usuario == 1) { // Bibliotecario - ve todos los préstamos
                $prestamos = $this->prestamoModel->getAll();
            } else { // Lector - solo ve sus préstamos
                $prestamos = $this->prestamoModel->getByUsuario($_SESSION['usuario_id']);
            }
            
            // Obtener estadísticas
            $estadisticas_raw = $this->prestamoModel->getEstadisticas();
            
            // Mapear estadísticas al formato esperado por la vista
            $estadisticas = [
                'total_prestamos' => $estadisticas_raw['total'] ?? 0,
                'prestamos_activos' => $estadisticas_raw['activos'] ?? 0,
                'prestamos_devueltos' => $estadisticas_raw['devueltos'] ?? 0,
                'prestamos_vencidos' => $estadisticas_raw['vencidos'] ?? 0
            ];
            
            // Si es lector, filtrar estadísticas solo para él
            if ($rol_usuario != 1) {
                $prestamosUsuario = $this->prestamoModel->getByUsuario($_SESSION['usuario_id']);
                $estadisticas = [
                    'total_prestamos' => count($prestamosUsuario),
                    'prestamos_activos' => count(array_filter($prestamosUsuario, function($p) { 
                        return is_null($p['fechaDevolucionReal']); 
                    })),
                    'prestamos_devueltos' => count(array_filter($prestamosUsuario, function($p) { 
                        return !is_null($p['fechaDevolucionReal']); 
                    })),
                    'prestamos_vencidos' => count(array_filter($prestamosUsuario, function($p) { 
                        return is_null($p['fechaDevolucionReal']) && 
                               strtotime($p['fechaDevolucionEsperada']) < time(); 
                    })),
                    'prestamos_hoy' => count(array_filter($prestamosUsuario, function($p) { 
                        return date('Y-m-d', strtotime($p['fechaPrestamo'])) == date('Y-m-d'); 
                    }))
                ];
            }
            
            include __DIR__ . '/../views/prestamos/index.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::index: " . $e->getMessage());
            $_GET['error'] = 'Error al cargar los préstamos';
            include __DIR__ . '/../views/prestamos/index.php';
        }
    }
    
    // Mostrar formulario para agregar préstamo
    public function agregar() {
        // Solo bibliotecarios pueden crear préstamos
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para crear préstamos'));
            exit;
        }
        
        try {
            $usuarios = $this->usuarioModel->getAll();
            $libros = Libro::getAll();
            
            include __DIR__ . '/../views/prestamos/agregar.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::agregar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar el formulario'));
            exit;
        }
    }
    
    // Guardar nuevo préstamo
    public function guardar() {
        // Solo bibliotecarios pueden crear préstamos
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para crear préstamos'));
            exit;
        }
        
        $errores = [];
        
        // Validar datos
        $usuario_id = $_POST['usuario_id'] ?? '';
        $libro_id = $_POST['libro_id'] ?? '';
        $fecha_prestamo = $_POST['fecha_prestamo'] ?? date('Y-m-d');
        $fecha_devolucion_esperada = $_POST['fecha_devolucion'] ?? ''; // El formulario envía 'fecha_devolucion'
        $observaciones = $_POST['observaciones'] ?? '';
        
        // Validaciones
        if (empty($usuario_id)) {
            $errores[] = 'Debe seleccionar un usuario';
        }
        
        if (empty($libro_id)) {
            $errores[] = 'Debe seleccionar un libro';
        }
        
        if (empty($fecha_devolucion_esperada)) {
            $errores[] = 'La fecha de devolución esperada es obligatoria';
        }
        
        // Validar fechas
        if (!empty($fecha_prestamo) && !empty($fecha_devolucion_esperada)) {
            if (strtotime($fecha_devolucion_esperada) <= strtotime($fecha_prestamo)) {
                $errores[] = 'La fecha de devolución debe ser posterior a la fecha de préstamo';
            }
        }
        
        // Verificar disponibilidad del libro

        // Verificar préstamo duplicado primero
        if (!empty($usuario_id) && !empty($libro_id) && Prestamo::tienePrestamoActivo($usuario_id, $libro_id)) {
            $errores[] = 'El usuario ya tiene un préstamo activo de este libro';
        }

        // Verificar disponibilidad del libro
        if (!empty($libro_id) && !in_array('El usuario ya tiene un préstamo activo de este libro', $errores) && !$this->prestamoModel->validarDisponibilidad($libro_id)) {
            $errores[] = 'El libro seleccionado no está disponible para préstamo';
        }
        
        if (!empty($errores)) {
            $usuarios = $this->usuarioModel->getAll();
            $libros = Libro::getAll();
            include __DIR__ . '/../views/prestamos/agregar.php';
            return;
        }
        
        try {
            $datos = [
                'usuario_id' => $usuario_id,
                'libro_id' => $libro_id,
                'fecha_prestamo' => $fecha_prestamo,
                'fecha_devolucion_esperada' => $fecha_devolucion_esperada,
                'observaciones' => $observaciones
            ];
            
            $resultado = $this->prestamoModel->insertar($datos);
            // Debug: registrar resultado completo y mensaje de error
            error_log("[DEBUG PrestamosController] Resultado insertar: " . print_r($resultado, true));
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode('Préstamo registrado exitosamente'));
            } else {
                $mensaje_error = isset($resultado['message']) ? $resultado['message'] : 'Error al registrar el préstamo';
                error_log("[DEBUG PrestamosController] Mensaje enviado a vista: " . $mensaje_error);
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode($mensaje_error));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::guardar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al procesar el préstamo'));
        }
        exit;
    }
    
    // Mostrar formulario para editar préstamo
    public function editar() {
        // Solo bibliotecarios pueden editar préstamos
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para editar préstamos'));
            exit;
        }
        
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        try {
            $prestamo = $this->prestamoModel->getById($id);
            
            if (!$prestamo) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Préstamo no encontrado'));
                exit;
            }
            
            $usuarios = $this->usuarioModel->getAll();
            $libros = Libro::getAll();
            
            include __DIR__ . '/../views/prestamos/editar.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::editar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar el préstamo'));
            exit;
        }
    }
    
    // Actualizar préstamo
    public function actualizar() {
        // Solo bibliotecarios pueden actualizar préstamos
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para actualizar préstamos'));
            exit;
        }
        
        $id = $_POST['id'] ?? '';
        $errores = [];
        
        if (empty($id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        // Validar datos
        $usuario_id = $_POST['usuario_id'] ?? '';
        $libro_id = $_POST['libro_id'] ?? '';
        $fecha_prestamo = $_POST['fecha_prestamo'] ?? '';
        $fecha_devolucion_esperada = $_POST['fecha_devolucion_esperada'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        // Validaciones (similar a guardar)
        if (empty($usuario_id)) {
            $errores[] = 'Debe seleccionar un usuario';
        }
        
        if (empty($libro_id)) {
            $errores[] = 'Debe seleccionar un libro';
        }
        
        if (empty($fecha_devolucion_esperada)) {
            $errores[] = 'La fecha de devolución esperada es obligatoria';
        }
        
        if (!empty($errores)) {
            $prestamo = $this->prestamoModel->getById($id);
            $usuarios = $this->usuarioModel->getAll();
            $libros = Libro::getAll();
            include __DIR__ . '/../views/prestamos/editar.php';
            return;
        }
        
        try {
            $datos = [
                'usuario_id' => $usuario_id,
                'libro_id' => $libro_id,
                'fecha_prestamo' => $fecha_prestamo,
                'fecha_devolucion_esperada' => $fecha_devolucion_esperada,
                'observaciones' => $observaciones
            ];
            
            $resultado = $this->prestamoModel->actualizar($id, $datos);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode('Préstamo actualizado exitosamente'));
            } else {
                $mensaje_error = isset($resultado['message']) ? $resultado['message'] : 'Error al actualizar el préstamo';
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode($mensaje_error));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::actualizar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al procesar la actualización'));
        }
        exit;
    }
    
    // Registrar devolución
    public function devolver() {
        // Solo bibliotecarios pueden registrar devoluciones
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para registrar devoluciones'));
            exit;
        }
        
        $id = $_POST['id'] ?? $_GET['id'] ?? '';
        $observaciones_devolucion = $_POST['observaciones_devolucion'] ?? '';
        
        if (empty($id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        try {
            $resultado = $this->prestamoModel->registrarDevolucion($id, $observaciones_devolucion);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode('Devolución registrada exitosamente'));
            } else {
                $mensaje_error = isset($resultado['message']) ? $resultado['message'] : 'Error al registrar la devolución';
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode($mensaje_error));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::devolver: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al procesar la devolución'));
        }
        exit;
    }
    
    // Eliminar préstamo
    public function eliminar() {
        // Solo bibliotecarios pueden eliminar préstamos
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para eliminar préstamos'));
            exit;
        }
        
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        try {
            $resultado = $this->prestamoModel->eliminar($id);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode('Préstamo eliminado exitosamente'));
            } else {
                $mensaje_error = isset($resultado['message']) ? $resultado['message'] : 'Error al eliminar el préstamo';
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode($mensaje_error));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::eliminar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al procesar la eliminación'));
        }
        exit;
    }
    
    // Buscar préstamos
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        
        try {
            $rol_usuario = $_SESSION['usuario_rol'] ?? 0;
            
            if (!empty($termino)) {
                $prestamos = $this->prestamoModel->buscar($termino);
                
                // Si es lector, filtrar solo sus préstamos
                if ($rol_usuario != 1) {
                    $prestamos = array_filter($prestamos, function($prestamo) {
                        return $prestamo['usuario_id'] == $_SESSION['usuario_id'];
                    });
                }
            } else {
                if ($rol_usuario == 1) {
                    $prestamos = $this->prestamoModel->getAll();
                } else {
                    $prestamos = $this->prestamoModel->getByUsuario($_SESSION['usuario_id']);
                }
            }
            
            // Obtener estadísticas
            $estadisticas_raw = $this->prestamoModel->getEstadisticas();
            
            // Mapear estadísticas al formato esperado por la vista
            $estadisticas = [
                'total_prestamos' => $estadisticas_raw['total'] ?? 0,
                'prestamos_activos' => $estadisticas_raw['activos'] ?? 0,
                'prestamos_devueltos' => $estadisticas_raw['devueltos'] ?? 0,
                'prestamos_vencidos' => $estadisticas_raw['vencidos'] ?? 0
            ];
            
            include __DIR__ . '/../views/prestamos/index.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::buscar: " . $e->getMessage());
            $_GET['error'] = 'Error al buscar préstamos';
            $this->index();
        }
    }
    
    // Obtener préstamos vencidos (para bibliotecarios)
    public function vencidos() {
        // Solo bibliotecarios pueden ver préstamos vencidos de todos
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para ver esta información'));
            exit;
        }
        
        try {
            $prestamos = $this->prestamoModel->getPrestamosVencidos();
            $estadisticas_raw = $this->prestamoModel->getEstadisticas();
            
            // Mapear estadísticas al formato esperado por la vista
            $estadisticas = [
                'total_prestamos' => $estadisticas_raw['total'] ?? 0,
                'prestamos_activos' => $estadisticas_raw['activos'] ?? 0,
                'prestamos_devueltos' => $estadisticas_raw['devueltos'] ?? 0,
                'prestamos_vencidos' => $estadisticas_raw['vencidos'] ?? 0
            ];
            
            include __DIR__ . '/../views/prestamos/vencidos.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::vencidos: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar préstamos vencidos'));
            exit;
        }
    }
    
    // API para obtener información de libro (AJAX)
    public function getLibroInfo() {
        header('Content-Type: application/json');
        
        $libro_id = $_GET['libro_id'] ?? '';
        
        if (empty($libro_id)) {
            echo json_encode(['error' => 'ID de libro requerido']);
            exit;
        }
        
        try {
            $libro = Libro::getById($libro_id);
            $disponible = $this->prestamoModel->verificarDisponibilidad($libro_id);
            
            if ($libro) {
                echo json_encode([
                    'success' => true,
                    'libro' => $libro,
                    'disponible' => $disponible
                ]);
            } else {
                echo json_encode(['error' => 'Libro no encontrado']);
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::getLibroInfo: " . $e->getMessage());
            echo json_encode(['error' => 'Error al obtener información del libro']);
        }
        exit;
    }
    
    // === MÉTODOS PARA SOLICITUDES DE PRÉSTAMO ===
    
    // Mostrar formulario para solicitar préstamo (para lectores)
    public function solicitar() {
        try {
            $libros = $this->solicitudModel->getLibrosDisponibles();
            include __DIR__ . '/../views/prestamos/solicitar.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::solicitar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar el formulario'));
            exit;
        }
    }
    
    // Procesar solicitud de préstamo
    public function procesarSolicitud() {
        $errores = [];
        
        // Validar datos
        $libro_id = $_POST['libro_id'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (empty($libro_id)) {
            $errores[] = 'Debe seleccionar un libro';
        }
        
        // Verificar que no tenga una solicitud pendiente para el mismo libro
        // (Esta verificación se puede agregar más tarde como procedimiento almacenado)
        
        if (!empty($errores)) {
            $libros = Libro::getAll();
            include __DIR__ . '/../views/prestamos/solicitar.php';
            return;
        }
        
        try {
            $resultado = $this->solicitudModel->insertar($_SESSION['usuario_id'], $libro_id, $observaciones);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes&mensaje=' . urlencode($resultado['message'] ?? 'Solicitud enviada exitosamente'));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar&error=' . urlencode($resultado['message'] ?? 'Error al procesar solicitud'));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::procesarSolicitud: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar&error=' . urlencode('Error al procesar la solicitud'));
        }
        exit;
    }
    
    // Ver mis solicitudes (para lectores)
    public function misSolicitudes() {
        try {
            $estado_filtro = $_GET['estado'] ?? null;
            $solicitudes = $this->solicitudModel->getByUsuario($_SESSION['usuario_id']);
            
            // Aplicar filtro por estado si se especifica
            if ($estado_filtro) {
                $solicitudes = array_filter($solicitudes, function($s) use ($estado_filtro) {
                    return $s['estado'] == $estado_filtro;
                });
            }
            
            $estadisticas = [
                'total' => count($solicitudes),
                'pendientes' => count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Pendiente'; })),
                'aprobadas' => count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Aprobada'; })),
                'rechazadas' => count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Rechazada'; })),
                'convertidas' => count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Convertida'; }))
            ];
            
            include __DIR__ . '/../views/prestamos/mis_solicitudes.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::misSolicitudes: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=dashboard&error=' . urlencode('Error al cargar las solicitudes'));
            exit;
        }
    }
    
    // Cancelar solicitud (para lectores)
    public function cancelarSolicitud() {
        $solicitud_id = $_GET['id'] ?? '';
        
        if (empty($solicitud_id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes&error=' . urlencode('ID de solicitud no válido'));
            exit;
        }
        
        try {
            $resultado = $this->solicitudModel->cancelar($solicitud_id, $_SESSION['usuario_id']);
            
            if ($resultado) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes&mensaje=' . urlencode('Solicitud cancelada exitosamente'));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes&error=' . urlencode('No se pudo cancelar la solicitud'));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::cancelarSolicitud: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes&error=' . urlencode('Error al procesar la cancelación'));
        }
        exit;
    }
    
    // Gestionar solicitudes (para bibliotecarios)
    public function gestionarSolicitudes() {
        // Solo bibliotecarios pueden gestionar solicitudes
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para gestionar solicitudes'));
            exit;
        }
        
        try {
            $estado_filtro = $_GET['estado'] ?? null;
            $solicitudes = $this->solicitudModel->getAll($estado_filtro);
            $estadisticas = $this->solicitudModel->getEstadisticas();
            
            include __DIR__ . '/../views/prestamos/gestionar_solicitudes.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::gestionarSolicitudes: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar las solicitudes'));
            exit;
        }
    }
    
    // Aprobar solicitud y crear préstamo
    public function aprobarSolicitud() {
        // Solo bibliotecarios pueden aprobar solicitudes
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para aprobar solicitudes'));
            exit;
        }
        
        $solicitud_id = $_POST['solicitud_id'] ?? '';
        $fecha_devolucion = $_POST['fecha_devolucion'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (empty($solicitud_id) || empty($fecha_devolucion)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&error=' . urlencode('Datos incompletos'));
            exit;
        }
        
        try {
            $resultado = $this->solicitudModel->aprobarYCrearPrestamo($solicitud_id, $_SESSION['usuario_id'], $fecha_devolucion, $observaciones);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&mensaje=' . urlencode('Solicitud aprobada y préstamo creado exitosamente'));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&error=' . urlencode($resultado['message'] ?? 'Error al aprobar la solicitud. El libro podría no estar disponible.'));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::aprobarSolicitud: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&error=' . urlencode('Error al procesar la aprobación'));
        }
        exit;
    }
    
    // Rechazar solicitud
    public function rechazarSolicitud() {
        // Solo bibliotecarios pueden rechazar solicitudes
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para rechazar solicitudes'));
            exit;
        }
        
        $solicitud_id = $_POST['solicitud_id'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (empty($solicitud_id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&error=' . urlencode('ID de solicitud no válido'));
            exit;
        }
        
        try {
            $resultado = $this->solicitudModel->responder($solicitud_id, 'Rechazada', $_SESSION['usuario_id'], $observaciones);
            
            if ($resultado) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&mensaje=' . urlencode('Solicitud rechazada exitosamente'));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&error=' . urlencode('Error al rechazar la solicitud'));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::rechazarSolicitud: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&error=' . urlencode('Error al procesar el rechazo'));
        }
        exit;
    }
    
    // Mostrar formulario para otorgar préstamo directo (solo bibliotecarios)
    public function gestionar() {
        // Verificar que sea bibliotecario
        if ($_SESSION['usuario_rol'] != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=libros&error=' . urlencode('No tiene permisos para esta acción'));
            exit;
        }
        
        if (!isset($_GET['libro_id'])) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=libros&error=' . urlencode('ID de libro no especificado'));
            exit;
        }
        
        $libro_id = $_GET['libro_id'];
        
        try {
            // Obtener información del libro
            $libro = Libro::getById($libro_id);
            if (!$libro) {
                throw new Exception('Libro no encontrado');
            }
            
            // Obtener lista de lectores (usuarios con rol 2)
            $usuario = new Usuario();
            $lectores = $usuario->getByRol(2);
            
            include __DIR__ . '/../views/prestamos/otorgar.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::gestionar: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=libros&error=' . urlencode('Error al cargar el formulario'));
            exit;
        }
    }
    
    // Procesar otorgamiento de préstamo directo
    public function procesar_otorgar() {
        // Verificar que sea bibliotecario
        if ($_SESSION['usuario_rol'] != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=libros&error=' . urlencode('No tiene permisos para esta acción'));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=libros');
            exit;
        }
        
        // Validar datos requeridos
        $errores = [];
        if (empty($_POST['idLibro'])) $errores[] = 'ID de libro es requerido';
        if (empty($_POST['idUsuario'])) $errores[] = 'Usuario es requerido';
        if (empty($_POST['fechaPrestamo'])) $errores[] = 'Fecha de préstamo es requerida';
        if (empty($_POST['fechaVencimiento'])) $errores[] = 'Fecha de vencimiento es requerida';
        
        if (!empty($errores)) {
            $libro = Libro::getById($_POST['idLibro']);
            $usuario = new Usuario();
            $lectores = $usuario->getByRol(2);
            $error = implode(', ', $errores);
            include __DIR__ . '/../views/prestamos/otorgar.php';
            return;
        }
        
        try {
            // DEBUG: Log de datos recibidos por POST
            error_log("DEBUG PRESTAMO: POST recibido: " . print_r($_POST, true));
            error_log("DEBUG PRESTAMO: idLibro recibido: " . ($_POST['idLibro'] ?? 'NO_SET'));

            // PRIMERO: Verificar que el usuario no tenga préstamo activo del mismo libro
            if (Prestamo::tienePrestamoActivo($_POST['idUsuario'], $_POST['idLibro'])) {
                throw new Exception('El usuario ya tiene un préstamo activo de este libro');
            }

            // SEGUNDO: Verificar disponibilidad del libro
            $libro = Libro::getById($_POST['idLibro']);

            // DEBUG: Log para ver qué devuelve
            error_log("DEBUG PRESTAMO: Libro obtenido: " . print_r($libro, true));
            error_log("DEBUG PRESTAMO: Disponible = " . ($libro['disponible'] ?? 'NULL'));

            if (!$libro || $libro['disponible'] <= 0) {
                error_log("DEBUG PRESTAMO: Fallo en validación - libro=" . ($libro ? 'existe' : 'no existe') . ", disponible=" . ($libro['disponible'] ?? 'NULL'));
                throw new Exception('El libro no está disponible para préstamo');
            }
            
            // Crear datos del préstamo
            $datosPrestamo = [
                'idUsuario' => $_POST['idUsuario'],
                'idLibro' => $_POST['idLibro'],
                'fechaPrestamo' => $_POST['fechaPrestamo'],
                'fechaVencimiento' => $_POST['fechaVencimiento'],
                'estado' => 'activo',
                'observaciones' => $_POST['observaciones'] ?? '',
                'idBibliotecario' => $_SESSION['usuario_id']
            ];
            
            // Insertar préstamo
            $prestamo = new Prestamo();
            $resultado = $prestamo->insertar($datosPrestamo);
            
            if ($resultado) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . 
                       urlencode('Préstamo otorgado exitosamente'));
            } else {
                throw new Exception('Error al crear el préstamo');
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::procesar_otorgar: " . $e->getMessage());
            $libro = Libro::getById($_POST['idLibro']);
            $usuario = new Usuario();
            $lectores = $usuario->getByRol(2);
            $error = $e->getMessage();
            include __DIR__ . '/../views/prestamos/otorgar.php';
        }
    }
    
    // Autodevolver préstamo (para lectores)
    public function autodevolverLibro() {
        $prestamo_id = $_POST['prestamo_id'] ?? $_GET['id'] ?? '';
        
        if (empty($prestamo_id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        try {
            $resultado = $this->prestamoModel->autodevolverPrestamo($prestamo_id);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode($resultado['message']));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode($resultado['message']));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::autodevolverLibro: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al procesar la devolución'));
        }
        exit;
    }
    
    // Solicitar ampliación de préstamo (para lectores)
    public function solicitarAmpliacion() {
        $prestamo_id = $_POST['prestamo_id'] ?? '';
        $dias_adicionales = $_POST['dias_adicionales'] ?? 7;
        $motivo = $_POST['motivo'] ?? '';
        
        if (empty($prestamo_id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        try {
            $resultado = $this->prestamoModel->solicitarAmpliacion($prestamo_id, $dias_adicionales, $motivo);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode($resultado['message']));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode($resultado['message']));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::solicitarAmpliacion: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al procesar la solicitud de ampliación'));
        }
        exit;
    }
    
    // Gestionar solicitudes de ampliación (para bibliotecarios)
    public function gestionarAmpliaciones() {
        // Solo bibliotecarios pueden gestionar ampliaciones
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para gestionar ampliaciones'));
            exit;
        }
        
        try {
            $estado_filtro = $_GET['estado'] ?? null;
            $solicitudes = $this->prestamoModel->getSolicitudesAmpliacion($estado_filtro);
            include __DIR__ . '/../views/prestamos/gestionar_ampliaciones.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::gestionarAmpliaciones: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar las solicitudes'));
            exit;
        }
    }
    
    // Aprobar solicitud de ampliación
    public function aprobarAmpliacion() {
        // Solo bibliotecarios pueden aprobar ampliaciones
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para aprobar ampliaciones'));
            exit;
        }
        
        $solicitud_id = $_POST['solicitud_id'] ?? '';
        $respuesta = $_POST['respuesta'] ?? '';
        
        if (empty($solicitud_id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode('ID de solicitud no válido'));
            exit;
        }
        
        try {
            $resultado = $this->prestamoModel->aprobarSolicitudAmpliacion($solicitud_id, $_SESSION['usuario_id'], $respuesta);
            
            if ($resultado['success']) {
                // Asegurar codificación UTF-8 en el header
                header('Content-Type: text/html; charset=UTF-8');
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&mensaje=' . urlencode($resultado['message']));
            } else {
                header('Content-Type: text/html; charset=UTF-8');
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode($resultado['message']));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::aprobarAmpliacion: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode('Error al procesar la aprobación'));
        }
        exit;
    }
    
    // Rechazar solicitud de ampliación
    public function rechazarAmpliacion() {
        // Solo bibliotecarios pueden rechazar ampliaciones
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para rechazar ampliaciones'));
            exit;
        }
        
        $solicitud_id = $_POST['solicitud_id'] ?? '';
        $respuesta = $_POST['respuesta'] ?? '';
        
        if (empty($solicitud_id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode('ID de solicitud no válido'));
            exit;
        }
        
        if (empty($respuesta)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode('El motivo del rechazo es obligatorio'));
            exit;
        }
        
        try {
            $resultado = $this->prestamoModel->rechazarSolicitudAmpliacion($solicitud_id, $_SESSION['usuario_id'], $respuesta);
            
            if ($resultado['success']) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&mensaje=' . urlencode($resultado['message']));
            } else {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode($resultado['message']));
            }
        } catch (Exception $e) {
            error_log("Error en PrestamosController::rechazarAmpliacion: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&error=' . urlencode('Error al procesar el rechazo'));
        }
        exit;
    }
    
    // Ampliar duración del préstamo (para bibliotecarios)
    public function ampliarDuracionPrestamo() {
        // Solo bibliotecarios pueden ampliar duración
        if (($_SESSION['usuario_rol'] ?? 0) != 1) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('No tienes permisos para ampliar duración'));
            exit;
        }
        
        $prestamo_id = $_POST['prestamo_id'] ?? '';
        $dias_adicionales = $_POST['dias_adicionales'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        
        if (empty($prestamo_id) || empty($dias_adicionales)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Datos incompletos'));
            exit;
        }
        
        try {
            // Obtener información del préstamo
            $prestamo = $this->prestamoModel->getById($prestamo_id);
            
            if (!$prestamo) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Préstamo no encontrado'));
                exit;
            }
            
            // Calcular nueva fecha de devolución
            $fecha_actual = $prestamo['fechaDevolucionEsperada'];
            $nueva_fecha = date('Y-m-d', strtotime($fecha_actual . ' + ' . $dias_adicionales . ' days'));
            
            // Actualizar la fecha de devolución esperada
            $conexion = obtenerConexion();
            $stmt = $conexion->prepare("
                UPDATE prestamos 
                SET fechaDevolucionEsperada = ?,
                    observaciones = CASE 
                        WHEN observaciones IS NOT NULL 
                        THEN CONCAT(observaciones, ' | Ampliación: ', ?)
                        ELSE CONCAT('Ampliación: ', ?)
                    END
                WHERE idPrestamo = ?
            ");
            
            $observacion_ampliacion = "Ampliado por " . $dias_adicionales . " días. Motivo: " . $motivo;
            $stmt->execute([$nueva_fecha, $observacion_ampliacion, $observacion_ampliacion, $prestamo_id]);
            
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&mensaje=' . urlencode('Duración del préstamo ampliada exitosamente'));
            
        } catch (Exception $e) {
            error_log("Error en PrestamosController::ampliarDuracionPrestamo: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al ampliar la duración'));
        }
        exit;
    }
    
    // Ver detalles de préstamo (solo lectura)
    public function verDetalles() {
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('ID de préstamo no válido'));
            exit;
        }
        
        try {
            $prestamo = $this->prestamoModel->getById($id);
            
            if (!$prestamo) {
                header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Préstamo no encontrado'));
                exit;
            }
            
            // Obtener información adicional del usuario y libro
            $usuario = $this->usuarioModel->getById($prestamo['idUsuario']);
            $libro = Libro::getById($prestamo['idLibro']);
            
            include __DIR__ . '/../views/prestamos/ver_detalles.php';
        } catch (Exception $e) {
            error_log("Error en PrestamosController::verDetalles: " . $e->getMessage());
            header('Location: /SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&error=' . urlencode('Error al cargar los detalles del préstamo'));
            exit;
        }
    }
}
?>

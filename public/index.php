<?php
// Configurar codificación UTF-8
header('Content-Type: text/html; charset=UTF-8');

// Iniciar sesión al principio
session_start();

// Punto de entrada principal del sistema
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/LibrosController.php';
require_once '../app/controllers/CategoriasController.php';
require_once '../app/controllers/UsuariosController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/PrestamosController.php';
require_once '../app/controllers/ReportesController.php';
require_once '../app/controllers/ExportController.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    // Rutas de autenticación
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'authenticate':
        $controller = new AuthController();
        $controller->authenticate();
        break;
        
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'dashboard':
        // Verificar autenticación antes de acceder al dashboard
        AuthController::verificarAutenticacion();
        $controller = new HomeController();
        $controller->dashboard();
        break;
        
    case 'libros':
        // Verificar autenticación antes de acceder a libros
        AuthController::verificarAutenticacion();
        $controller = new LibrosController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        if ($action === 'agregar') {
            // Solo bibliotecarios pueden agregar libros
            AuthController::verificarRol(1);
            $controller->agregar();
        } elseif ($action === 'guardar') {
            AuthController::verificarRol(1);
            $controller->guardar();
        } elseif ($action === 'editar') {
            AuthController::verificarRol(1);
            $controller->editar();
        } elseif ($action === 'actualizar') {
            AuthController::verificarRol(1);
            $controller->actualizar();
        } elseif ($action === 'eliminar') {
            AuthController::verificarRol(1);
            $controller->eliminar();
        } elseif ($action === 'detalle') {
            // Cualquier usuario autenticado puede ver detalles
            $controller->detalle();
        } elseif ($action === 'subirPDF') {
            // Solo bibliotecarios pueden subir PDFs
            AuthController::verificarRol(1);
            $controller->subirPDF();
        } elseif ($action === 'actualizarPDF') {
            // Solo bibliotecarios pueden actualizar PDFs
            AuthController::verificarRol(1);
            $controller->actualizarPDF();
        } elseif ($action === 'leerPDF') {
            // Cualquier usuario autenticado puede leer PDFs
            $controller->leerPDF();
        } else {
            $controller->index();
        }
        break;
        
    case 'categorias':
        // Verificar autenticación antes de acceder a categorías
        AuthController::verificarAutenticacion();
        $controller = new CategoriasController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        if ($action === 'agregar') {
            // Solo bibliotecarios pueden gestionar categorías
            AuthController::verificarRol(1);
            $controller->agregar();
        } elseif ($action === 'guardar') {
            AuthController::verificarRol(1);
            $controller->guardar();
        } elseif ($action === 'editar') {
            AuthController::verificarRol(1);
            $controller->editar();
        } elseif ($action === 'actualizar') {
            AuthController::verificarRol(1);
            $controller->actualizar();
        } elseif ($action === 'eliminar') {
            AuthController::verificarRol(1);
            $controller->eliminar();
        } else {
            $controller->index();
        }
        break;
        
    case 'usuarios':
        // Verificar autenticación y rol de bibliotecario para gestión de usuarios
        AuthController::verificarRol(1);
        $controller = new UsuariosController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        if ($action === 'agregar') {
            $controller->agregar();
        } elseif ($action === 'guardar') {
            $controller->guardar();
        } elseif ($action === 'editar') {
            $controller->editar();
        } elseif ($action === 'actualizar') {
            $controller->actualizar();
        } elseif ($action === 'eliminar') {
            $controller->eliminar();
        } elseif ($action === 'buscar') {
            $controller->buscar();
        } elseif ($action === 'filtrarPorRol') {
            $controller->filtrarPorRol();
        } elseif ($action === 'activar') {
            $controller->activar();
        } elseif ($action === 'desactivar') {
            $controller->desactivar();
        } else {
            $controller->index();
        }
        break;
        
    case 'prestamos':
        // Verificar autenticación antes de acceder a préstamos
        AuthController::verificarAutenticacion();
        $controller = new PrestamosController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        
        if ($action === 'agregar') {
            // Solo bibliotecarios pueden crear préstamos
            AuthController::verificarRol(1);
            $controller->agregar();
        } elseif ($action === 'guardar') {
            AuthController::verificarRol(1);
            $controller->guardar();
        } elseif ($action === 'editar') {
            AuthController::verificarRol(1);
            $controller->editar();
        } elseif ($action === 'actualizar') {
            AuthController::verificarRol(1);
            $controller->actualizar();
        } elseif ($action === 'eliminar') {
            AuthController::verificarRol(1);
            $controller->eliminar();
        } elseif ($action === 'devolver') {
            AuthController::verificarRol(1);
            $controller->devolver();
        } elseif ($action === 'vencidos') {
            AuthController::verificarRol(1);
            $controller->vencidos();
        } elseif ($action === 'buscar') {
            $controller->buscar();
        } elseif ($action === 'getLibroInfo') {
            $controller->getLibroInfo();
        } elseif ($action === 'solicitar') {
            // Cualquier usuario autenticado (principalmente lectores)
            $controller->solicitar();
        } elseif ($action === 'procesarSolicitud') {
            $controller->procesarSolicitud();
        } elseif ($action === 'misSolicitudes') {
            $controller->misSolicitudes();
        } elseif ($action === 'gestionarSolicitudes') {
            // Solo bibliotecarios pueden gestionar solicitudes
            AuthController::verificarRol(1);
            $controller->gestionarSolicitudes();
        } elseif ($action === 'aprobarSolicitud') {
            AuthController::verificarRol(1);
            $controller->aprobarSolicitud();
        } elseif ($action === 'rechazarSolicitud') {
            AuthController::verificarRol(1);
            $controller->rechazarSolicitud();
        } elseif ($action === 'autodevolverLibro') {
            // Cualquier usuario puede autodevolver sus libros
            $controller->autodevolverLibro();
        } elseif ($action === 'solicitarAmpliacion') {
            // Cualquier usuario puede solicitar ampliaciones
            $controller->solicitarAmpliacion();
        } elseif ($action === 'gestionar') {
            // Solo bibliotecarios pueden gestionar préstamos directos
            AuthController::verificarRol(1);
            $controller->gestionar();
        } elseif ($action === 'procesar_otorgar') {
            AuthController::verificarRol(1);
            $controller->procesar_otorgar();
        } elseif ($action === 'gestionarAmpliaciones') {
            // Solo bibliotecarios pueden gestionar ampliaciones
            AuthController::verificarRol(1);
            $controller->gestionarAmpliaciones();
        } elseif ($action === 'aprobarAmpliacion') {
            AuthController::verificarRol(1);
            $controller->aprobarAmpliacion();
        } elseif ($action === 'rechazarAmpliacion') {
            AuthController::verificarRol(1);
            $controller->rechazarAmpliacion();
        } elseif ($action === 'ampliarDuracionPrestamo') {
            AuthController::verificarRol(1);
            $controller->ampliarDuracionPrestamo();
        } elseif ($action === 'verDetalles') {
            $controller->verDetalles();
        } else {
            $controller->index();
        }
        break;
        
    case 'reportes':
        // Verificar autenticación antes de acceder a reportes
        AuthController::verificarAutenticacion();
        $controller = new ReportesController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        
        if ($action === 'index') {
            $controller->index();
        } elseif ($action === 'prestamos') {
            $controller->prestamos();
        } elseif ($action === 'usuarios') {
            $controller->usuarios();
        } elseif ($action === 'libros') {
            $controller->libros();
        } else {
            $controller->index();
        }
        break;
        
    case 'export':
        // Verificar autenticación antes de acceder a exportaciones
        AuthController::verificarAutenticacion();
        $controller = new ExportController();
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        if ($action === 'excel') {
            $controller->excel();
        } elseif ($action === 'pdf') {
            $controller->pdf();
        } else {
            header('Location: index.php?page=reportes&error=' . urlencode('Acción de exportación no válida'));
            exit;
        }
        break;
        
    case 'home':
    default:
        // Si no está logueado, redirigir al login
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        // Si está logueado, mostrar dashboard
        header('Location: index.php?page=dashboard');
        exit;
}
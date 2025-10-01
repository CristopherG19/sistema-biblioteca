<?php
require_once '../app/models/Libro.php';
require_once '../app/models/Categoria.php';
require_once '../app/models/Prestamo.php';
require_once '../app/models/Historial.php';
require_once '../app/utils/PDFHandler.php';

class LibrosController {
    private $pdfHandler;
    
    public function __construct() {
        $this->pdfHandler = new PDFHandler();
    }
    
    public function index() {
        $libros = Libro::getAll();
        $categorias = Categoria::getAll();
        include __DIR__ . '/../views/libros/index.php';
    }

    public function agregar() {
        $categorias = Categoria::getAll();
        include __DIR__ . '/../views/libros/agregar.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];
            
            // Validar datos básicos
            if (empty($_POST['titulo'])) $errores[] = 'El título es obligatorio';
            if (empty($_POST['autor'])) $errores[] = 'El autor es obligatorio';
            if (empty($_POST['editorial'])) $errores[] = 'La editorial es obligatoria';
            if (empty($_POST['anio'])) $errores[] = 'El año es obligatorio';
            if (empty($_POST['isbn'])) $errores[] = 'El ISBN es obligatorio';
            if (empty($_POST['stock']) || $_POST['stock'] < 1) $errores[] = 'El stock debe ser mayor a 0';
            if (empty($_POST['idCategoria'])) $errores[] = 'La categoría es obligatoria';
            
            if (!empty($errores)) {
                $categorias = Categoria::getAll();
                include __DIR__ . '/../views/libros/agregar.php';
                return;
            }
            
            try {
                // Verificar si el ISBN ya existe
                $libro = new Libro();
                $libroExistente = $libro->obtenerPorIsbn($_POST['isbn']);
                if ($libroExistente) {
                    $categorias = Categoria::getAll();
                    $error = 'Ya existe un libro con este ISBN: ' . $_POST['isbn'];
                    include __DIR__ . '/../views/libros/agregar.php';
                    return;
                }
                
                // Si hay un archivo PDF
                if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Log del archivo recibido
                    error_log("Archivo PDF recibido: " . $_FILES['archivo_pdf']['name'] . ", Tamaño: " . $_FILES['archivo_pdf']['size']);
                    
                    // Primero insertar el libro básico para obtener el ID
                    // Usar INSERT directo en lugar de procedimiento para obtener lastInsertId()
                    $conexion = obtenerConexion();
                    
                    $sql = "INSERT INTO Libros (idCategoria, titulo, autor, editorial, anio, isbn, stock, disponible, descripcion) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($sql);
                    $insertSuccess = $stmt->execute([
                        $_POST['idCategoria'],
                        $_POST['titulo'],
                        $_POST['autor'],
                        $_POST['editorial'],
                        $_POST['anio'],
                        $_POST['isbn'],
                        $_POST['stock'],
                        $_POST['stock'], // disponible igual a stock
                        $_POST['descripcion']
                    ]);
                    
                    if ($insertSuccess) {
                        // Obtener el ID del último libro insertado
                        $idLibro = $conexion->lastInsertId();
                        error_log("Libro insertado con ID: " . $idLibro);
                        
                        // Procesar el PDF
                        $resultadoPDF = $this->pdfHandler->procesarPDF($_FILES['archivo_pdf'], $idLibro);
                        
                        if ($resultadoPDF['exito']) {
                            // Actualizar el libro con la información del PDF
                            $datosActualizacion = [
                                'archivo_pdf' => $resultadoPDF['nombreArchivo'],
                                'numero_paginas' => $resultadoPDF['numeroPaginas'],
                                'tamano_archivo' => $resultadoPDF['tamaño']
                            ];
                            
                            $actualizacionExitosa = Libro::actualizarConPDF($idLibro, $datosActualizacion);
                            
                            if ($actualizacionExitosa) {
                                header('Location: index.php?page=libros&mensaje=' . 
                                       urlencode('Libro agregado exitosamente con archivo PDF'));
                            } else {
                                header('Location: index.php?page=libros&error=' . 
                                       urlencode('Libro agregado pero error al actualizar información del PDF'));
                            }
                        } else {
                            // Si falla el PDF, mantener el libro pero mostrar error
                            header('Location: index.php?page=libros&error=' . 
                                   urlencode('Libro agregado pero error con PDF: ' . $resultadoPDF['mensaje']));
                        }
                    } else {
                        throw new Exception('Error al insertar el libro en la base de datos. Verifique que todos los campos estén completos y sean válidos.');
                    }
                } else {
                    // Libro sin PDF
                    if (Libro::insertar($_POST)) {
                        header('Location: index.php?page=libros&mensaje=' . 
                               urlencode('Libro agregado exitosamente'));
                    } else {
                        throw new Exception('Error al insertar el libro');
                    }
                }
            } catch (Exception $e) {
                error_log("Error en LibrosController::guardar: " . $e->getMessage());
                header('Location: index.php?page=libros&error=' . 
                       urlencode('Error al guardar el libro: ' . $e->getMessage()));
            }
        } else {
            header('Location: index.php?page=libros');
        }
        exit;
    }

    public function editar() {
        $id = $_GET['id'];
        $libro = Libro::getById($id);
        $categorias = Categoria::getAll();
        include __DIR__ . '/../views/libros/editar.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            Libro::actualizar($id, $_POST);
        }
        header('Location: index.php?page=libros');
        exit;
    }

    public function eliminar() {
        $id = $_GET['id'];
        
        try {
            // Obtener información del libro antes de eliminarlo
            $libro = Libro::getById($id);
            
            // Si tiene PDF, eliminarlo del servidor
            if (!empty($libro['archivo_pdf'])) {
                $this->pdfHandler->eliminarPDF($libro['archivo_pdf']);
            }
            
            // Eliminar libro de la base de datos
            if (Libro::eliminar($id)) {
                header('Location: index.php?page=libros&mensaje=' . 
                       urlencode('Libro eliminado exitosamente'));
            } else {
                throw new Exception('Error al eliminar el libro de la base de datos');
            }
        } catch (Exception $e) {
            error_log("Error en LibrosController::eliminar: " . $e->getMessage());
            header('Location: index.php?page=libros&error=' . 
                   urlencode('Error al eliminar el libro: ' . $e->getMessage()));
        }
        exit;
    }
    
    // Nuevo método para mostrar detalles del libro
    public function detalle() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?page=libros&error=' . 
                   urlencode('ID de libro no especificado'));
            exit;
        }
        
        $id = $_GET['id'];
        $libro = Libro::getLibroConDetallePDF($id);
        
        if (!$libro) {
            header('Location: index.php?page=libros&error=' . 
                   urlencode('Libro no encontrado'));
            exit;
        }
        
        // Obtener información adicional del PDF si existe
        if (!empty($libro['archivo_pdf'])) {
            $infoPDF = $this->pdfHandler->obtenerInfoArchivo($libro['archivo_pdf']);
            $libro['info_pdf'] = $infoPDF;
            $libro['url_pdf'] = $this->pdfHandler->obtenerURLArchivo($libro['archivo_pdf']);
        }
        
        // Registrar visualización en el historial (solo para lectores)
        if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2) {
            $historial = new Historial();
            $historial->registrarVisualizacion($_SESSION['usuario_id'], $id, $libro['titulo']);
        }
        
        include __DIR__ . '/../views/libros/detalle.php';
    }

    // Mostrar formulario para subir PDF y procesar la subida (para bibliotecarios)
    public function subirPDF() {
        // Debug crítico del ID
        $id_get = isset($_GET['id']) ? $_GET['id'] : 'NO_SET';
        $id_post = isset($_POST['id']) ? $_POST['id'] : 'NO_SET';
        error_log("CRITICAL DEBUG: GET id = " . $id_get . ", POST id = " . $id_post);
        
        // Aceptar id por GET (cuando se abre la página) o por POST (cuando se envía el formulario)
        $id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);
        
        error_log("CRITICAL DEBUG: ID final seleccionado = " . var_export($id, true));
        
        if (!$id) {
            header('Location: index.php?page=libros&error=' . urlencode('ID de libro no especificado'));
            exit;
        }

        $libro = Libro::getById($id);
        if (!$libro) {
            header('Location: index.php?page=libros&error=' . urlencode('Libro no encontrado'));
            exit;
        }

        // Procesar POST de subida
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_FILES['archivo_pdf']) || $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_NO_FILE) {
                $error = 'No se recibió ningún archivo';
                include __DIR__ . '/../views/libros/subir_pdf.php';
                return;
            }

            $resultadoPDF = $this->pdfHandler->procesarPDF($_FILES['archivo_pdf'], $id);
            if ($resultadoPDF['exito']) {
                $datosActualizacion = [
                    'archivo_pdf' => $resultadoPDF['nombreArchivo'],
                    'numero_paginas' => $resultadoPDF['numeroPaginas'],
                    'tamano_archivo' => $resultadoPDF['tamaño']
                ];

                $actualizacion = Libro::actualizarConPDF($id, $datosActualizacion);
                if ($actualizacion) {
                    header('Location: index.php?page=libros&action=detalle&id=' . $id . '&mensaje=' . urlencode('PDF subido correctamente'));
                } else {
                    header('Location: index.php?page=libros&action=detalle&id=' . $id . '&error=' . urlencode('Error al guardar información del PDF'));
                }
            } else {
                $error = 'Error al procesar PDF: ' . $resultadoPDF['mensaje'];
                include __DIR__ . '/../views/libros/subir_pdf.php';
            }
            return;
        }

        include __DIR__ . '/../views/libros/subir_pdf.php';
    }

    // Actualizar PDF existente (usa la misma vista/flujo que subir)
    public function actualizarPDF() {
        // Reusar la misma lógica
        $this->subirPDF();
    }
    
    // Método para mostrar visor PDF
    public function leerPDF() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?page=libros&error=' . 
                   urlencode('ID de libro no especificado'));
            exit;
        }
        
        $id = $_GET['id'];
        $libro = Libro::getLibroConDetallePDF($id);
        
        if (!$libro || empty($libro['archivo_pdf'])) {
            header('Location: index.php?page=libros&error=' . 
                   urlencode('PDF no encontrado'));
            exit;
        }
        
        $urlPDF = $this->pdfHandler->obtenerURLArchivo($libro['archivo_pdf']);
        
        if (!$urlPDF) {
            header('Location: index.php?page=libros&error=' . 
                   urlencode('El archivo PDF no existe'));
            exit;
        }
        
        // Registrar lectura (opcional - para estadísticas)
        $this->registrarLectura($id, $_SESSION['usuario_id']);
        
        include __DIR__ . '/../views/libros/visor_pdf.php';
    }
    
    private function registrarLectura($idLibro, $idUsuario) {
        try {
            $conexion = obtenerConexion();
            // Usar procedimiento almacenado para registrar lectura
            $stmt = $conexion->prepare("CALL sp_libro_registrar_lectura(?, ?)");
            $stmt->execute([$idLibro, $idUsuario]);
            $stmt->closeCursor(); // Liberar resultado del procedimiento
        } catch (Exception $e) {
            // Si falla, no es crítico, solo log
            error_log("Error al registrar lectura: " . $e->getMessage());
        }
    }
}

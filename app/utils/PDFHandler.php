<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

class PDFHandler {
    private $uploadDir;
    private $maxFileSize;
    private $allowedMimeTypes;
    
    public function __construct() {
        $this->uploadDir = __DIR__ . '/../../public/uploads/libros/';
        $this->maxFileSize = 50 * 1024 * 1024; // 50MB
        $this->allowedMimeTypes = ['application/pdf'];
        
        // Crear directorio si no existe
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Procesar y guardar archivo PDF
     */
    public function procesarPDF($archivo, $libroId) {
        try {
            // Debug crítico
            error_log("CRITICAL DEBUG: procesarPDF recibió libroId = " . var_export($libroId, true) . " (tipo: " . gettype($libroId) . ")");
            
            // Validar archivo
            $validacion = $this->validarArchivo($archivo);
            if (!$validacion['valido']) {
                return ['exito' => false, 'mensaje' => $validacion['mensaje']];
            }
            
            // Generar nombre único para el archivo
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreArchivo = 'libro_' . $libroId . '_' . time() . '.' . $extension;
            
            error_log("CRITICAL DEBUG: nombreArchivo generado = " . $nombreArchivo);
            
            $rutaCompleta = $this->uploadDir . $nombreArchivo;
            
            // Mover archivo al directorio de destino
            if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                return ['exito' => false, 'mensaje' => 'Error al guardar el archivo'];
            }
            
            // Analizar PDF para obtener información
            $infoPDF = $this->analizarPDF($rutaCompleta);
            
            $resultado = [
                'exito' => true,
                'nombreArchivo' => $nombreArchivo,
                'rutaCompleta' => $rutaCompleta,
                'tamaño' => filesize($rutaCompleta),
                'numeroPaginas' => $infoPDF['paginas'],
                'metadatos' => $infoPDF['metadatos'],
                'mensaje' => 'PDF procesado exitosamente'
            ];
            
            return $resultado;
            
        } catch (Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al procesar PDF: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Validar archivo PDF
     */
    private function validarArchivo($archivo) {
        // Verificar que se haya subido correctamente
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['valido' => false, 'mensaje' => 'Error en la subida del archivo'];
        }
        
        // Verificar tamaño
        if ($archivo['size'] > $this->maxFileSize) {
            $tamañoMB = round($this->maxFileSize / (1024 * 1024));
            return ['valido' => false, 'mensaje' => "El archivo es demasiado grande. Máximo: {$tamañoMB}MB"];
        }
        
        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            return ['valido' => false, 'mensaje' => 'Solo se permiten archivos PDF'];
        }
        
        // Verificar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            return ['valido' => false, 'mensaje' => 'El archivo debe tener extensión .pdf'];
        }
        
        return ['valido' => true, 'mensaje' => 'Archivo válido'];
    }
    
    /**
     * Analizar PDF y extraer información
     */
    private function analizarPDF($rutaArchivo) {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($rutaArchivo);
            
            // Obtener número de páginas
            $paginas = count($pdf->getPages());
            
            // Obtener metadatos
            $detalles = $pdf->getDetails();
            $metadatos = [
                'titulo' => $detalles['Title'] ?? '',
                'autor' => $detalles['Author'] ?? '',
                'creador' => $detalles['Creator'] ?? '',
                'fechaCreacion' => $detalles['CreationDate'] ?? '',
                'fechaModificacion' => $detalles['ModDate'] ?? ''
            ];
            
            return [
                'paginas' => $paginas > 0 ? $paginas : $this->contarPaginasAlternativo($rutaArchivo),
                'metadatos' => $metadatos
            ];
            
        } catch (Exception $e) {
            error_log("Error al analizar PDF con parser principal: " . $e->getMessage());
            // Intentar método alternativo para obtener páginas
            $paginasAlternativo = $this->contarPaginasAlternativo($rutaArchivo);
            return [
                'paginas' => $paginasAlternativo,
                'metadatos' => []
            ];
        }
    }
    
    /**
     * Método alternativo para contar páginas usando búsqueda de patrones
     */
    private function contarPaginasAlternativo($rutaArchivo) {
        try {
            $contenido = file_get_contents($rutaArchivo);
            if ($contenido === false) {
                return 1; // Asumir 1 página si no se puede leer
            }
            
            // Buscar patrones de páginas en el PDF
            $patronesPaginas = [
                '/\/Count\s+(\d+)/',
                '/\/N\s+(\d+)/',
                '/\/Type\s*\/Page\s/i'
            ];
            
            foreach ($patronesPaginas as $patron) {
                if (preg_match_all($patron, $contenido, $matches)) {
                    if (isset($matches[1]) && !empty($matches[1])) {
                        // Para patrones que capturan números
                        $paginas = max($matches[1]);
                        if ($paginas > 0) {
                            return (int)$paginas;
                        }
                    } else {
                        // Para el patrón de /Type/Page, contar ocurrencias
                        $paginas = count($matches[0]);
                        if ($paginas > 0) {
                            return $paginas;
                        }
                    }
                }
            }
            
            // Si no se encuentra nada, asumir 1 página
            return 1;
            
        } catch (Exception $e) {
            error_log("Error en método alternativo de páginas: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Eliminar archivo PDF
     */
    public function eliminarPDF($nombreArchivo) {
        $rutaCompleta = $this->uploadDir . $nombreArchivo;
        
        if (file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }
        
        return false;
    }
    
    /**
     * Verificar si un archivo PDF existe
     */
    public function existeArchivo($nombreArchivo) {
        $rutaCompleta = $this->uploadDir . $nombreArchivo;
        return file_exists($rutaCompleta);
    }
    
    /**
     * Obtener URL del archivo PDF
     */
    public function obtenerURLArchivo($nombreArchivo) {
        if ($this->existeArchivo($nombreArchivo)) {
            return '/SISTEMA_BIBLIOTECA/public/uploads/libros/' . $nombreArchivo;
        }
        return null;
    }
    
    /**
     * Obtener información del archivo
     */
    public function obtenerInfoArchivo($nombreArchivo) {
        $rutaCompleta = $this->uploadDir . $nombreArchivo;
        
        if (!file_exists($rutaCompleta)) {
            return null;
        }
        
        return [
            'nombre' => $nombreArchivo,
            'tamaño' => filesize($rutaCompleta),
            'fechaModificacion' => filemtime($rutaCompleta),
            'url' => $this->obtenerURLArchivo($nombreArchivo)
        ];
    }
    
    /**
     * Formatear tamaño de archivo para mostrar
     */
    public static function formatearTamaño($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
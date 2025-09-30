<?php
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/AuthController.php';

class ExportController {
    
    public function __construct() {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        // Solo bibliotecarios pueden exportar
        if (!AuthController::esBibliotecario()) {
            header('Location: index.php?page=dashboard&error=' . urlencode('No tienes permisos para exportar reportes'));
            exit;
        }
    }
    
    public function excel() {
        $tipo = $_GET['tipo'] ?? '';
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        switch ($tipo) {
            case 'usuarios':
                $this->exportarUsuariosExcel();
                break;
            case 'libros':
                $this->exportarLibrosExcel();
                break;
            case 'prestamos':
                $this->exportarPrestamosExcel($fecha_inicio, $fecha_fin);
                break;
            default:
                header('Location: index.php?page=reportes&error=' . urlencode('Tipo de reporte no válido'));
                exit;
        }
    }
    
    public function pdf() {
        $tipo = $_GET['tipo'] ?? '';
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        switch ($tipo) {
            case 'usuarios':
                $this->exportarUsuariosPDF();
                break;
            case 'libros':
                $this->exportarLibrosPDF();
                break;
            case 'prestamos':
                $this->exportarPrestamosPDF($fecha_inicio, $fecha_fin);
                break;
            default:
                header('Location: index.php?page=reportes&error=' . urlencode('Tipo de reporte no válido'));
                exit;
        }
    }
    
    private function exportarUsuariosExcel() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAll();
        
        $filename = 'reporte_usuarios_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // BOM para UTF-8
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Nombre',
            'Email',
            'Rol',
            'Fecha Registro',
            'Último Acceso',
            'Estado'
        ]);
        
        // Datos
        foreach ($usuarios as $usuario) {
            $rol = $usuario['rol'] == 1 ? 'Bibliotecario' : 'Lector';
            $estado = $usuario['activo'] ? 'Activo' : 'Inactivo';
            
            fputcsv($output, [
                $usuario['idUsuario'],
                $usuario['nombre'],
                $usuario['email'],
                $rol,
                date('d/m/Y H:i', strtotime($usuario['fecha_registro'])),
                $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Nunca',
                $estado
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportarLibrosExcel() {
        $libros = Libro::getAll();
        $categorias = Categoria::getAll();
        
        $filename = 'reporte_libros_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // BOM para UTF-8
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Título',
            'Autor',
            'ISBN',
            'Categoría',
            'Editorial',
            'Año',
            'Stock',
            'Disponibles',
            'Fecha Adición'
        ]);
        
        // Datos
        foreach ($libros as $libro) {
            fputcsv($output, [
                $libro['idLibro'],
                $libro['titulo'],
                $libro['autor'],
                $libro['isbn'],
                $libro['categoria_nombre'] ?? 'Sin categoría',
                $libro['editorial'],
                $libro['anio'],
                $libro['stock'],
                $libro['disponible'],
                date('d/m/Y H:i', strtotime($libro['fecha_adicion']))
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportarPrestamosExcel($fecha_inicio, $fecha_fin) {
        $prestamoModel = new Prestamo();
        $prestamos = $prestamoModel->getAll();
        
        // Filtrar por rango de fechas
        $prestamos_filtrados = array_filter($prestamos, function($p) use ($fecha_inicio, $fecha_fin) {
            $fecha_prestamo = strtotime($p['fechaPrestamo']);
            return $fecha_prestamo >= strtotime($fecha_inicio) && $fecha_prestamo <= strtotime($fecha_fin);
        });
        
        $filename = 'reporte_prestamos_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // BOM para UTF-8
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Usuario',
            'Libro',
            'ISBN',
            'Fecha Préstamo',
            'Fecha Devolución Esperada',
            'Fecha Devolución Real',
            'Estado',
            'Días de Retraso'
        ]);
        
        // Datos
        foreach ($prestamos_filtrados as $prestamo) {
            $estado = is_null($prestamo['fechaDevolucionReal']) ? 'Activo' : 'Devuelto';
            $dias_retraso = 0;
            
            if (is_null($prestamo['fechaDevolucionReal'])) {
                $fecha_esperada = strtotime($prestamo['fechaDevolucionEsperada']);
                $hoy = time();
                if ($fecha_esperada < $hoy) {
                    $dias_retraso = floor(($hoy - $fecha_esperada) / (60 * 60 * 24));
                }
            }
            
            fputcsv($output, [
                $prestamo['idPrestamo'],
                $prestamo['usuario_nombre'] ?? 'N/A',
                $prestamo['libro_titulo'] ?? 'N/A',
                $prestamo['libro_isbn'] ?? 'N/A',
                date('d/m/Y H:i', strtotime($prestamo['fechaPrestamo'])),
                date('d/m/Y', strtotime($prestamo['fechaDevolucionEsperada'])),
                $prestamo['fechaDevolucionReal'] ? date('d/m/Y H:i', strtotime($prestamo['fechaDevolucionReal'])) : '-',
                $estado,
                $dias_retraso
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportarUsuariosPDF() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAll();
        
        $html = $this->generarHTMLUsuariosPDF($usuarios);
        $this->generarPDF($html, 'reporte_usuarios_' . date('Y-m-d_H-i-s') . '.pdf');
    }
    
    private function exportarLibrosPDF() {
        $libros = Libro::getAll();
        $categorias = Categoria::getAll();
        
        $html = $this->generarHTMLLibrosPDF($libros, $categorias);
        $this->generarPDF($html, 'reporte_libros_' . date('Y-m-d_H-i-s') . '.pdf');
    }
    
    private function exportarPrestamosPDF($fecha_inicio, $fecha_fin) {
        $prestamoModel = new Prestamo();
        $prestamos = $prestamoModel->getAll();
        
        // Filtrar por rango de fechas
        $prestamos_filtrados = array_filter($prestamos, function($p) use ($fecha_inicio, $fecha_fin) {
            $fecha_prestamo = strtotime($p['fechaPrestamo']);
            return $fecha_prestamo >= strtotime($fecha_inicio) && $fecha_prestamo <= strtotime($fecha_fin);
        });
        
        $html = $this->generarHTMLPrestamosPDF($prestamos_filtrados, $fecha_inicio, $fecha_fin);
        $this->generarPDF($html, 'reporte_prestamos_' . date('Y-m-d_H-i-s') . '.pdf');
    }
    
    private function generarHTMLUsuariosPDF($usuarios) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Usuarios</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #6f42c1; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>REPORTE DE USUARIOS</h1>
                <p>Sistema de Biblioteca - BiblioSys</p>
                <p>Generado el: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Último Acceso</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($usuarios as $usuario) {
            $rol = $usuario['rol'] == 1 ? 'Bibliotecario' : 'Lector';
            $estado = $usuario['activo'] ? 'Activo' : 'Inactivo';
            
            $html .= '
                    <tr>
                        <td>' . $usuario['idUsuario'] . '</td>
                        <td>' . htmlspecialchars($usuario['nombre']) . '</td>
                        <td>' . htmlspecialchars($usuario['email']) . '</td>
                        <td>' . $rol . '</td>
                        <td>' . date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) . '</td>
                        <td>' . ($usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Nunca') . '</td>
                        <td>' . $estado . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <p>Total de usuarios: ' . count($usuarios) . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generarHTMLLibrosPDF($libros, $categorias) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Libros</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #6f42c1; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
                .stats { margin: 20px 0; }
                .stats div { display: inline-block; margin-right: 30px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>REPORTE DE LIBROS</h1>
                <p>Sistema de Biblioteca - BiblioSys</p>
                <p>Generado el: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="stats">
                <div><strong>Total Libros:</strong> ' . count($libros) . '</div>
                <div><strong>Total Categorías:</strong> ' . count($categorias) . '</div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>ISBN</th>
                        <th>Categoría</th>
                        <th>Editorial</th>
                        <th>Año</th>
                        <th>Stock</th>
                        <th>Disponibles</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($libros as $libro) {
            $html .= '
                    <tr>
                        <td>' . $libro['idLibro'] . '</td>
                        <td>' . htmlspecialchars($libro['titulo']) . '</td>
                        <td>' . htmlspecialchars($libro['autor']) . '</td>
                        <td>' . htmlspecialchars($libro['isbn']) . '</td>
                        <td>' . htmlspecialchars($libro['categoria_nombre'] ?? 'Sin categoría') . '</td>
                        <td>' . htmlspecialchars($libro['editorial']) . '</td>
                        <td>' . $libro['anio'] . '</td>
                        <td>' . $libro['stock'] . '</td>
                        <td>' . $libro['disponible'] . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <p>Total de libros: ' . count($libros) . ' | Total de categorías: ' . count($categorias) . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generarHTMLPrestamosPDF($prestamos, $fecha_inicio, $fecha_fin) {
        $total_prestamos = count($prestamos);
        $prestamos_activos = count(array_filter($prestamos, function($p) { 
            return is_null($p['fechaDevolucionReal']); 
        }));
        $prestamos_devueltos = count(array_filter($prestamos, function($p) { 
            return !is_null($p['fechaDevolucionReal']); 
        }));
        $prestamos_vencidos = count(array_filter($prestamos, function($p) { 
            return is_null($p['fechaDevolucionReal']) && 
                   strtotime($p['fechaDevolucionEsperada']) < time(); 
        }));
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Préstamos</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #6f42c1; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
                .stats { margin: 20px 0; }
                .stats div { display: inline-block; margin-right: 30px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>REPORTE DE PRÉSTAMOS</h1>
                <p>Sistema de Biblioteca - BiblioSys</p>
                <p>Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</p>
                <p>Generado el: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="stats">
                <div><strong>Total Préstamos:</strong> ' . $total_prestamos . '</div>
                <div><strong>Activos:</strong> ' . $prestamos_activos . '</div>
                <div><strong>Devueltos:</strong> ' . $prestamos_devueltos . '</div>
                <div><strong>Vencidos:</strong> ' . $prestamos_vencidos . '</div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Libro</th>
                        <th>ISBN</th>
                        <th>Fecha Préstamo</th>
                        <th>Fecha Devolución Esperada</th>
                        <th>Fecha Devolución Real</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($prestamos as $prestamo) {
            $estado = is_null($prestamo['fechaDevolucionReal']) ? 'Activo' : 'Devuelto';
            
            $html .= '
                    <tr>
                        <td>' . $prestamo['idPrestamo'] . '</td>
                        <td>' . htmlspecialchars($prestamo['usuario_nombre'] ?? 'N/A') . '</td>
                        <td>' . htmlspecialchars($prestamo['libro_titulo'] ?? 'N/A') . '</td>
                        <td>' . htmlspecialchars($prestamo['libro_isbn'] ?? 'N/A') . '</td>
                        <td>' . date('d/m/Y H:i', strtotime($prestamo['fechaPrestamo'])) . '</td>
                        <td>' . date('d/m/Y', strtotime($prestamo['fechaDevolucionEsperada'])) . '</td>
                        <td>' . ($prestamo['fechaDevolucionReal'] ? date('d/m/Y H:i', strtotime($prestamo['fechaDevolucionReal'])) : '-') . '</td>
                        <td>' . $estado . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <p>Total de préstamos: ' . $total_prestamos . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generarPDF($html, $filename) {
        // Para una implementación simple, usamos la función de impresión del navegador
        // En producción, se recomienda usar una librería como TCPDF o mPDF
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        
        // Agregar estilos para impresión
        $html = str_replace('<head>', '<head>
            <style>
                @media print {
                    body { margin: 0; }
                    .no-print { display: none !important; }
                    table { page-break-inside: avoid; }
                    thead { display: table-header-group; }
                    tfoot { display: table-footer-group; }
                }
            </style>', $html);
        
        echo $html;
        exit;
    }
}
?>

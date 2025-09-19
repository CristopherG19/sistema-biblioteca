<?php
/**
 * Script de Instalación Rápida de Procedimientos Almacenados Esenciales
 * Sistema de Biblioteca
 */

echo "<h1>⚡ INSTALACIÓN RÁPIDA DE PROCEDIMIENTOS ESENCIALES</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $conexion = obtenerConexion();
    
    echo "<p>🔗 Conexión establecida exitosamente</p>";
    
    // Procedimientos esenciales que necesitamos YA
    $procedimientos = [
        // Para Libros
        "sp_libro_obtener_por_id" => "
            CREATE PROCEDURE sp_libro_obtener_por_id(IN p_id INT)
            BEGIN
                SELECT l.*, c.nombre as categoria 
                FROM Libros l 
                JOIN Categorias c ON l.idCategoria = c.idCategoria 
                WHERE l.idLibro = p_id;
            END",
            
        "sp_libro_obtener_disponibles" => "
            CREATE PROCEDURE sp_libro_obtener_disponibles()
            BEGIN
                SELECT l.*, c.nombre as categoria 
                FROM Libros l 
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
                WHERE l.disponible > 0
                ORDER BY l.titulo;
            END",
            
        "sp_libro_obtener_con_prestamos" => "
            CREATE PROCEDURE sp_libro_obtener_con_prestamos()
            BEGIN
                SELECT l.*, c.nombre as categoria,
                       COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
                       COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
                FROM Libros l 
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
                LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
                GROUP BY l.idLibro, l.titulo, l.autor, l.editorial, l.anio, l.isbn, l.stock, l.disponible, l.descripcion, c.nombre
                ORDER BY l.titulo;
            END",
            
        "sp_libro_registrar_lectura" => "
            CREATE PROCEDURE sp_libro_registrar_lectura(
                IN p_libro_id INT,
                IN p_usuario_id INT
            )
            BEGIN
                INSERT INTO LibrosLecturas (idLibro, idUsuario, fecha_inicio) 
                VALUES (p_libro_id, p_usuario_id, NOW())
                ON DUPLICATE KEY UPDATE 
                    fecha_inicio = NOW(),
                    fecha_fin = NULL;
                
                SELECT ROW_COUNT() as affected_rows;
            END",
            
        // Para Usuarios
        "sp_usuario_obtener_por_id" => "
            CREATE PROCEDURE sp_usuario_obtener_por_id(IN p_id INT)
            BEGIN
                SELECT u.*, r.nombre as rol_nombre 
                FROM Usuarios u 
                INNER JOIN Roles r ON u.rol = r.idRol 
                WHERE u.idUsuario = p_id;
            END",
            
        "sp_usuario_estadisticas" => "
            CREATE PROCEDURE sp_usuario_estadisticas()
            BEGIN
                SELECT 
                    COUNT(*) as total_usuarios,
                    SUM(CASE WHEN rol = 2 THEN 1 ELSE 0 END) as total_lectores,
                    SUM(CASE WHEN rol = 1 THEN 1 ELSE 0 END) as total_bibliotecarios,
                    SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as nuevos_hoy
                FROM Usuarios;
            END",
            
        // Para Préstamos
        "sp_prestamo_obtener_todos" => "
            CREATE PROCEDURE sp_prestamo_obtener_todos()
            BEGIN
                SELECT p.*, 
                       u.nombre as usuario_nombre,
                       u.email as usuario_email,
                       l.titulo as libro_titulo, 
                       l.autor as libro_autor,
                       l.isbn as libro_isbn
                FROM Prestamos p
                INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
                INNER JOIN Libros l ON p.idLibro = l.idLibro
                ORDER BY p.fechaPrestamo DESC;
            END",
            
        "sp_prestamo_obtener_estadisticas" => "
            CREATE PROCEDURE sp_prestamo_obtener_estadisticas()
            BEGIN
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN fechaDevolucionReal IS NULL THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN fechaDevolucionReal IS NOT NULL THEN 1 ELSE 0 END) as devueltos,
                    SUM(CASE WHEN fechaDevolucionReal IS NULL AND fechaDevolucionEsperada < CURDATE() THEN 1 ELSE 0 END) as vencidos
                FROM Prestamos;
            END"
    ];
    
    echo "<h2>📋 Instalando Procedimientos Esenciales</h2>";
    $instalados = 0;
    
    foreach ($procedimientos as $nombre => $sql) {
        try {
            // Eliminar si existe
            $conexion->exec("DROP PROCEDURE IF EXISTS $nombre");
            
            // Crear nuevo
            $conexion->exec($sql);
            
            echo "<p style='color: green;'>✅ $nombre - Instalado</p>";
            $instalados++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ $nombre - Error: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2>🧪 Verificando Instalación</h2>";
    
    // Probar algunos procedimientos
    try {
        $stmt = $conexion->prepare("CALL sp_usuario_estadisticas()");
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        if ($stats) {
            echo "<p style='color: green;'>✅ Procedimiento usuarios funcional - {$stats['total_usuarios']} usuarios</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error probando procedimiento de usuarios: " . $e->getMessage() . "</p>";
    }
    
    try {
        $stmt = $conexion->prepare("CALL sp_libro_obtener_disponibles()");
        $stmt->execute();
        $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        echo "<p style='color: green;'>✅ Procedimiento libros funcional - " . count($libros) . " libros disponibles</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error probando procedimiento de libros: " . $e->getMessage() . "</p>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ INSTALACIÓN COMPLETADA</h3>";
    echo "<p><strong>$instalados procedimientos</strong> instalados exitosamente</p>";
    echo "<p>El sistema ahora debería funcionar sin errores de procedimientos almacenados.</p>";
    echo "<p><strong>URLs para probar:</strong></p>";
    echo "<ul>";
    echo "<li><a href='/SISTEMA_BIBLIOTECA/public/index.php'>🏠 Dashboard Principal</a></li>";
    echo "<li><a href='/SISTEMA_BIBLIOTECA/public/index.php?page=libros'>📚 Gestión de Libros</a></li>";
    echo "<li><a href='/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios'>👥 Gestión de Usuarios</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "<p>Asegúrate de que:</p>";
    echo "<ul>";
    echo "<li>XAMPP esté ejecutándose</li>";
    echo "<li>MySQL esté activo</li>";
    echo "<li>La base de datos 'biblioteca' exista</li>";
    echo "</ul>";
}

?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    background: #f8f9fa;
}
h1, h2 { 
    color: #343a40; 
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
p { 
    margin: 8px 0; 
    line-height: 1.4;
}
ul { line-height: 1.6; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
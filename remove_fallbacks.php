<?php
// Script para eliminar todos los fallbacks del código
echo "<h2>🧹 Eliminando Fallbacks del Código</h2>";

$archivos = [
    'app/models/Usuario.php',
    'app/models/Libro.php',
    'app/models/Categoria.php'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "<h3>📝 Procesando: $archivo</h3>";
        
        $contenido = file_get_contents($archivo);
        $originalLength = strlen($contenido);
        
        // Eliminar bloques try-catch con fallbacks
        $patrones = [
            // Patrón para bloques try-catch con fallback
            '/try\s*\{[^}]*CALL\s+sp_\w+[^}]*\}\s*catch\s*\([^)]*\)\s*\{[^}]*error_log[^}]*fallback[^}]*\}/s',
            // Patrón para comentarios de fallback
            '/\/\/\s*Fallback[^\n]*\n/',
            '/\/\/\s*fallback[^\n]*\n/',
            // Patrón para bloques de consulta directa en catch
            '/catch\s*\([^)]*\)\s*\{[^}]*error_log[^}]*fallback[^}]*try\s*\{[^}]*\}\s*catch[^}]*\}/s'
        ];
        
        foreach ($patrones as $patron) {
            $contenido = preg_replace($patron, '', $contenido);
        }
        
        // Simplificar métodos que solo tienen try-catch
        $contenido = preg_replace('/try\s*\{([^}]+)\}\s*catch\s*\([^)]*\)\s*\{[^}]*error_log[^}]*return[^}]*\}/s', '$1', $contenido);
        
        // Limpiar líneas vacías múltiples
        $contenido = preg_replace('/\n\s*\n\s*\n/', "\n\n", $contenido);
        
        $newLength = strlen($contenido);
        $removed = $originalLength - $newLength;
        
        if ($removed > 0) {
            file_put_contents($archivo, $contenido);
            echo "<p>✅ Eliminados $removed caracteres de fallbacks</p>";
        } else {
            echo "<p>ℹ️ No se encontraron fallbacks para eliminar</p>";
        }
    } else {
        echo "<p>❌ Archivo no encontrado: $archivo</p>";
    }
}

echo "<h3>🎉 Limpieza de fallbacks completada</h3>";
echo "<p>Ahora el código usa únicamente procedimientos almacenados</p>";
?>

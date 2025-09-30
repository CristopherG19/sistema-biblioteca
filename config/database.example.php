<?php
/**
 * Archivo de ejemplo para configuración de base de datos
 * Copia este archivo como 'database.php' y configura los valores apropiados
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');  // Puerto de MySQL
define('DB_NAME', 'biblioteca_db');
define('DB_USER', 'root');
define('DB_PASS', 'tu_contraseña_aqui');  // Cambiar por tu contraseña
define('DB_CHARSET', 'utf8mb4');

/**
 * Función para obtener conexión a la base de datos
 */
function obtenerConexion() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $opciones);
        
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        die("Error de conexión a la base de datos.");
    }
}

/**
 * Función para verificar la conexión a la base de datos
 */
function verificarConexion() {
    try {
        $conexion = obtenerConexion();
        return $conexion !== null;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Función para obtener información de la base de datos
 */
function getInfoBaseDatos() {
    return [
        'host' => DB_HOST,
        'port' => DB_PORT,
        'database' => DB_NAME,
        'charset' => DB_CHARSET
    ];
}

// Configuración de zona horaria para la base de datos
date_default_timezone_set('America/Lima');

// Configuración de sesiones seguras (solo si no hay sesión activa)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
}
?>

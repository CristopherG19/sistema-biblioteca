<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit;
}

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['idLibro']) || !isset($input['tiempo'])) {
    http_response_code(400);
    exit;
}

try {
    $conexion = obtenerConexion();
    
    $stmt = $conexion->prepare("
        UPDATE LibrosLecturas 
        SET fecha_ultima_lectura = NOW(),
            tiempo_lectura_minutos = tiempo_lectura_minutos + ?
        WHERE idLibro = ? AND idUsuario = ?
    ");
    
    $stmt->execute([
        $input['tiempo'],
        $input['idLibro'],
        $_SESSION['usuario_id']
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    error_log("Error al registrar tiempo de lectura: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}
?>
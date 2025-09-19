<?php
require_once __DIR__ . '/../../config/database.php';

class Libro {
    // Obtener todos los libros
    public static function getAll() {
        $conexion = obtenerConexion();
        $stmt = $conexion->query('CALL sp_listar_libros()');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    // Obtener libro por ID
    public static function getById($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_obtener_por_id(?)');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    // Insertar libro
    public static function insertar($datos) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_insertar_libro(?,?,?,?,?,?,?,?,?)');
        return $stmt->execute([
            $datos['idCategoria'],
            $datos['titulo'],
            $datos['autor'],
            $datos['editorial'],
            $datos['anio'],
            $datos['isbn'],
            $datos['stock'],
            $datos['stock'], // disponible igual a stock al crear
            $datos['descripcion']
        ]);
    }
    
    // Insertar libro con PDF
    public static function insertarConPDF($datos) {
        $conexion = obtenerConexion();
        $conexion->beginTransaction();
        
        try {
            // Usar procedimiento para insertar libro básico primero
            $stmt = $conexion->prepare('CALL sp_insertar_libro(?,?,?,?,?,?,?,?,?)');
            $resultado = $stmt->execute([
                $datos['idCategoria'],
                $datos['titulo'],
                $datos['autor'],
                $datos['editorial'],
                $datos['anio'],
                $datos['isbn'],
                $datos['stock'],
                $datos['stock'],
                $datos['descripcion']
            ]);
            
            if (!$resultado) {
                throw new Exception('Error al insertar libro básico');
            }
            
            // Obtener el ID del libro insertado
            $idLibro = $conexion->lastInsertId();
            
            // Actualizar con información del PDF usando procedimiento
            if (!empty($datos['archivo_pdf'])) {
                $stmt = $conexion->prepare('CALL sp_libro_actualizar_pdf(?,?,?,?)');
                $stmt->execute([
                    $idLibro,
                    $datos['archivo_pdf'],
                    $datos['numero_paginas'] ?? null,
                    $datos['tamano_archivo'] ?? null
                ]);
            }
            
            $conexion->commit();
            return $idLibro;
        } catch (Exception $e) {
            $conexion->rollback();
            error_log("Error al insertar libro con PDF: " . $e->getMessage());
            throw $e;
        }
    }

    // Actualizar libro
    public static function actualizar($id, $datos) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_actualizar_libro(?,?,?,?,?,?,?,?,?,?)');
        return $stmt->execute([
            $id,
            $datos['idCategoria'],
            $datos['titulo'],
            $datos['autor'],
            $datos['editorial'],
            $datos['anio'],
            $datos['isbn'],
            $datos['stock'],
            $datos['disponible'],
            $datos['descripcion']
        ]);
    }
    
    // Actualizar parcialmente
    public static function actualizarCampos($id, $campos) {
        $conexion = obtenerConexion();
        
        // Si solo se actualiza PDF, usar procedimiento específico
        if (count($campos) <= 3 && isset($campos['archivo_pdf'])) {
            $stmt = $conexion->prepare('CALL sp_libro_actualizar_pdf(?,?,?,?)');
            return $stmt->execute([
                $id,
                $campos['archivo_pdf'] ?? null,
                $campos['numero_paginas'] ?? null,
                $campos['tamano_archivo'] ?? null
            ]);
        }
        
        // Para otros campos, usar query directa
        $setClauses = [];
        $valores = [];
        
        foreach ($campos as $campo => $valor) {
            $setClauses[] = "$campo = ?";
            $valores[] = $valor;
        }
        $valores[] = $id;
        
        $sql = "UPDATE Libros SET " . implode(', ', $setClauses) . " WHERE idLibro = ?";
        $stmt = $conexion->prepare($sql);
        return $stmt->execute($valores);
    }

    // Actualizar libro con información de PDF
    public static function actualizarConPDF($id, $datosActualizacion) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_actualizar_pdf(?,?,?,?)');
        return $stmt->execute([
            $id,
            $datosActualizacion['archivo_pdf'],
            $datosActualizacion['numero_paginas'],
            $datosActualizacion['tamano_archivo']
        ]);
    }

    // Eliminar libro
    public static function eliminar($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_eliminar_libro(?)');
        return $stmt->execute([$id]);
    }

    // Obtener libros disponibles
    public static function getDisponibles($limite = null) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_obtener_disponibles(?)');
        $stmt->execute([$limite]);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    // Buscar libros
    public static function buscarPorTituloAutor($termino) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_buscar_por_titulo_autor(?)');
        $stmt->execute([$termino]);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    // Actualizar stock después de préstamo
    public static function actualizarStockPrestamo($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_actualizar_stock_prestamo(?)');
        $stmt->execute([$id]);
        $resultado = $stmt->fetch();
        $stmt->closeCursor();
        return $resultado['status'] === 'success';
    }

    // Actualizar stock después de devolución
    public static function actualizarStockDevolucion($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_actualizar_stock_devolucion(?)');
        $stmt->execute([$id]);
        $resultado = $stmt->fetch();
        $stmt->closeCursor();
        return $resultado['status'] === 'success';
    }

    // Obtener libros con información de préstamos
    public static function getConPrestamos() {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_obtener_con_prestamos()');
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }
    
    // Obtener libros recientes
    public static function getRecientes($limite = 5) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_obtener_recientes(?)');
        $stmt->execute([$limite]);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    // Verificar si ISBN existe
    public static function isbnExiste($isbn, $excluir_id = null) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_verificar_isbn_existe(?, ?)');
        $stmt->execute([$isbn, $excluir_id]);
        $resultado = $stmt->fetch();
        $stmt->closeCursor();
        return $resultado['existe'] > 0;
    }

    // Obtener libro por ISBN
    public static function obtenerPorIsbn($isbn) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_obtener_por_isbn(?)');
        $stmt->execute([$isbn]);
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    // Obtener libro con detalle del PDF
    public static function getLibroConDetallePDF($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_obtener_con_detalle_pdf(?)');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    // Registrar lectura de libro
    public static function registrarLectura($libro_id, $usuario_id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('CALL sp_libro_registrar_lectura(?, ?)');
        $stmt->execute([$libro_id, $usuario_id]);
        $resultado = $stmt->fetch();
        $stmt->closeCursor();
        return $resultado['affected_rows'] > 0;
    }
}
?>
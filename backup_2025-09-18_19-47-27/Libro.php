<?php
require_once __DIR__ . '/../../config/database.php';
class Libro {
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query('CALL sp_listar_libros()');
        $result = $stmt->fetchAll();
        $stmt->closeCursor(); // Importante para liberar el resultado del SP
        return $result;
    }

    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT l.*, c.nombre as categoria FROM Libros l JOIN Categorias c ON l.idCategoria = c.idCategoria WHERE l.idLibro = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function insertar($datos) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_insertar_libro(?,?,?,?,?,?,?,?,?)');
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
    
    public static function insertarConPDF($datos) {
        try {
            $conexion = obtenerConexion();
            $conexion->beginTransaction();
            
            // Insertar libro básico
            $stmt = $conexion->prepare('
                INSERT INTO Libros (idCategoria, titulo, autor, editorial, anio, isbn, stock, disponible, descripcion, archivo_pdf, numero_paginas, tamano_archivo, fecha_subida)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $resultado = $stmt->execute([
                $datos['idCategoria'],
                $datos['titulo'],
                $datos['autor'],
                $datos['editorial'],
                $datos['anio'],
                $datos['isbn'],
                $datos['stock'],
                $datos['stock'], // disponible igual a stock al crear
                $datos['descripcion'],
                $datos['archivo_pdf'] ?? null,
                $datos['numero_paginas'] ?? null,
                $datos['tamano_archivo'] ?? null
            ]);
            
            if ($resultado) {
                $idLibro = $conexion->lastInsertId();
                $conexion->commit();
                return $idLibro;
            } else {
                $conexion->rollBack();
                return false;
            }
        } catch (Exception $e) {
            if (isset($conexion)) {
                $conexion->rollBack();
            }
            error_log("Error al insertar libro con PDF: " . $e->getMessage());
            return false;
        }
    }

    public static function actualizar($id, $datos) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_actualizar_libro(?,?,?,?,?,?,?,?,?,?)');
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
    
    public static function actualizarConPDF($id, $datos) {
        try {
            $conexion = obtenerConexion();
            $conexion->beginTransaction();
            
            // Construir la consulta dinámicamente según los datos proporcionados
            $campos = [];
            $valores = [];
            
            foreach (['idCategoria', 'titulo', 'autor', 'editorial', 'anio', 'isbn', 'stock', 'disponible', 'descripcion'] as $campo) {
                if (isset($datos[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $datos[$campo];
                }
            }
            
            // Campos específicos para PDF
            foreach (['archivo_pdf', 'numero_paginas', 'tamano_archivo'] as $campo) {
                if (isset($datos[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $datos[$campo];
                }
            }
            
            // Si se actualiza el PDF, actualizar fecha_subida
            if (isset($datos['archivo_pdf'])) {
                $campos[] = "fecha_subida = NOW()";
            }
            
            if (!empty($campos)) {
                $valores[] = $id;
                $sql = "UPDATE Libros SET " . implode(', ', $campos) . " WHERE idLibro = ?";
                $stmt = $conexion->prepare($sql);
                $resultado = $stmt->execute($valores);
                
                if ($resultado) {
                    $conexion->commit();
                    return true;
                } else {
                    $conexion->rollBack();
                    return false;
                }
            }
            
            return true;
        } catch (Exception $e) {
            if (isset($conexion)) {
                $conexion->rollBack();
            }
            error_log("Error al actualizar libro con PDF: " . $e->getMessage());
            return false;
        }
    }

    public static function eliminar($id) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_eliminar_libro(?)');
        return $stmt->execute([$id]);
    }
    
    public static function getRecientes($limite = 5) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('
            SELECT l.*, c.nombre as categoria 
            FROM Libros l 
            JOIN Categorias c ON l.idCategoria = c.idCategoria 
            ORDER BY l.fecha_adicion DESC 
            LIMIT ?
        ');
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
    
    public static function getDisponibles($limite = 8) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('
            SELECT l.*, c.nombre as categoria 
            FROM Libros l 
            JOIN Categorias c ON l.idCategoria = c.idCategoria 
            WHERE l.disponible > 0 
            ORDER BY l.titulo 
            LIMIT ?
        ');
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }
    
    // Métodos específicos para PDFs
    public static function actualizarPDF($id, $archivoPDF, $numeroPaginas, $tamanoArchivo) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('
            UPDATE Libros 
            SET archivo_pdf = ?, numero_paginas = ?, tamano_archivo = ?, fecha_subida = NOW()
            WHERE idLibro = ?
        ');
        return $stmt->execute([$archivoPDF, $numeroPaginas, $tamanoArchivo, $id]);
    }
    
    public static function eliminarPDF($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('
            UPDATE Libros 
            SET archivo_pdf = NULL, numero_paginas = NULL, tamano_archivo = NULL, fecha_subida = NULL
            WHERE idLibro = ?
        ');
        return $stmt->execute([$id]);
    }
    
    public static function getLibrosConPDF() {
        $conexion = obtenerConexion();
        $stmt = $conexion->query('
            SELECT l.*, c.nombre as categoria 
            FROM Libros l 
            JOIN Categorias c ON l.idCategoria = c.idCategoria 
            WHERE l.archivo_pdf IS NOT NULL 
            ORDER BY l.fecha_subida DESC
        ');
        return $stmt->fetchAll();
    }
    
    public static function getLibroConDetallePDF($id) {
        $conexion = obtenerConexion();
        $stmt = $conexion->prepare('
            SELECT l.*, c.nombre as categoria,
                   CASE 
                       WHEN l.archivo_pdf IS NOT NULL THEN 1 
                       ELSE 0 
                   END as tiene_pdf
            FROM Libros l 
            JOIN Categorias c ON l.idCategoria = c.idCategoria 
            WHERE l.idLibro = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function obtenerPorIsbn($isbn) {
        try {
            $pdo = obtenerConexion();
            $sql = "SELECT * FROM Libros WHERE isbn = :isbn";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar libro por ISBN: " . $e->getMessage());
            return false;
        }
    }
}

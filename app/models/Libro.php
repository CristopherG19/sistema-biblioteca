<?php
require_once __DIR__ . '/../../config/database.php';

class Libro {
    // Obtener todos los libros (usa procedimiento existente)
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->query('CALL sp_listar_libros()');
        $result = $stmt->fetchAll();
        $stmt->closeCursor(); // Importante para liberar el resultado del SP
        return $result;
    }

    // Obtener libro por ID (fallback a consulta directa)
    public static function getById($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_obtener_por_id(?)');
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            error_log("DEBUG LIBRO::getById resultado procedimiento: " . print_r($result, true));
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa si el procedimiento no existe
            error_log("DEBUG LIBRO::getById Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            $stmt = $pdo->prepare('SELECT l.*, c.nombre as categoria FROM Libros l JOIN Categorias c ON l.idCategoria = c.idCategoria WHERE l.idLibro = ?');
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            error_log("DEBUG LIBRO::getById resultado fallback: " . print_r($result, true));
            return $result;
        }
    }

    // Insertar libro (usa procedimiento existente)
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
    
    // Insertar libro con PDF (mejorado para usar transacciones)
    public static function insertarConPDF($datos) {
        try {
            $conexion = obtenerConexion();
            $conexion->beginTransaction();
            
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

    // Actualizar libro (usa procedimiento existente)
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
    
    // Actualizar parcialmente (mejorado con procedimientos)
    public static function actualizarCampos($id, $campos) {
        try {
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
            
            // Para otros campos, usar query directa (pendiente crear SP específico)
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
            
        } catch (Exception $e) {
            error_log("Error al actualizar campos del libro: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar libro con información de PDF (usa procedimiento almacenado)
    public static function actualizarConPDF($id, $datosActualizacion) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_actualizar_pdf(?,?,?,?)');
            return $stmt->execute([
                $id,
                $datosActualizacion['archivo_pdf'],
                $datosActualizacion['numero_paginas'],
                $datosActualizacion['tamano_archivo']
            ]);
        } catch (Exception $e) {
            // Si el procedimiento no existe o falla, intentar UPDATE directo como fallback
            error_log("sp_libro_actualizar_pdf falló, intentando UPDATE directo: " . $e->getMessage());
            try {
                $sql = "UPDATE Libros SET archivo_pdf = ?, numero_paginas = ?, tamano_archivo = ?, fecha_subida = NOW() WHERE idLibro = ?";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([
                    $datosActualizacion['archivo_pdf'],
                    $datosActualizacion['numero_paginas'],
                    $datosActualizacion['tamano_archivo'],
                    $id
                ]);
            } catch (Exception $e2) {
                error_log("Error al actualizar PDF del libro con UPDATE directo: " . $e2->getMessage());
                return false;
            }
        }
    }

    // Eliminar libro (usa procedimiento existente)
    public static function eliminar($id) {
        global $pdo;
        $stmt = $pdo->prepare('CALL sp_eliminar_libro(?)');
        return $stmt->execute([$id]);
    }

    // Obtener libros disponibles (fallback a consulta directa)
    public static function getDisponibles($limite = null) {
        global $pdo;
        try {
            // Intentar usar procedimiento almacenado primero
            $stmt = $pdo->prepare('CALL sp_libro_obtener_disponibles()');
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            
            // Aplicar límite si se especifica
            if ($limite && count($result) > $limite) {
                $result = array_slice($result, 0, $limite);
            }
            
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa si el procedimiento no existe
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $sql = "SELECT l.*, c.nombre as categoria 
                    FROM Libros l 
                    INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
                    WHERE l.disponible > 0
                    ORDER BY l.titulo";
            
            if ($limite) {
                $sql .= " LIMIT " . (int)$limite;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    // Buscar libros (actualizado a procedimiento)
    public static function buscarPorTituloAutor($termino) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_buscar_por_titulo_autor(?)');
            $stmt->execute([$termino]);
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("Error al buscar libros: " . $e->getMessage());
            return [];
        }
    }

    // Actualizar stock después de préstamo (ACTUALIZADO A PROCEDIMIENTO)
    public static function actualizarStockPrestamo($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_actualizar_stock_prestamo(?)');
            $stmt->execute([$id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            return $resultado['status'] === 'success';
        } catch (Exception $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $pdo->prepare("UPDATE Libros SET disponible = disponible - 1 WHERE idLibro = ?");
            return $stmt->execute([$id]);
        }
    }

    // Actualizar stock después de devolución (ACTUALIZADO A PROCEDIMIENTO)
    public static function actualizarStockDevolucion($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_actualizar_stock_devolucion(?)');
            $stmt->execute([$id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            return $resultado['status'] === 'success';
        } catch (Exception $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $pdo->prepare("UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = ?");
            return $stmt->execute([$id]);
        }
    }

    // Obtener libros con información de préstamos (actualizado a procedimiento)
    public static function getConPrestamos() {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_obtener_con_prestamos()');
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $pdo->prepare("
                SELECT l.*, c.nombre as categoria,
                       COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
                       COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
                FROM Libros l 
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
                LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
                GROUP BY l.idLibro
                ORDER BY l.titulo
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }
    
    // Obtener libros recientes (AHORA CON PROCEDIMIENTO)
    public static function getRecientes($limite = 5) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_obtener_recientes(?)');
            $stmt->execute([$limite]);
            $result = $stmt->fetchAll();
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa
            error_log("Procedimiento no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $pdo->prepare("
                SELECT l.*, c.nombre as categoria 
                FROM Libros l 
                INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
                ORDER BY l.idLibro DESC 
                LIMIT ?
            ");
            $stmt->execute([$limite]);
            return $stmt->fetchAll();
        }
    }

    // Verificar si ISBN existe (actualizado a procedimiento)
    public static function isbnExiste($isbn, $excluir_id = null) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_verificar_isbn_existe(?, ?)');
            $stmt->execute([$isbn, $excluir_id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            return $resultado['existe'] > 0;
        } catch (Exception $e) {
            error_log("Error al verificar ISBN: " . $e->getMessage());
            return false;
        }
    }

    // Obtener libro por ISBN (actualizado a procedimiento)
    public static function obtenerPorIsbn($isbn) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_obtener_por_isbn(?)');
            $stmt->execute([$isbn]);
            $result = $stmt->fetch();
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa si el procedimiento no existe
            error_log("Procedimiento sp_libro_obtener_por_isbn no encontrado, usando fallback: " . $e->getMessage());
            
            $stmt = $pdo->prepare('SELECT l.*, c.nombre as categoria FROM Libros l JOIN Categorias c ON l.idCategoria = c.idCategoria WHERE l.isbn = ?');
            $stmt->execute([$isbn]);
            return $stmt->fetch();
        }
    }

    // Obtener libro con detalle del PDF (archivo, paginas, tamano, fecha subida)
    public static function getLibroConDetallePDF($id) {
        global $pdo;
        try {
            // Intentar usar procedimiento almacenado si existe
            $stmt = $pdo->prepare('CALL sp_libro_obtener_con_detalle_pdf(?)');
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            // Fallback a consulta directa
            error_log("Procedimiento sp_libro_obtener_con_detalle_pdf no encontrado, usando fallback: " . $e->getMessage());

            $sql = "SELECT l.*, c.nombre as categoria, ";
            $sql .= "CASE WHEN l.archivo_pdf IS NOT NULL AND l.archivo_pdf <> '' THEN 1 ELSE 0 END as tiene_pdf, ";
            $sql .= "l.archivo_pdf, l.numero_paginas, l.tamano_archivo, l.fecha_subida ";
            $sql .= "FROM Libros l INNER JOIN Categorias c ON l.idCategoria = c.idCategoria WHERE l.idLibro = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
    }

    // Registrar lectura de libro (actualizado a procedimiento) 
    public static function registrarLectura($libro_id, $usuario_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare('CALL sp_libro_registrar_lectura(?, ?)');
            $stmt->execute([$libro_id, $usuario_id]);
            $resultado = $stmt->fetch();
            $stmt->closeCursor();
            return $resultado['affected_rows'] > 0;
        } catch (Exception $e) {
            error_log("Error al registrar lectura: " . $e->getMessage());
            return false;
        }
    }
}
?>
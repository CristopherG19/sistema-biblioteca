<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-book text-primary me-2"></i><?php echo htmlspecialchars($libro['titulo']); ?></h2>
            <p class="text-muted mb-0">Información detallada del libro</p>
        </div>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Catálogo
            </a>
            <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2 && $libro['disponible'] > 0): ?>
                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar&libro_id=<?php echo $libro['idLibro']; ?>" 
                   class="btn btn-success">
                    <i class="fas fa-hand-holding me-2"></i>Solicitar Préstamo
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Información del Libro -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Libro</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-book me-2 text-primary"></i>Título:</td>
                                    <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-user-edit me-2 text-primary"></i>Autor:</td>
                                    <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-building me-2 text-primary"></i>Editorial:</td>
                                    <td><?php echo htmlspecialchars($libro['editorial']); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-calendar me-2 text-primary"></i>Año:</td>
                                    <td><?php echo htmlspecialchars($libro['anio']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-barcode me-2 text-primary"></i>ISBN:</td>
                                    <td><?php echo htmlspecialchars($libro['isbn']); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-tag me-2 text-primary"></i>Categoría:</td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($libro['categoria']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-boxes me-2 text-primary"></i>Stock:</td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $libro['stock']; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="fas fa-check-circle me-2 text-primary"></i>Disponible:</td>
                                    <td>
                                        <?php if ($libro['disponible'] > 0): ?>
                                            <span class="badge bg-success"><?php echo $libro['disponible']; ?> ejemplares</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No disponible</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($libro['descripcion'])): ?>
                        <hr>
                        <h6><i class="fas fa-align-left me-2 text-primary"></i>Descripción</h6>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($libro['descripcion'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Estado del Libro -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Estado</h6>
                </div>
                <div class="card-body text-center">
                    <?php if ($libro['disponible'] > 0): ?>
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">Disponible</h5>
                        <p class="text-muted mb-0">
                            <?php echo $libro['disponible']; ?> de <?php echo $libro['stock']; ?> ejemplares disponibles
                        </p>
                    <?php else: ?>
                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">No Disponible</h5>
                        <p class="text-muted mb-0">Todos los ejemplares están prestados</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Archivo Digital -->
            <?php if ($libro['tiene_pdf'] && !empty($libro['archivo_pdf'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Versión Digital</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-file-pdf fa-4x text-danger"></i>
                        </div>
                        
                        <div class="mb-3">
                            <?php if (!empty($libro['numero_paginas'])): ?>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-file-alt me-2"></i>Páginas:</span>
                                    <strong><?php echo $libro['numero_paginas']; ?></strong>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($libro['tamano_archivo'])): ?>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-weight me-2"></i>Tamaño:</span>
                                    <strong><?php echo PDFHandler::formatearTamaño($libro['tamano_archivo']); ?></strong>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($libro['fecha_subida'])): ?>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-calendar me-2"></i>Subido:</span>
                                    <strong><?php echo date('d/m/Y', strtotime($libro['fecha_subida'])); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php 
                        // Verificar si el lector tiene préstamo activo
                        $puedeAccederPDF = false;
                        if (isset($_SESSION['usuario_rol'])) {
                            if ($_SESSION['usuario_rol'] == 1) {
                                // Bibliotecarios siempre pueden acceder
                                $puedeAccederPDF = true;
                            } elseif ($_SESSION['usuario_rol'] == 2) {
                                // Lectores solo si tienen préstamo activo
                                $puedeAccederPDF = Prestamo::tienePrestamoActivo($_SESSION['usuario_id'], $libro['idLibro']);
                            }
                        }
                        ?>
                        
                        <div class="d-grid gap-2">
                            <?php if ($puedeAccederPDF): ?>
                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=leerPDF&id=<?php echo $libro['idLibro']; ?>" 
                                   class="btn btn-danger" target="_blank">
                                    <i class="fas fa-eye me-2"></i>Leer Online
                                </a>
                                <a href="<?php echo $libro['url_pdf']; ?>" 
                                   class="btn btn-outline-danger" 
                                   download="<?php echo htmlspecialchars($libro['titulo'] . '.pdf'); ?>">
                                    <i class="fas fa-download me-2"></i>Descargar PDF
                                </a>
                            <?php else: ?>
                                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2): ?>
                                    <div class="alert alert-warning text-center mb-3">
                                        <i class="fas fa-lock me-2"></i>
                                        <strong>Acceso Restringido</strong><br>
                                        <small>Necesitas solicitar un préstamo para acceder al contenido digital</small>
                                    </div>
                                    <?php if ($libro['disponible'] > 0): ?>
                                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar&libro_id=<?php echo $libro['idLibro']; ?>" 
                                           class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-hand-holding me-2"></i>Solicitar Préstamo
                                        </a>
                                    <?php else: ?>
                                        <div class="alert alert-danger text-center">
                                            <i class="fas fa-times-circle me-2"></i>
                                            <strong>No Disponible</strong><br>
                                            <small>Todos los ejemplares están prestados</small>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        <small>Inicia sesión para acceder al contenido</small>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-file-excel fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Sin Versión Digital</h6>
                        <p class="text-muted small mb-0">
                            Este libro solo está disponible en formato físico
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Acciones para Bibliotecarios -->
            <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning">
                        <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Administración</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=editar&id=<?php echo $libro['idLibro']; ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar Libro
                            </a>
                            
                            <?php if (!$libro['tiene_pdf']): ?>
                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=subirPDF&id=<?php echo $libro['idLibro']; ?>" 
                                   class="btn btn-outline-success">
                                    <i class="fas fa-upload me-2"></i>Subir PDF
                                </a>
                            <?php else: ?>
                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=actualizarPDF&id=<?php echo $libro['idLibro']; ?>" 
                                   class="btn btn-outline-info">
                                    <i class="fas fa-sync me-2"></i>Actualizar PDF
                                </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-danger" 
                                    onclick="confirmarEliminacion(<?php echo $libro['idLibro']; ?>)">
                                <i class="fas fa-trash me-2"></i>Eliminar Libro
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(libroId) {
    if (confirm('¿Estás seguro de que quieres eliminar este libro? Esta acción no se puede deshacer.')) {
        window.location.href = `/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=eliminar&id=${libroId}`;
    }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
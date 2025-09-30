<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-hand-holding text-success me-2"></i>Otorgar Préstamo</h2>
            <p class="text-muted mb-0">Gestionar préstamo directo del libro</p>
        </div>
        <a href="index.php?page=libros" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al Catálogo
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Información del Libro -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Libro Seleccionado</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="text-center">
                                <i class="fas fa-book fa-4x text-info mb-2"></i>
                                <?php if (!empty($libro['archivo_pdf'])): ?>
                                    <div><i class="fas fa-file-pdf text-danger"></i> PDF</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-9">
                            <h5 class="card-title"><?php echo htmlspecialchars($libro['titulo']); ?></h5>
                            <p class="card-text">
                                <strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?><br>
                                <strong>Editorial:</strong> <?php echo htmlspecialchars($libro['editorial']); ?><br>
                                <strong>ISBN:</strong> <?php echo htmlspecialchars($libro['isbn']); ?><br>
                                <strong>Categoría:</strong> 
                                <span class="badge bg-info"><?php echo htmlspecialchars($libro['categoria_nombre']); ?></span>
                            </p>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-primary"><?php echo $libro['stock']; ?></div>
                                        <small class="text-muted">Stock Total</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-success"><?php echo $libro['disponible']; ?></div>
                                        <small class="text-muted">Disponibles</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-warning"><?php echo $libro['stock'] - $libro['disponible']; ?></div>
                                        <small class="text-muted">Prestados</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Préstamo -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Datos del Préstamo</h5>
                </div>
                <div class="card-body">
                    <?php if ($libro['disponible'] > 0): ?>
                        <form method="POST" action="index.php?page=prestamos&action=procesar_otorgar">
                            <input type="hidden" name="idLibro" value="<?php echo $libro['idLibro']; ?>">
                            
                            <div class="mb-3">
                                <label for="idUsuario" class="form-label">
                                    <i class="fas fa-user me-1"></i>Seleccionar Usuario
                                </label>
                                <select class="form-select" id="idUsuario" name="idUsuario" required>
                                    <option value="">Seleccione un lector...</option>
                                    <?php foreach ($lectores as $lector): ?>
                                        <option value="<?php echo $lector['idUsuario']; ?>">
                                            <?php echo htmlspecialchars($lector['nombreCompleto'] . ' - ' . $lector['email']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>Solo se muestran usuarios con rol de lector
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="fechaPrestamo" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Fecha de Préstamo
                                </label>
                                <input type="date" class="form-control" id="fechaPrestamo" name="fechaPrestamo" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="fechaVencimiento" class="form-label">
                                    <i class="fas fa-calendar-times me-1"></i>Fecha de Vencimiento
                                </label>
                                <input type="date" class="form-control" id="fechaVencimiento" name="fechaVencimiento" 
                                       value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                                <div class="form-text">
                                    <i class="fas fa-clock me-1"></i>Por defecto se establecen 14 días
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observaciones" class="form-label">
                                    <i class="fas fa-comment me-1"></i>Observaciones (Opcional)
                                </label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                          placeholder="Notas adicionales sobre el préstamo..."></textarea>
                            </div>

                            <hr>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Otorgar Préstamo
                                </button>
                                <a href="index.php?page=libros" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning">No Disponible</h5>
                            <p class="text-muted">
                                Este libro no tiene ejemplares disponibles para préstamo.
                            </p>
                            <a href="index.php?page=libros" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Catálogo
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaPrestamo = document.getElementById('fechaPrestamo');
    const fechaVencimiento = document.getElementById('fechaVencimiento');
    
    // Actualizar fecha de vencimiento cuando cambia la fecha de préstamo
    fechaPrestamo.addEventListener('change', function() {
        const fechaInicio = new Date(this.value);
        const fechaFin = new Date(fechaInicio);
        fechaFin.setDate(fechaFin.getDate() + 14);
        
        fechaVencimiento.value = fechaFin.toISOString().split('T')[0];
    });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

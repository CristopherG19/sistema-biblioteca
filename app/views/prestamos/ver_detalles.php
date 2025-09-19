<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-eye text-primary me-2"></i>
                Detalles del Préstamo
            </h2>
            <p class="text-muted mb-0">Información completa del préstamo #<?php echo htmlspecialchars($prestamo['idPrestamo']); ?></p>
        </div>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Solicitudes
            </a>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i>Ver Todos los Préstamos
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información del Préstamo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 text-dark fw-bold">
                        <i class="fas fa-book-reader text-primary me-2"></i>
                        Información del Préstamo
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Usuario -->
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-user text-primary fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-muted">Usuario</h6>
                                    <h5 class="mb-0 text-dark fw-bold">
                                        <?php echo htmlspecialchars($usuario['nombre'] ?? 'N/A'); ?>
                                        <?php if (!empty($usuario['apellido'])): ?>
                                            <?php echo htmlspecialchars($usuario['apellido']); ?>
                                        <?php endif; ?>
                                    </h5>
                                    <small class="text-muted"><?php echo htmlspecialchars($usuario['email'] ?? ''); ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Libro -->
                        <div class="col-md-6 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-lg bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-book text-info fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-muted">Libro</h6>
                                    <h5 class="mb-0 text-dark fw-bold">
                                        <?php echo htmlspecialchars($libro['titulo'] ?? 'N/A'); ?>
                                    </h5>
                                    <small class="text-muted"><?php echo htmlspecialchars($libro['autor'] ?? ''); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fechas -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        Fechas del Préstamo
                                    </h6>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Fecha de Préstamo</small>
                                        <strong class="text-dark">
                                            <?php echo date('d/m/Y', strtotime($prestamo['fechaPrestamo'])); ?>
                                        </strong>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Fecha de Devolución Esperada</small>
                                        <strong class="text-dark">
                                            <?php echo date('d/m/Y', strtotime($prestamo['fechaDevolucionEsperada'])); ?>
                                        </strong>
                                    </div>
                                    <?php if (!empty($prestamo['fechaDevolucionReal'])): ?>
                                    <div>
                                        <small class="text-muted d-block">Fecha de Devolución Real</small>
                                        <strong class="text-success">
                                            <?php echo date('d/m/Y', strtotime($prestamo['fechaDevolucionReal'])); ?>
                                        </strong>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Estado y Duración -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        Estado y Duración
                                    </h6>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Estado Actual</small>
                                        <?php 
                                        $estado = 'Activo';
                                        $badgeClass = 'bg-success';
                                        
                                        if (!empty($prestamo['fechaDevolucionReal'])) {
                                            $estado = 'Devuelto';
                                            $badgeClass = 'bg-info';
                                        } elseif (strtotime($prestamo['fechaDevolucionEsperada']) < time()) {
                                            $estado = 'Vencido';
                                            $badgeClass = 'bg-danger';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 fs-6">
                                            <?php echo $estado; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Duración del Préstamo</small>
                                        <strong class="text-dark">
                                            <?php 
                                            // Calcular duración desde fecha de préstamo hasta fecha actual
                                            $fecha_prestamo = new DateTime($prestamo['fechaPrestamo']);
                                            $fecha_actual = new DateTime();
                                            $dias_prestamo = $fecha_actual->diff($fecha_prestamo)->days;
                                            
                                            // Si el préstamo ya fue devuelto, calcular hasta la fecha de devolución
                                            if (!empty($prestamo['fechaDevolucionReal'])) {
                                                $fecha_devolucion = new DateTime($prestamo['fechaDevolucionReal']);
                                                $dias_prestamo = $fecha_devolucion->diff($fecha_prestamo)->days;
                                            }
                                            
                                            echo $dias_prestamo . ' día' . ($dias_prestamo != 1 ? 's' : '');
                                            ?>
                                        </strong>
                                        <small class="text-muted d-block mt-1">
                                            <?php 
                                            $fecha_devolucion_esperada = new DateTime($prestamo['fechaDevolucionEsperada']);
                                            $duracion_total = $fecha_devolucion_esperada->diff($fecha_prestamo)->days;
                                            echo "Duración total: " . $duracion_total . " días";
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <?php if (!empty($prestamo['observaciones'])): ?>
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-comment text-primary me-2"></i>
                            Observaciones
                        </h6>
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($prestamo['observaciones'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Estado del Préstamo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info bg-opacity-10 border-0 py-3">
                    <h6 class="card-title mb-0 text-info fw-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Estado del Préstamo
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-<?php echo $estado === 'Devuelto' ? 'check-circle' : ($estado === 'Vencido' ? 'exclamation-triangle' : 'clock'); ?> fa-3x text-muted"></i>
                    </div>
                    <span class="badge <?php echo $badgeClass; ?> px-3 py-2 fs-6">
                        <?php echo $estado; ?>
                    </span>
                    
                    <?php if ($estado === 'Devuelto'): ?>
                        <div class="mt-3">
                            <small class="text-muted">Devuelto el:</small><br>
                            <strong><?php echo date('d/m/Y', strtotime($prestamo['fechaDevolucionReal'])); ?></strong>
                        </div>
                    <?php elseif ($estado === 'Vencido'): ?>
                        <div class="mt-3">
                            <small class="text-danger">Vencido hace:</small><br>
                            <strong class="text-danger">
                                <?php 
                                $dias_vencido = ceil((time() - strtotime($prestamo['fechaDevolucionEsperada'])) / (60 * 60 * 24));
                                echo $dias_vencido . ' día' . ($dias_vencido != 1 ? 's' : '');
                                ?>
                            </strong>
                        </div>
                    <?php else: ?>
                        <div class="mt-3">
                            <small class="text-muted">Días restantes:</small><br>
                            <strong>
                                <?php 
                                $dias_restantes = ceil((strtotime($prestamo['fechaDevolucionEsperada']) - time()) / (60 * 60 * 24));
                                if ($dias_restantes > 0) {
                                    echo $dias_restantes . ' día' . ($dias_restantes != 1 ? 's' : '');
                                } else {
                                    echo 'Hoy vence';
                                }
                                ?>
                            </strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                    <h6 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-database me-2"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">ID del Préstamo:</small><br>
                        <strong>#<?php echo htmlspecialchars($prestamo['idPrestamo']); ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Fecha de Registro:</small><br>
                        <strong><?php echo date('d/m/Y H:i', strtotime($prestamo['fechaPrestamo'])); ?></strong>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted">Última Actualización:</small><br>
                        <strong><?php echo date('d/m/Y H:i', strtotime($prestamo['fechaPrestamo'])); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
                    <h6 class="card-title mb-0 text-warning fw-bold">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($estado === 'Activo'): ?>
                            <button type="button" 
                                    class="btn btn-outline-warning btn-sm" 
                                    onclick="ampliarDuracion(<?php echo $prestamo['idPrestamo']; ?>, '<?php echo htmlspecialchars($libro['titulo'] ?? 'N/A'); ?>', '<?php echo htmlspecialchars($usuario['nombre'] ?? 'N/A'); ?>')">
                                <i class="fas fa-clock me-2"></i>Ampliar Duración
                            </button>
                        <?php endif; ?>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" 
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-2"></i>Ver Todos los Préstamos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ampliar duración (reutilizado) -->
<div class="modal fade" id="modalAmpliarDuracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Ampliar Duración del Préstamo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=ampliarDuracionPrestamo">
                <div class="modal-body">
                    <input type="hidden" id="ampliarPrestamoId" name="prestamo_id">
                    
                    <div id="ampliarInfo" class="mb-3"></div>
                    
                    <div class="alert alert-info border-0 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Al ampliar la duración, se extenderá la fecha de devolución esperada del préstamo.
                    </div>
                    
                    <div class="mb-3">
                        <label for="diasAmpliacion" class="form-label">Días adicionales</label>
                        <select class="form-select" id="diasAmpliacion" name="dias_adicionales" required>
                            <option value="">Selecciona los días adicionales</option>
                            <option value="7">7 días</option>
                            <option value="14">14 días</option>
                            <option value="21">21 días</option>
                            <option value="30">30 días</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivoAmpliacion" class="form-label">Motivo de la ampliación</label>
                        <textarea class="form-control" 
                                  id="motivoAmpliacion" 
                                  name="motivo" 
                                  rows="3" 
                                  placeholder="Explica el motivo de la ampliación..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-clock me-2"></i>Ampliar Duración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para ampliar duración del préstamo
function ampliarDuracion(prestamoId, libroTitulo, usuarioNombre) {
    document.getElementById('ampliarPrestamoId').value = prestamoId;
    document.getElementById('ampliarInfo').innerHTML = `
        <div class="card border-0 bg-warning bg-opacity-10">
            <div class="card-body">
                <h6><i class="fas fa-user me-2"></i>Usuario: ${usuarioNombre}</h6>
                <h6><i class="fas fa-book me-2"></i>Libro: ${libroTitulo}</h6>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalAmpliarDuracion'));
    modal.show();
}
</script>

<style>
.avatar-lg {
    width: 60px;
    height: 60px;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    font-size: 1rem;
    font-weight: 500;
}
</style>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

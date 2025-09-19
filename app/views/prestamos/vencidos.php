<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título Principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                Préstamos Vencidos
            </h2>
            <p class="text-muted mb-0">Libros que deben ser devueltos con urgencia</p>
        </div>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=agregar" 
               class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Préstamo
            </a>
        </div>
    </div>

    <!-- Mostrar mensajes -->
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Estadísticas de Vencimientos -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo count($prestamos); ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Préstamos Vencidos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-calendar-times fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php 
                        $vencidosHoy = array_filter($prestamos, function($p) {
                            return date('Y-m-d', strtotime($p['fechaDevolucionEsperada'])) == date('Y-m-d');
                        });
                        echo count($vencidosHoy);
                        ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Vencen Hoy</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php 
                        $vencidosUltimaSemana = array_filter($prestamos, function($p) {
                            $diasVencido = (time() - strtotime($p['fechaDevolucionEsperada'])) / (60 * 60 * 24);
                            return $diasVencido <= 7;
                        });
                        echo count($vencidosUltimaSemana);
                        ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Última Semana</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-secondary mb-2">
                        <i class="fas fa-hourglass-end fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php 
                        $vencidosMasUnMes = array_filter($prestamos, function($p) {
                            $diasVencido = (time() - strtotime($p['fechaDevolucionEsperada'])) / (60 * 60 * 24);
                            return $diasVencido > 30;
                        });
                        echo count($vencidosMasUnMes);
                        ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Más de 30 días</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta informativa -->
    <?php if (!empty($prestamos)): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle fa-lg"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="alert-heading mb-1">Atención Requerida</h6>
                    <p class="mb-2">Se encontraron <?php echo count($prestamos); ?> préstamos vencidos que requieren seguimiento inmediato.</p>
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        Considera contactar a los usuarios para programar la devolución o renovación de los libros.
                    </small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista de Préstamos Vencidos -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-dark fw-bold">
                    <i class="fas fa-list me-2 text-danger"></i>
                    Préstamos Vencidos
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="imprimirReporte()">
                        <i class="fas fa-print me-1"></i>Imprimir
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="exportarExcel()">
                        <i class="fas fa-file-excel me-1"></i>Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($prestamos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaVencidos">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted fw-semibold py-3">#</th>
                                <th class="border-0 text-muted fw-semibold py-3">Usuario</th>
                                <th class="border-0 text-muted fw-semibold py-3">Libro</th>
                                <th class="border-0 text-muted fw-semibold py-3">F. Préstamo</th>
                                <th class="border-0 text-muted fw-semibold py-3">F. Vencimiento</th>
                                <th class="border-0 text-muted fw-semibold py-3">Días Vencido</th>
                                <th class="border-0 text-muted fw-semibold py-3">Contacto</th>
                                <th class="border-0 text-muted fw-semibold py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestamos as $index => $prestamo): ?>
                                <?php 
                                $diasVencido = ceil((time() - strtotime($prestamo['fechaDevolucionEsperada'])) / (60 * 60 * 24));
                                $urgenciaClass = '';
                                $urgenciaIcon = '';
                                
                                if ($diasVencido <= 3) {
                                    $urgenciaClass = 'table-warning';
                                    $urgenciaIcon = 'text-warning';
                                } elseif ($diasVencido <= 7) {
                                    $urgenciaClass = 'table-danger';
                                    $urgenciaIcon = 'text-danger';
                                } else {
                                    $urgenciaClass = 'table-dark';
                                    $urgenciaIcon = 'text-dark';
                                }
                                ?>
                                <tr class="<?php echo $urgenciaClass; ?>">
                                    <td class="py-3">
                                        <span class="fw-semibold"><?php echo $prestamo['idPrestamo']; ?></span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($prestamo['usuario_nombre'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($prestamo['usuario_email'] ?? ''); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-book text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($prestamo['libro_titulo'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($prestamo['libro_autor'] ?? ''); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?php echo date('d/m/Y', strtotime($prestamo['fechaPrestamo'])); ?>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?php echo date('d/m/Y', strtotime($prestamo['fechaDevolucionEsperada'])); ?>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle <?php echo $urgenciaIcon; ?> me-2"></i>
                                            <span class="fw-bold <?php echo $urgenciaIcon; ?>">
                                                <?php echo $diasVencido; ?> día<?php echo $diasVencido != 1 ? 's' : ''; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex gap-1">
                                            <a href="tel:<?php echo htmlspecialchars($prestamo['usuario_telefono'] ?? ''); ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Llamar">
                                                <i class="fas fa-phone"></i>
                                            </a>
                                            <a href="mailto:<?php echo htmlspecialchars($prestamo['usuario_email'] ?? ''); ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Email">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-success" 
                                                    title="Registrar Devolución"
                                                    onclick="registrarDevolucion(<?php echo $prestamo['idPrestamo']; ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            
                                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=editar&id=<?php echo $prestamo['idPrestamo']; ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    title="Extender Plazo"
                                                    onclick="extenderPlazo(<?php echo $prestamo['idPrestamo']; ?>)">
                                                <i class="fas fa-calendar-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h5 class="text-success">¡Excelente!</h5>
                    <p class="text-muted mb-0">No hay préstamos vencidos en este momento</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para registrar devolución -->
<div class="modal fade" id="modalDevolucion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Registrar Devolución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDevolucion" method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=devolver">
                <div class="modal-body">
                    <input type="hidden" id="devolucionPrestamoId" name="id">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Este préstamo está vencido. La devolución se registrará como tardía.
                    </div>
                    <div class="mb-3">
                        <label for="observacionesDevolucion" class="form-label">Observaciones</label>
                        <textarea class="form-control" 
                                  id="observacionesDevolucion" 
                                  name="observaciones_devolucion" 
                                  rows="3" 
                                  placeholder="Observaciones sobre la devolución tardía..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Registrar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para extender plazo -->
<div class="modal fade" id="modalExtender" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus text-warning me-2"></i>
                    Extender Plazo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formExtender" method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=actualizar">
                <div class="modal-body">
                    <input type="hidden" id="extenderPrestamoId" name="id">
                    <div class="mb-3">
                        <label for="nuevaFechaDevolucion" class="form-label">Nueva Fecha de Devolución</label>
                        <input type="date" 
                               class="form-control" 
                               id="nuevaFechaDevolucion" 
                               name="fecha_devolucion_esperada" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="motivoExtension" class="form-label">Motivo de la Extensión</label>
                        <textarea class="form-control" 
                                  id="motivoExtension" 
                                  name="observaciones" 
                                  rows="3" 
                                  placeholder="Motivo por el cual se extiende el plazo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-plus me-2"></i>Extender Plazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para registrar devolución
function registrarDevolucion(prestamoId) {
    document.getElementById('devolucionPrestamoId').value = prestamoId;
    const modal = new bootstrap.Modal(document.getElementById('modalDevolucion'));
    modal.show();
}

// Función para extender plazo
function extenderPlazo(prestamoId) {
    document.getElementById('extenderPrestamoId').value = prestamoId;
    
    // Configurar fecha mínima (mañana)
    const mañana = new Date();
    mañana.setDate(mañana.getDate() + 1);
    document.getElementById('nuevaFechaDevolucion').min = mañana.toISOString().split('T')[0];
    
    // Sugerir fecha (15 días desde hoy)
    const fechaSugerida = new Date();
    fechaSugerida.setDate(fechaSugerida.getDate() + 15);
    document.getElementById('nuevaFechaDevolucion').value = fechaSugerida.toISOString().split('T')[0];
    
    const modal = new bootstrap.Modal(document.getElementById('modalExtender'));
    modal.show();
}

// Función para imprimir reporte
function imprimirReporte() {
    window.print();
}

// Función para exportar a Excel (simulada)
function exportarExcel() {
    alert('Funcionalidad de exportar a Excel pendiente de implementar');
}

// Efectos hover para las filas
document.querySelectorAll('#tablaVencidos tbody tr').forEach(fila => {
    fila.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.01)';
        this.style.transition = 'all 0.2s ease';
    });
    
    fila.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: scale(1.05);
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.table-dark {
    background-color: rgba(33, 37, 41, 0.1) !important;
}

@media print {
    .btn, .modal, .alert {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
}
</style>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
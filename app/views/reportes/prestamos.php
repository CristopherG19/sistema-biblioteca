<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-handshake text-primary me-2"></i>
                Reporte de Préstamos
            </h2>
            <p class="text-muted mb-0">Análisis detallado de préstamos por período</p>
        </div>
        <div>
            <a href="index.php?page=reportes" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <input type="hidden" name="page" value="reportes">
                <input type="hidden" name="action" value="prestamos">
                
                <div class="col-md-4 mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?php echo htmlspecialchars($reportePrestamos['fecha_inicio']); ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                           value="<?php echo htmlspecialchars($reportePrestamos['fecha_fin']); ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                    <a href="index.php?page=reportes&action=prestamos" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-refresh me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas del Período -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-handshake text-primary"></i>
                    </div>
                    <h4 class="text-primary mb-0"><?php echo $reportePrestamos['total_prestamos']; ?></h4>
                    <small class="text-muted">Total Préstamos</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-check text-success"></i>
                    </div>
                    <h4 class="text-success mb-0"><?php echo $reportePrestamos['prestamos_devueltos']; ?></h4>
                    <small class="text-muted">Devueltos</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <h4 class="text-warning mb-0"><?php echo $reportePrestamos['prestamos_activos']; ?></h4>
                    <small class="text-muted">Activos</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                    <h4 class="text-danger mb-0"><?php echo $reportePrestamos['prestamos_vencidos']; ?></h4>
                    <small class="text-muted">Vencidos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Préstamos -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0 py-3">
            <h6 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-list me-2"></i>
                Detalle de Préstamos
                <span class="badge bg-primary ms-2"><?php echo count($reportePrestamos['prestamos']); ?> registros</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($reportePrestamos['prestamos'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">#</th>
                                <th class="py-3">Usuario</th>
                                <th class="py-3">Libro</th>
                                <th class="py-3">Fecha Préstamo</th>
                                <th class="py-3">Fecha Devolución Esperada</th>
                                <th class="py-3">Fecha Devolución Real</th>
                                <th class="py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportePrestamos['prestamos'] as $index => $prestamo): ?>
                                <tr>
                                    <td class="py-3"><?php echo $index + 1; ?></td>
                                    <td class="py-3">
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($prestamo['usuario_nombre'] ?? 'N/A'); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($prestamo['usuario_email'] ?? 'N/A'); ?></small>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($prestamo['libro_titulo'] ?? 'N/A'); ?></div>
                                            <small class="text-muted">ISBN: <?php echo htmlspecialchars($prestamo['libro_isbn'] ?? 'N/A'); ?></small>
                                        </div>
                                    </td>
                                    <td class="py-3"><?php echo date('d/m/Y', strtotime($prestamo['fechaPrestamo'])); ?></td>
                                    <td class="py-3"><?php echo date('d/m/Y', strtotime($prestamo['fechaDevolucionEsperada'])); ?></td>
                                    <td class="py-3">
                                        <?php if ($prestamo['fechaDevolucionReal']): ?>
                                            <?php echo date('d/m/Y', strtotime($prestamo['fechaDevolucionReal'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3">
                                        <?php
                                        $estado = 'Activo';
                                        if ($prestamo['fechaDevolucionReal']) {
                                            $estado = 'Devuelto';
                                        } elseif (strtotime($prestamo['fechaDevolucionEsperada']) < time()) {
                                            $estado = 'Vencido';
                                        }
                                        
                                        $badgeClass = '';
                                        switch($estado) {
                                            case 'Activo':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'Devuelto':
                                                $badgeClass = 'bg-primary';
                                                break;
                                            case 'Vencido':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No hay préstamos en el período seleccionado</h5>
                    <p class="text-muted">Intenta con un rango de fechas diferente</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4 no-print">
        <div class="col-12 text-center">
            <button class="btn btn-success me-2" onclick="imprimirReporte()">
                <i class="fas fa-print me-2"></i>Imprimir Reporte
            </button>
            <a href="index.php?page=export&action=excel&tipo=prestamos&fecha_inicio=<?php echo urlencode($reportePrestamos['fecha_inicio']); ?>&fecha_fin=<?php echo urlencode($reportePrestamos['fecha_fin']); ?>" class="btn btn-primary me-2">
                <i class="fas fa-file-excel me-2"></i>Exportar Excel
            </a>
            <a href="index.php?page=export&action=pdf&tipo=prestamos&fecha_inicio=<?php echo urlencode($reportePrestamos['fecha_inicio']); ?>&fecha_fin=<?php echo urlencode($reportePrestamos['fecha_fin']); ?>" class="btn btn-info" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Exportar PDF
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { margin: 0; padding: 20px; }
    .container { max-width: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table { font-size: 12px; }
    .btn { display: none !important; }
    .navbar { display: none !important; }
    .footer { display: none !important; }
    .card-header { background-color: #f8f9fa !important; }
    .badge { border: 1px solid #000 !important; }
}
</style>

<script>
function imprimirReporte() {
    // Ocultar elementos no necesarios para impresión
    const elementosNoImprimir = document.querySelectorAll('.no-print');
    elementosNoImprimir.forEach(el => el.style.display = 'none');
    
    // Imprimir
    window.print();
    
    // Restaurar elementos
    elementosNoImprimir.forEach(el => el.style.display = '');
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

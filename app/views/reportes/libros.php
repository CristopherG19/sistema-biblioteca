<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-book text-warning me-2"></i>
                Reporte de Libros
            </h2>
            <p class="text-muted mb-0">Análisis del catálogo y categorías</p>
        </div>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=reportes" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
            </a>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-book text-warning"></i>
                    </div>
                    <h4 class="text-warning mb-0"><?php echo $reporteLibros['total_libros']; ?></h4>
                    <small class="text-muted">Total Libros</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-tags text-info"></i>
                    </div>
                    <h4 class="text-info mb-0"><?php echo $reporteLibros['total_categorias']; ?></h4>
                    <small class="text-muted">Categorías</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Libros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-0 py-3">
            <h6 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-list me-2"></i>
                Lista de Libros
                <span class="badge bg-warning ms-2"><?php echo count($reporteLibros['libros']); ?> registros</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($reporteLibros['libros'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">#</th>
                                <th class="py-3">Título</th>
                                <th class="py-3">Autor</th>
                                <th class="py-3">ISBN</th>
                                <th class="py-3">Categoría</th>
                                <th class="py-3">Disponibles</th>
                                <th class="py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reporteLibros['libros'] as $index => $libro): ?>
                                <tr>
                                    <td class="py-3"><?php echo $index + 1; ?></td>
                                    <td class="py-3">
                                        <div class="fw-bold"><?php echo htmlspecialchars($libro['titulo']); ?></div>
                                    </td>
                                    <td class="py-3"><?php echo htmlspecialchars($libro['autor']); ?></td>
                                    <td class="py-3"><?php echo htmlspecialchars($libro['isbn']); ?></td>
                                    <td class="py-3"><?php echo htmlspecialchars($libro['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                                    <td class="py-3">
                                        <span class="badge bg-success"><?php echo $libro['disponibles']; ?></span>
                                    </td>
                                    <td class="py-3">
                                        <?php
                                        $estado = $libro['disponibles'] > 0 ? 'Disponible' : 'No disponible';
                                        $badgeClass = $libro['disponibles'] > 0 ? 'bg-success' : 'bg-danger';
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
                    <h5 class="text-muted">No hay libros registrados</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla de Categorías -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0 py-3">
            <h6 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-tags me-2"></i>
                Categorías
                <span class="badge bg-info ms-2"><?php echo count($reporteLibros['categorias']); ?> registros</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($reporteLibros['categorias'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">#</th>
                                <th class="py-3">Nombre</th>
                                <th class="py-3">Descripción</th>
                                <th class="py-3">Fecha Creación</th>
                                <th class="py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reporteLibros['categorias'] as $index => $categoria): ?>
                                <tr>
                                    <td class="py-3"><?php echo $index + 1; ?></td>
                                    <td class="py-3">
                                        <div class="fw-bold"><?php echo htmlspecialchars($categoria['nombre']); ?></div>
                                    </td>
                                    <td class="py-3"><?php echo htmlspecialchars($categoria['descripcion'] ?? 'Sin descripción'); ?></td>
                                    <td class="py-3"><?php echo date('d/m/Y', strtotime($categoria['fechaCreacion'])); ?></td>
                                    <td class="py-3">
                                        <span class="badge bg-success">Activa</span>
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
                    <h5 class="text-muted">No hay categorías registradas</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <button class="btn btn-success me-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir Reporte
            </button>
            <button class="btn btn-primary me-2" onclick="exportarExcel()">
                <i class="fas fa-file-excel me-2"></i>Exportar Excel
            </button>
            <button class="btn btn-info" onclick="exportarPDF()">
                <i class="fas fa-file-pdf me-2"></i>Exportar PDF
            </button>
        </div>
    </div>
</div>

<script>
function exportarExcel() {
    alert('Función de exportación a Excel - Próximamente');
}

function exportarPDF() {
    alert('Función de exportación a PDF - Próximamente');
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

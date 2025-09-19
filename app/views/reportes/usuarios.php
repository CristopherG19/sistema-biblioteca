<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-users text-success me-2"></i>
                Reporte de Usuarios
            </h2>
            <p class="text-muted mb-0">Estadísticas de usuarios y actividad</p>
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
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-users text-success"></i>
                    </div>
                    <h4 class="text-success mb-0"><?php echo $reporteUsuarios['total_usuarios']; ?></h4>
                    <small class="text-muted">Total Usuarios</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-check text-primary"></i>
                    </div>
                    <h4 class="text-primary mb-0"><?php echo $reporteUsuarios['usuarios_activos']; ?></h4>
                    <small class="text-muted">Usuarios Activos (Lectores)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0 py-3">
            <h6 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-list me-2"></i>
                Lista de Usuarios
                <span class="badge bg-success ms-2"><?php echo count($reporteUsuarios['usuarios']); ?> registros</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($reporteUsuarios['usuarios'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">#</th>
                                <th class="py-3">Nombre</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Rol</th>
                                <th class="py-3">Fecha Registro</th>
                                <th class="py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reporteUsuarios['usuarios'] as $index => $usuario): ?>
                                <tr>
                                    <td class="py-3"><?php echo $index + 1; ?></td>
                                    <td class="py-3">
                                        <div class="fw-bold"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></div>
                                    </td>
                                    <td class="py-3"><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td class="py-3">
                                        <?php
                                        $rol = $usuario['rol'] == 1 ? 'Bibliotecario' : 'Lector';
                                        $badgeClass = $usuario['rol'] == 1 ? 'bg-warning' : 'bg-info';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $rol; ?></span>
                                    </td>
                                    <td class="py-3"><?php echo date('d/m/Y', strtotime($usuario['fechaRegistro'])); ?></td>
                                    <td class="py-3">
                                        <span class="badge bg-success">Activo</span>
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
                    <h5 class="text-muted">No hay usuarios registrados</h5>
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

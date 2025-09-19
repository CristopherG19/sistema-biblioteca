<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Reportes y Estadísticas
            </h2>
            <p class="text-muted mb-0">Análisis detallado del sistema bibliotecario</p>
        </div>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=dashboard" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Resumen General
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-book text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Total Libros</h6>
                                    <h4 class="mb-0 text-primary"><?php echo $estadisticas['libros']['total'] ?? 0; ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-users text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Total Usuarios</h6>
                                    <h4 class="mb-0 text-success"><?php echo $estadisticas['usuarios']['total'] ?? 0; ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-handshake text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Total Préstamos</h6>
                                    <h4 class="mb-0 text-warning"><?php echo $estadisticas['prestamos']['total'] ?? 0; ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-tags text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Categorías</h6>
                                    <h4 class="mb-0 text-info"><?php echo $estadisticas['categorias']['total'] ?? 0; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes Disponibles -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-dark fw-bold mb-3">
                <i class="fas fa-file-alt me-2"></i>
                Reportes Disponibles
            </h5>
        </div>
    </div>

    <div class="row">
        <!-- Reporte de Préstamos -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-handshake text-primary fa-2x"></i>
                        </div>
                        <h5 class="card-title">Reporte de Préstamos</h5>
                        <p class="card-text text-muted">Análisis detallado de préstamos por período</p>
                    </div>
                    <div class="d-grid">
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=reportes&action=prestamos" 
                           class="btn btn-primary">
                            <i class="fas fa-chart-line me-2"></i>Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Usuarios -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-users text-success fa-2x"></i>
                        </div>
                        <h5 class="card-title">Reporte de Usuarios</h5>
                        <p class="card-text text-muted">Estadísticas de usuarios y actividad</p>
                    </div>
                    <div class="d-grid">
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=reportes&action=usuarios" 
                           class="btn btn-success">
                            <i class="fas fa-user-chart me-2"></i>Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Libros -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-book text-warning fa-2x"></i>
                        </div>
                        <h5 class="card-title">Reporte de Libros</h5>
                        <p class="card-text text-muted">Análisis del catálogo y categorías</p>
                    </div>
                    <div class="d-grid">
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=reportes&action=libros" 
                           class="btn btn-warning">
                            <i class="fas fa-book-open me-2"></i>Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Visualizaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info bg-opacity-10 border-0 py-3">
                    <h5 class="card-title mb-0 text-info fw-bold">
                        <i class="fas fa-chart-pie me-2"></i>
                        Visualizaciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Gráfico de Préstamos por Mes -->
                        <div class="col-lg-6 mb-4">
                            <h6 class="text-muted mb-3">Préstamos por Mes (Últimos 6 meses)</h6>
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Gráfico de barras interactivo</p>
                                <small class="text-muted">Próximamente con Chart.js</small>
                            </div>
                        </div>

                        <!-- Gráfico de Libros Más Prestados -->
                        <div class="col-lg-6 mb-4">
                            <h6 class="text-muted mb-3">Libros Más Prestados</h6>
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Gráfico circular interactivo</p>
                                <small class="text-muted">Próximamente con Chart.js</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

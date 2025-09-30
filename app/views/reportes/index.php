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
            <a href="index.php?page=dashboard" class="btn btn-outline-secondary me-2">
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
                        <a href="index.php?page=reportes&action=prestamos" 
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
                        <a href="index.php?page=reportes&action=usuarios" 
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
                        <a href="index.php?page=reportes&action=libros" 
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
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="prestamosChart"></canvas>
                            </div>
                        </div>

                        <!-- Gráfico de Libros Más Prestados -->
                        <div class="col-lg-6 mb-4">
                            <h6 class="text-muted mb-3">Libros Más Prestados</h6>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="librosChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráfico de Usuarios Más Activos -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Usuarios Más Activos</h6>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="usuariosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Datos para los gráficos (estos vendrían del controlador)
const prestamosPorMes = <?php echo json_encode($datosGraficos['prestamos_por_mes'] ?? []); ?>;
const librosMasPrestados = <?php echo json_encode($datosGraficos['libros_mas_prestados'] ?? []); ?>;
const usuariosMasActivos = <?php echo json_encode($datosGraficos['usuarios_mas_activos'] ?? []); ?>;

// Configuración común para todos los gráficos
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#6c757d';

// Gráfico de Préstamos por Mes
const prestamosCtx = document.getElementById('prestamosChart').getContext('2d');
new Chart(prestamosCtx, {
    type: 'bar',
    data: {
        labels: prestamosPorMes.map(item => item.mes),
        datasets: [{
            label: 'Préstamos',
            data: prestamosPorMes.map(item => item.prestamos),
            backgroundColor: [
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(246, 194, 62, 0.8)',
                'rgba(231, 74, 59, 0.8)',
                'rgba(54, 185, 204, 0.8)',
                'rgba(111, 66, 193, 0.8)'
            ],
            borderColor: [
                'rgba(78, 115, 223, 1)',
                'rgba(28, 200, 138, 1)',
                'rgba(246, 194, 62, 1)',
                'rgba(231, 74, 59, 1)',
                'rgba(54, 185, 204, 1)',
                'rgba(111, 66, 193, 1)'
            ],
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#dee2e6',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        return context.parsed.y + ' préstamos';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    }
});

// Gráfico de Libros Más Prestados
const librosCtx = document.getElementById('librosChart').getContext('2d');
new Chart(librosCtx, {
    type: 'doughnut',
    data: {
        labels: librosMasPrestados.map(item => item.titulo),
        datasets: [{
            data: librosMasPrestados.map(item => item.prestamos),
            backgroundColor: [
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(246, 194, 62, 0.8)',
                'rgba(231, 74, 59, 0.8)',
                'rgba(54, 185, 204, 0.8)'
            ],
            borderColor: [
                'rgba(78, 115, 223, 1)',
                'rgba(28, 200, 138, 1)',
                'rgba(246, 194, 62, 1)',
                'rgba(231, 74, 59, 1)',
                'rgba(54, 185, 204, 1)'
            ],
            borderWidth: 2,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#dee2e6',
                borderWidth: 1,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} préstamos (${percentage}%)`;
                    }
                }
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    }
});

// Gráfico de Usuarios Más Activos
const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
new Chart(usuariosCtx, {
    type: 'horizontalBar',
    data: {
        labels: usuariosMasActivos.map(item => item.nombre),
        datasets: [{
            label: 'Préstamos',
            data: usuariosMasActivos.map(item => item.prestamos),
            backgroundColor: [
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(246, 194, 62, 0.8)',
                'rgba(231, 74, 59, 0.8)',
                'rgba(54, 185, 204, 0.8)'
            ],
            borderColor: [
                'rgba(78, 115, 223, 1)',
                'rgba(28, 200, 138, 1)',
                'rgba(246, 194, 62, 1)',
                'rgba(231, 74, 59, 1)',
                'rgba(54, 185, 204, 1)'
            ],
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#dee2e6',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        return context.parsed.x + ' préstamos';
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    stepSize: 1
                }
            },
            y: {
                grid: {
                    display: false
                }
            }
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart'
        }
    }
});
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

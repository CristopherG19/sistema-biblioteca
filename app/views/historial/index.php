<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header de la página -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-history text-primary me-2"></i>
                        Mi Historial
                    </h2>
                    <p class="text-muted mb-0">Registro de todas tus actividades en el sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="actualizarHistorial()">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                    </button>
                    <a href="index.php?page=historial&action=exportar&tipo=<?php echo $tipo_filtro; ?>" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Exportar
                    </a>
                </div>
            </div>

            <!-- Mensajes -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['mensaje']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['total_actividades']; ?></h4>
                            <p class="text-muted mb-0">Total Actividades</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fas fa-book-reader fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['total_prestamos']; ?></h4>
                            <p class="text-muted mb-0">Préstamos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="fas fa-undo fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['total_devoluciones']; ?></h4>
                            <p class="text-muted mb-0">Devoluciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['total_visualizaciones']; ?></h4>
                            <p class="text-muted mb-0">Visualizaciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-secondary mb-2">
                                <i class="fas fa-search fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['total_busquedas']; ?></h4>
                            <p class="text-muted mb-0">Búsquedas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-danger mb-2">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h4 class="mb-1">
                                <?php 
                                echo $estadisticas['ultima_actividad'] 
                                    ? date('d/m', strtotime($estadisticas['ultima_actividad'])) 
                                    : 'N/A'; 
                                ?>
                            </h4>
                            <p class="text-muted mb-0">Última Actividad</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">Filtrar por tipo de actividad:</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <a href="index.php?page=historial&tipo=todos" 
                                   class="btn <?php echo $tipo_filtro === 'todos' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    Todos
                                </a>
                                <a href="index.php?page=historial&tipo=prestamo" 
                                   class="btn <?php echo $tipo_filtro === 'prestamo' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    Préstamos
                                </a>
                                <a href="index.php?page=historial&tipo=visualizacion" 
                                   class="btn <?php echo $tipo_filtro === 'visualizacion' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    Visualizaciones
                                </a>
                                <a href="index.php?page=historial&tipo=busqueda" 
                                   class="btn <?php echo $tipo_filtro === 'busqueda' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    Búsquedas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Historial -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="card-title mb-0 text-dark fw-bold">
                        <i class="fas fa-list me-2"></i>
                        Actividades Recientes
                        <span class="badge bg-primary ms-2"><?php echo count($historial); ?> registros</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($historial)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3">Fecha</th>
                                        <th class="py-3">Tipo</th>
                                        <th class="py-3">Descripción</th>
                                        <th class="py-3">Libro</th>
                                        <th class="py-3">Autor</th>
                                        <th class="py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historial as $actividad): ?>
                                        <tr>
                                            <td class="py-3">
                                                <div>
                                                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($actividad['fecha_actividad'])); ?></div>
                                                    <small class="text-muted"><?php echo date('H:i', strtotime($actividad['fecha_actividad'])); ?></small>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <?php
                                                $iconos = [
                                                    'prestamo' => 'fas fa-book-reader text-success',
                                                    'devolucion' => 'fas fa-undo text-info',
                                                    'solicitud' => 'fas fa-hand-paper text-warning',
                                                    'visualizacion' => 'fas fa-eye text-primary',
                                                    'busqueda' => 'fas fa-search text-secondary'
                                                ];
                                                $icono = $iconos[$actividad['tipo_actividad']] ?? 'fas fa-circle text-muted';
                                                ?>
                                                <span class="badge bg-light text-dark">
                                                    <i class="<?php echo $icono; ?> me-1"></i>
                                                    <?php echo ucfirst($actividad['tipo_actividad']); ?>
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <div class="text-dark"><?php echo htmlspecialchars($actividad['descripcion']); ?></div>
                                            </td>
                                            <td class="py-3">
                                                <?php if ($actividad['libro_titulo']): ?>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($actividad['libro_titulo']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($actividad['libro_isbn']); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <?php echo $actividad['libro_autor'] ? htmlspecialchars($actividad['libro_autor']) : '-'; ?>
                                            </td>
                                            <td class="py-3">
                                                <?php if ($actividad['libro_titulo'] && isset($actividad['idLibro'])): ?>
                                                    <a href="index.php?page=libros&action=detalle&id=<?php echo $actividad['idLibro']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver Libro">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-history fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No hay actividades registradas</h5>
                            <p class="text-muted">Tu historial de actividades aparecerá aquí</p>
                            <a href="index.php?page=libros" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Explorar Libros
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Paginación (si es necesaria) -->
            <?php if (count($historial) >= $limite): ?>
                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-outline-primary" onclick="cargarMas()">
                        <i class="fas fa-plus me-2"></i>Cargar Más Actividades
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function actualizarHistorial() {
    location.reload();
}

function cargarMas() {
    const limiteActual = <?php echo $limite; ?>;
    const nuevoLimite = limiteActual + 20;
    window.location.href = `index.php?page=historial&tipo=<?php echo $tipo_filtro; ?>&limite=${nuevoLimite}`;
}

// Función para registrar actividad (usada desde otras páginas)
function registrarActividad(tipo, descripcion, libroId = null) {
    fetch('index.php?page=historial&action=registrar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `tipo=${tipo}&descripcion=${descripcion}&libro_id=${libroId}`
    })
    .catch(error => console.error('Error al registrar actividad:', error));
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

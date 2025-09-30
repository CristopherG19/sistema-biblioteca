<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título Principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-list-alt text-primary me-2"></i>
                Mis Solicitudes de Préstamo
            </h2>
            <p class="text-muted mb-0">Revisa el estado de tus solicitudes de préstamo</p>
        </div>
        <div>
            <a href="index.php?page=prestamos&action=solicitar" 
               class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nueva Solicitud
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

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-2">Filtrar por estado:</h6>
                    <div class="btn-group" role="group">
                        <a href="index.php?page=prestamos&action=misSolicitudes" 
                           class="btn btn-outline-primary <?php echo !isset($_GET['estado']) ? 'active' : ''; ?>">
                            Todas
                        </a>
                        <a href="index.php?page=prestamos&action=misSolicitudes&estado=Pendiente" 
                           class="btn btn-outline-warning <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'active' : ''; ?>">
                            Pendientes
                        </a>
                        <a href="index.php?page=prestamos&action=misSolicitudes&estado=Aprobada" 
                           class="btn btn-outline-success <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Aprobada') ? 'active' : ''; ?>">
                            Aprobadas
                        </a>
                        <a href="index.php?page=prestamos&action=misSolicitudes&estado=Convertida" 
                           class="btn btn-outline-info <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Convertida') ? 'active' : ''; ?>">
                            Convertidas
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Última actualización: <?php echo date('d/m/Y H:i'); ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-list fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['total']; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Total</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['pendientes']; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-check fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['aprobadas']; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Aprobadas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['convertidas']; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Convertidas a Préstamo</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-times fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['rechazadas']; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Rechazadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Solicitudes -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-history me-2 text-primary"></i>
                Historial de Solicitudes
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($solicitudes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted fw-semibold py-3">#</th>
                                <th class="border-0 text-muted fw-semibold py-3">Libro</th>
                                <th class="border-0 text-muted fw-semibold py-3">Fecha Solicitud</th>
                                <th class="border-0 text-muted fw-semibold py-3">Estado</th>
                                <th class="border-0 text-muted fw-semibold py-3">Respuesta</th>
                                <th class="border-0 text-muted fw-semibold py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <?php 
                                $badgeClass = '';
                                switch($solicitud['estado']) {
                                    case 'Pendiente':
                                        $badgeClass = 'bg-warning';
                                        break;
                                    case 'Aprobada':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'Rechazada':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'Convertida':
                                        $badgeClass = 'bg-info';
                                        break;
                                    default:
                                        $badgeClass = 'bg-secondary';
                                        break;
                                }
                                
                                $iconClass = '';
                                switch($solicitud['estado']) {
                                    case 'Pendiente':
                                        $iconClass = 'fas fa-clock';
                                        break;
                                    case 'Aprobada':
                                        $iconClass = 'fas fa-check';
                                        break;
                                    case 'Rechazada':
                                        $iconClass = 'fas fa-times';
                                        break;
                                    case 'Convertida':
                                        $iconClass = 'fas fa-exchange-alt';
                                        break;
                                    default:
                                        $iconClass = 'fas fa-question';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td class="py-3 text-muted">
                                        <?php echo $solicitud['idSolicitud']; ?>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-book text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($solicitud['libro_titulo']); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($solicitud['libro_autor']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                            <i class="<?php echo $iconClass; ?> me-1"></i>
                                            <?php echo $solicitud['estado']; ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php if (!empty($solicitud['fecha_respuesta'])): ?>
                                            <div class="small text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_respuesta'])); ?>
                                            </div>
                                            <?php if (!empty($solicitud['bibliotecario_nombre'])): ?>
                                                <div class="small text-muted">
                                                    Por: <?php echo htmlspecialchars($solicitud['bibliotecario_nombre'] . (!empty($solicitud['bibliotecario_apellido']) ? ' ' . $solicitud['bibliotecario_apellido'] : '')); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    title="Ver detalles"
                                                    onclick="verDetalles(<?php echo htmlspecialchars(json_encode($solicitud)); ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <?php if ($solicitud['estado'] === 'Pendiente'): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Cancelar solicitud"
                                                        onclick="cancelarSolicitud(<?php echo $solicitud['idSolicitud']; ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($solicitud['estado'] === 'Convertida' && !empty($solicitud['prestamo_id'])): ?>
                                                <a href="index.php?page=prestamos&action=verDetalles&id=<?php echo $solicitud['prestamo_id']; ?>" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   title="Ver detalles del préstamo">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif; ?>
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
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No tienes solicitudes de préstamo</h5>
                    <p class="text-muted mb-3">Comienza solicitando tu primer libro</p>
                    <a href="index.php?page=prestamos&action=solicitar" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Solicitar Préstamo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Detalles de la Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetallesContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para ver detalles de la solicitud
function verDetalles(solicitud) {
    const estadoBadge = {
        'Pendiente': 'bg-warning',
        'Aprobada': 'bg-success',
        'Rechazada': 'bg-danger',
        'Convertida': 'bg-info'
    };
    
    const fechaRespuesta = solicitud.fecha_respuesta ? 
        new Date(solicitud.fecha_respuesta).toLocaleString('es-ES') : 
        'Pendiente de respuesta';
    
    const bibliotecario = solicitud.bibliotecario_nombre ? 
        `${solicitud.bibliotecario_nombre}${solicitud.bibliotecario_apellido ? ' ' + solicitud.bibliotecario_apellido : ''}` : 
        'No asignado';
    
    document.getElementById('modalDetallesContent').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 bg-light mb-3">
                    <div class="card-header bg-transparent">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-book text-info me-2"></i>
                            Información del Libro
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Título:</strong><br>${solicitud.libro_titulo}</p>
                        <p><strong>Autor:</strong><br>${solicitud.libro_autor}</p>
                        <p><strong>Categoría:</strong><br>${solicitud.categoria_nombre}</p>
                        <p class="mb-0"><strong>Disponibles:</strong><br>
                            <span class="badge bg-success">${solicitud.libro_disponible} ejemplares</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 bg-light mb-3">
                    <div class="card-header bg-transparent">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Estado de la Solicitud
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Estado:</strong><br>
                            <span class="badge ${estadoBadge[solicitud.estado]} px-2 py-1">${solicitud.estado}</span>
                        </p>
                        <p><strong>Fecha de solicitud:</strong><br>
                            ${new Date(solicitud.fecha_solicitud).toLocaleString('es-ES')}
                        </p>
                        <p><strong>Fecha de respuesta:</strong><br>${fechaRespuesta}</p>
                        <p class="mb-0"><strong>Bibliotecario:</strong><br>${bibliotecario}</p>
                    </div>
                </div>
            </div>
        </div>
        
        ${solicitud.observaciones_usuario ? `
            <div class="card border-0 bg-light mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-comment text-success me-2"></i>
                        Tus comentarios
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">${solicitud.observaciones_usuario}</p>
                </div>
            </div>
        ` : ''}
        
        ${solicitud.observaciones_bibliotecario ? `
            <div class="card border-0 bg-light">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user-tie text-warning me-2"></i>
                        Comentarios del bibliotecario
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">${solicitud.observaciones_bibliotecario}</p>
                </div>
            </div>
        ` : ''}
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    modal.show();
}

// Función para cancelar solicitud
function cancelarSolicitud(solicitudId) {
    if (confirm('¿Estás seguro de que quieres cancelar esta solicitud?')) {
        window.location.href = `index.php?page=prestamos&action=cancelarSolicitud&id=${solicitudId}`;
    }
}
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
    transition: background-color 0.2s ease;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

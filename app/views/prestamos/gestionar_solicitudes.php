<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título Principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-tasks text-primary me-2"></i>
                Gestionar Solicitudes de Préstamo
            </h2>
            <p class="text-muted mb-0">Revisa y gestiona las solicitudes de préstamo de los usuarios</p>
        </div>
        <div>
            <a href="index.php?page=prestamos&action=agregar" 
               class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Préstamo Directo
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

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-list fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['total_solicitudes']; ?>
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
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['convertidas']; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Convertidas</p>
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

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <a href="index.php?page=prestamos&action=gestionarSolicitudes" 
                           class="btn btn-outline-primary <?php echo (!isset($_GET['estado'])) ? 'active' : ''; ?>">
                            Todas
                        </a>
                        <a href="index.php?page=prestamos&action=gestionarSolicitudes&estado=Pendiente" 
                           class="btn btn-outline-warning <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'active' : ''; ?>">
                            Pendientes
                        </a>
                        <a href="index.php?page=prestamos&action=gestionarSolicitudes&estado=Convertida" 
                           class="btn btn-outline-info <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Convertida') ? 'active' : ''; ?>">
                            Convertidas
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Última actualización: <?php echo date('d/m/Y H:i'); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Solicitudes -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-inbox me-2 text-primary"></i>
                Solicitudes de Préstamo
                <?php if (isset($_GET['estado'])): ?>
                    <span class="badge bg-secondary ms-2"><?php echo ucfirst($_GET['estado']); ?></span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($solicitudes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted fw-semibold py-3">#</th>
                                <th class="border-0 text-muted fw-semibold py-3">Usuario</th>
                                <th class="border-0 text-muted fw-semibold py-3">Libro</th>
                                <th class="border-0 text-muted fw-semibold py-3">Fecha</th>
                                <th class="border-0 text-muted fw-semibold py-3">Estado</th>
                                <th class="border-0 text-muted fw-semibold py-3">Disponible</th>
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
                                
                                $fechaSolicitud = strtotime($solicitud['fecha_solicitud']);
                                $fechaHoy = strtotime(date('Y-m-d'));
                                $diasEspera = floor((time() - $fechaSolicitud) / (60 * 60 * 24));
                                $esHoy = date('Y-m-d', $fechaSolicitud) === date('Y-m-d');
                                $urgenciaClass = $diasEspera > 3 ? 'text-danger' : ($diasEspera > 1 ? 'text-warning' : '');
                                ?>
                                <tr class="<?php echo $solicitud['estado'] === 'Pendiente' ? 'table-light' : ''; ?>">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold"><?php echo $solicitud['idSolicitud']; ?></span>
                                            <?php if ($diasEspera > 2 && $solicitud['estado'] === 'Pendiente' && !$esHoy): ?>
                                                <span class="badge bg-danger ms-2 small">Urgente</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">
                                                    <?php echo htmlspecialchars($solicitud['usuario_nombre'] . (!empty($solicitud['usuario_apellido']) ? ' ' . $solicitud['usuario_apellido'] : '')); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($solicitud['usuario_email']); ?>
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
                                                    <?php echo htmlspecialchars($solicitud['libro_titulo']); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($solicitud['libro_autor']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <?php echo date('d/m/Y', strtotime($solicitud['fecha_solicitud'])); ?>
                                        </div>
                                        <div class="small <?php echo $urgenciaClass; ?>">
                                            <?php if ($esHoy): ?>
                                                <span class="text-success fw-semibold">Hoy</span>
                                            <?php elseif ($diasEspera == 1): ?>
                                                Ayer
                                            <?php else: ?>
                                                Hace <?php echo $diasEspera; ?> días
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                            <i class="<?php echo $iconClass; ?> me-1"></i>
                                            <?php echo $solicitud['estado']; ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($solicitud['libro_disponible'] > 0): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                <?php echo $solicitud['libro_disponible']; ?> disp.
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>
                                                No disponible
                                            </span>
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
                                                <?php if ($solicitud['libro_disponible'] > 0): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success" 
                                                            title="Aprobar y crear préstamo"
                                                            onclick="aprobarSolicitud(<?php echo $solicitud['idSolicitud']; ?>, '<?php echo htmlspecialchars($solicitud['libro_titulo']); ?>', '<?php echo htmlspecialchars($solicitud['usuario_nombre'] . (!empty($solicitud['usuario_apellido']) ? ' ' . $solicitud['usuario_apellido'] : '')); ?>')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Rechazar solicitud"
                                                        onclick="rechazarSolicitud(<?php echo $solicitud['idSolicitud']; ?>, '<?php echo htmlspecialchars($solicitud['libro_titulo']); ?>', '<?php echo htmlspecialchars($solicitud['usuario_nombre'] . (!empty($solicitud['usuario_apellido']) ? ' ' . $solicitud['usuario_apellido'] : '')); ?>')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($solicitud['estado'] === 'Convertida' && !empty($solicitud['prestamo_id'])): ?>
                                                <a href="index.php?page=prestamos&action=verDetalles&id=<?php echo $solicitud['prestamo_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Ver detalles del préstamo">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        title="Ampliar duración del préstamo"
                                                        onclick="ampliarDuracion(<?php echo $solicitud['prestamo_id']; ?>, '<?php echo htmlspecialchars($solicitud['libro_titulo']); ?>', '<?php echo htmlspecialchars($solicitud['usuario_nombre'] . (!empty($solicitud['usuario_apellido']) ? ' ' . $solicitud['usuario_apellido'] : '')); ?>')">
                                                    <i class="fas fa-clock"></i>
                                                </button>
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
                    <h5 class="text-muted">No hay solicitudes 
                        <?php echo isset($_GET['estado']) ? strtolower($_GET['estado']) : ''; ?>
                    </h5>
                    <p class="text-muted mb-0">Las nuevas solicitudes aparecerán aquí</p>
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

<!-- Modal para aprobar solicitud -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Aprobar Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=prestamos&action=aprobarSolicitud">
                <div class="modal-body">
                    <input type="hidden" id="aprobarSolicitudId" name="solicitud_id">
                    
                    <div class="alert alert-info border-0 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Al aprobar esta solicitud se creará automáticamente el préstamo correspondiente.
                    </div>
                    
                    <div id="aprobarInfo" class="mb-3"></div>
                    
                    <div class="mb-3">
                        <label for="fechaDevolucion" class="form-label">Fecha de Devolución</label>
                        <input type="date" 
                               class="form-control" 
                               id="fechaDevolucion" 
                               name="fecha_devolucion" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacionesAprobar" class="form-label">Observaciones</label>
                        <textarea class="form-control" 
                                  id="observacionesAprobar" 
                                  name="observaciones" 
                                  rows="3" 
                                  placeholder="Observaciones sobre la aprobación (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Aprobar y Crear Préstamo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para rechazar solicitud -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    Rechazar Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=prestamos&action=rechazarSolicitud">
                <div class="modal-body">
                    <input type="hidden" id="rechazarSolicitudId" name="solicitud_id">
                    
                    <div id="rechazarInfo" class="mb-3"></div>
                    
                    <div class="mb-3">
                        <label for="observacionesRechazar" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="observacionesRechazar" 
                                  name="observaciones" 
                                  rows="3" 
                                  placeholder="Explica el motivo del rechazo para informar al usuario..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ampliar duración del préstamo -->
<div class="modal fade" id="modalAmpliarDuracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Ampliar Duración del Préstamo (Desde Solicitud)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=prestamos&action=ampliarDuracionPrestamo">
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
// Configurar fecha mínima y sugerida
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fechaDevolucion');
    
    // Fecha mínima: mañana
    const mañana = new Date();
    mañana.setDate(mañana.getDate() + 1);
    fechaInput.min = mañana.toISOString().split('T')[0];
    
    // Fecha sugerida: 15 días desde hoy
    const fechaSugerida = new Date();
    fechaSugerida.setDate(fechaSugerida.getDate() + 15);
    fechaInput.value = fechaSugerida.toISOString().split('T')[0];
});

// Función para ver detalles
function verDetalles(solicitud) {
    // Similar a la función anterior pero adaptada para bibliotecarios
    const estadoBadge = {
        'Pendiente': 'bg-warning',
        'Aprobada': 'bg-success',
        'Rechazada': 'bg-danger',
        'Convertida': 'bg-info'
    };
    
    document.getElementById('modalDetallesContent').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 bg-light mb-3">
                    <div class="card-header bg-transparent">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-user text-primary me-2"></i>
                            Usuario Solicitante
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong><br>${solicitud.usuario_nombre}${solicitud.usuario_apellido ? ' ' + solicitud.usuario_apellido : ''}</p>
                        <p><strong>Email:</strong><br>${solicitud.usuario_email}</p>
                        ${solicitud.usuario_telefono ? `<p class="mb-0"><strong>Teléfono:</strong><br>${solicitud.usuario_telefono}</p>` : ''}
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 bg-light mb-3">
                    <div class="card-header bg-transparent">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-book text-info me-2"></i>
                            Libro Solicitado
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Título:</strong><br>${solicitud.libro_titulo}</p>
                        <p><strong>Autor:</strong><br>${solicitud.libro_autor}</p>
                        <p class="mb-0"><strong>Disponibles:</strong><br>
                            <span class="badge ${solicitud.libro_disponible > 0 ? 'bg-success' : 'bg-danger'}">${solicitud.libro_disponible} ejemplares</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        ${solicitud.observaciones_usuario ? `
            <div class="card border-0 bg-light">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-comment text-success me-2"></i>
                        Comentarios del usuario
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">${solicitud.observaciones_usuario}</p>
                </div>
            </div>
        ` : ''}
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
    modal.show();
}

// Función para aprobar solicitud
function aprobarSolicitud(solicitudId, libro, usuario) {
    document.getElementById('aprobarSolicitudId').value = solicitudId;
    document.getElementById('aprobarInfo').innerHTML = `
        <div class="card border-0 bg-success bg-opacity-10">
            <div class="card-body">
                <h6><i class="fas fa-user me-2"></i>Usuario: ${usuario}</h6>
                <h6><i class="fas fa-book me-2"></i>Libro: ${libro}</h6>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalAprobar'));
    modal.show();
}

// Función para rechazar solicitud
function rechazarSolicitud(solicitudId, libro, usuario) {
    document.getElementById('rechazarSolicitudId').value = solicitudId;
    document.getElementById('rechazarInfo').innerHTML = `
        <div class="card border-0 bg-danger bg-opacity-10">
            <div class="card-body">
                <h6><i class="fas fa-user me-2"></i>Usuario: ${usuario}</h6>
                <h6><i class="fas fa-book me-2"></i>Libro: ${libro}</h6>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalRechazar'));
    modal.show();
}

// Función para ampliar duración del préstamo
function ampliarDuracion(prestamoId, libro, usuario) {
    document.getElementById('ampliarPrestamoId').value = prestamoId;
    document.getElementById('ampliarInfo').innerHTML = `
        <div class="card border-0 bg-warning bg-opacity-10">
            <div class="card-body">
                <h6><i class="fas fa-user me-2"></i>Usuario: ${usuario}</h6>
                <h6><i class="fas fa-book me-2"></i>Libro: ${libro}</h6>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalAmpliarDuracion'));
    modal.show();
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

.btn-group .btn {
    transition: all 0.2s ease;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table-light {
    background-color: rgba(13, 110, 253, 0.05) !important;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

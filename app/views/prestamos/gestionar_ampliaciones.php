<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-clock text-warning me-2"></i>Gestionar Solicitudes de Ampliación</h2>
            <p class="text-muted mb-0">Revisa y gestiona las solicitudes de ampliación de préstamos</p>
        </div>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Préstamos
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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-list text-info fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1"><?php echo count($solicitudes); ?></h3>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-clock text-warning fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1"><?php echo count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Pendiente'; })); ?></h3>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1"><?php echo count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Aprobada'; })); ?></h3>
                    <p class="text-muted mb-0">Aprobadas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1"><?php echo count(array_filter($solicitudes, function($s) { return $s['estado'] == 'Rechazada'; })); ?></h3>
                    <p class="text-muted mb-0">Rechazadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-2">Filtrar por estado:</h6>
                    <div class="btn-group" role="group">
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones" 
                           class="btn btn-outline-primary <?php echo !isset($_GET['estado']) ? 'active' : ''; ?>">
                            Todas
                        </a>
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&estado=Pendiente" 
                           class="btn btn-outline-warning <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'active' : ''; ?>">
                            Pendientes
                        </a>
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&estado=Aprobada" 
                           class="btn btn-outline-success <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Aprobada') ? 'active' : ''; ?>">
                            Aprobadas
                        </a>
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones&estado=Rechazada" 
                           class="btn btn-outline-danger <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Rechazada') ? 'active' : ''; ?>">
                            Rechazadas
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

    <!-- Lista de Solicitudes -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Solicitudes de Ampliación</h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($solicitudes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Libro</th>
                                <th>Fecha Actual Vencimiento</th>
                                <th>Días Solicitados</th>
                                <th>Nueva Fecha</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Fecha Solicitud</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <?php 
                                $nuevaFecha = date('d/m/Y', strtotime($solicitud['fechaDevolucionEsperada'] . ' +' . $solicitud['diasAdicionales'] . ' days'));
                                ?>
                                <tr>
                                    <td><?php echo $solicitud['idSolicitud']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($solicitud['usuario_nombre']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($solicitud['usuario_email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-book text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($solicitud['libro_titulo']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($solicitud['libro_autor']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <?php echo date('d/m/Y', strtotime($solicitud['fechaDevolucionEsperada'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            +<?php echo $solicitud['diasAdicionales']; ?> días
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?php echo $nuevaFecha; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                onclick="verMotivo('<?php echo htmlspecialchars($solicitud['motivo']); ?>')"
                                                title="Ver motivo completo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <?php if ($solicitud['estado'] == 'Pendiente'): ?>
                                            <span class="badge bg-warning">Pendiente</span>
                                        <?php elseif ($solicitud['estado'] == 'Aprobada'): ?>
                                            <span class="badge bg-success">Aprobada</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Rechazada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($solicitud['fechaSolicitud'])); ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($solicitud['estado'] == 'Pendiente'): ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-outline-success" 
                                                        title="Aprobar Solicitud"
                                                        onclick="aprobarSolicitud(<?php echo $solicitud['idSolicitud']; ?>, '<?php echo htmlspecialchars($solicitud['libro_titulo']); ?>', <?php echo $solicitud['diasAdicionales']; ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Rechazar Solicitud"
                                                        onclick="rechazarSolicitud(<?php echo $solicitud['idSolicitud']; ?>, '<?php echo htmlspecialchars($solicitud['libro_titulo']); ?>')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <small class="text-muted">
                                                Procesada el <?php echo $solicitud['fechaRespuesta'] ? date('d/m/Y', strtotime($solicitud['fechaRespuesta'])) : '-'; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay solicitudes de ampliación</h5>
                    <p class="text-muted">Las nuevas solicitudes aparecerán aquí</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para ver motivo -->
<div class="modal fade" id="modalMotivo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comment text-info me-2"></i>
                    Motivo de la Solicitud
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <p id="motivoTexto" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para aprobar solicitud -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Aprobar Solicitud de Ampliación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=aprobarAmpliacion">
                <div class="modal-body">
                    <input type="hidden" id="aprobarSolicitudId" name="solicitud_id">
                    
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Libro:</strong> <span id="aprobarLibroTitulo"></span><br>
                        <strong>Días adicionales:</strong> <span id="aprobarDias"></span> días
                    </div>
                    
                    <div class="mb-3">
                        <label for="respuestaAprobar" class="form-label">Comentarios del bibliotecario (opcional)</label>
                        <textarea class="form-control" 
                                  id="respuestaAprobar" 
                                  name="respuesta" 
                                  rows="3" 
                                  placeholder="Comentarios adicionales sobre la aprobación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Aprobar Solicitud
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    Rechazar Solicitud de Ampliación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=rechazarAmpliacion">
                <div class="modal-body">
                    <input type="hidden" id="rechazarSolicitudId" name="solicitud_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ¿Estás seguro de que deseas rechazar la solicitud para <strong id="rechazarLibroTitulo"></strong>?
                    </div>
                    
                    <div class="mb-3">
                        <label for="respuestaRechazar" class="form-label">Motivo del rechazo *</label>
                        <textarea class="form-control" 
                                  id="respuestaRechazar" 
                                  name="respuesta" 
                                  rows="3" 
                                  placeholder="Explica por qué se rechaza la solicitud..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function verMotivo(motivo) {
    document.getElementById('motivoTexto').textContent = motivo;
    const modal = new bootstrap.Modal(document.getElementById('modalMotivo'));
    modal.show();
}

function aprobarSolicitud(solicitudId, libroTitulo, dias) {
    document.getElementById('aprobarSolicitudId').value = solicitudId;
    document.getElementById('aprobarLibroTitulo').textContent = libroTitulo;
    document.getElementById('aprobarDias').textContent = dias;
    const modal = new bootstrap.Modal(document.getElementById('modalAprobar'));
    modal.show();
}

function rechazarSolicitud(solicitudId, libroTitulo) {
    document.getElementById('rechazarSolicitudId').value = solicitudId;
    document.getElementById('rechazarLibroTitulo').textContent = libroTitulo;
    const modal = new bootstrap.Modal(document.getElementById('modalRechazar'));
    modal.show();
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
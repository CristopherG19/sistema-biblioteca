<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título Principal -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-book-reader text-primary me-2"></i>
                Gestión de Préstamos
            </h2>
            <p class="text-muted mb-0">Administra los préstamos de libros de la biblioteca</p>
        </div>
        <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=agregar" 
               class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Préstamo
            </a>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=vencidos" 
               class="btn btn-warning ms-2">
                <i class="fas fa-exclamation-triangle me-2"></i>Vencidos
            </a>
        </div>
        <?php else: ?>
        <div>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar" 
               class="btn btn-success">
                <i class="fas fa-paper-plane me-2"></i>Solicitar Préstamo
            </a>
            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes" 
               class="btn btn-outline-info ms-2">
                <i class="fas fa-list me-2"></i>Mis Solicitudes
            </a>
        </div>
        <?php endif; ?>
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
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-handshake fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['total_prestamos'] ?? 0; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Total Préstamos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['prestamos_activos'] ?? 0; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Activos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['prestamos_devueltos'] ?? 0; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Devueltos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h3 class="card-title text-dark fw-bold mb-1">
                        <?php echo $estadisticas['prestamos_vencidos'] ?? 0; ?>
                    </h3>
                    <p class="card-text text-muted small mb-0">Vencidos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <form method="GET" action="/SISTEMA_BIBLIOTECA/public/index.php">
                        <input type="hidden" name="page" value="prestamos">
                        <input type="hidden" name="action" value="buscar">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   placeholder="Buscar por usuario, libro, ISBN..." 
                                   value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        <select class="form-select" id="filtroEstado" style="max-width: 200px;">
                            <option value="">Todos los estados</option>
                            <option value="Activo">Activos</option>
                            <option value="Devuelto">Devueltos</option>
                            <option value="Vencido">Vencidos</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Préstamos -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0 text-dark fw-bold">
                <i class="fas fa-list me-2 text-primary"></i>
                Lista de Préstamos
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($prestamos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaPrestamos">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted fw-semibold py-3">#</th>
                                <th class="border-0 text-muted fw-semibold py-3">Usuario</th>
                                <th class="border-0 text-muted fw-semibold py-3">Libro</th>
                                <th class="border-0 text-muted fw-semibold py-3">F. Préstamo</th>
                                <th class="border-0 text-muted fw-semibold py-3">F. Dev. Esperada</th>
                                <th class="border-0 text-muted fw-semibold py-3">F. Dev. Real</th>
                                <th class="border-0 text-muted fw-semibold py-3">Estado</th>
                                <th class="border-0 text-muted fw-semibold py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestamos as $index => $prestamo): ?>
                                <?php 
                                $estado = 'Activo';
                                $badgeClass = 'bg-success';
                                
                                if (!empty($prestamo['fechaDevolucionReal'])) {
                                    $estado = 'Devuelto';
                                    $badgeClass = 'bg-info';
                                } elseif (strtotime($prestamo['fechaDevolucionEsperada']) < time()) {
                                    $estado = 'Vencido';
                                    $badgeClass = 'bg-danger';
                                }
                                ?>
                                <tr data-estado="<?php echo $estado; ?>">
                                    <td class="py-3 text-muted">
                                        <?php echo $prestamo['idPrestamo']; ?>
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
                                    <td class="py-3 text-muted">
                                        <?php 
                                        echo !empty($prestamo['fechaDevolucionReal']) 
                                            ? date('d/m/Y', strtotime($prestamo['fechaDevolucionReal'])) 
                                            : '-'; 
                                        ?>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                            <?php echo $estado; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                                                <?php if (empty($prestamo['fechaDevolucionReal'])): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-success" 
                                                            title="Registrar Devolución"
                                                            onclick="registrarDevolucion(<?php echo $prestamo['idPrestamo']; ?>)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button type="button" 
                                                        class="btn btn-outline-warning" 
                                                        title="Ampliar Duración"
                                                        onclick="ampliarDuracion(<?php echo $prestamo['idPrestamo']; ?>, '<?php echo htmlspecialchars($prestamo['libro_titulo'] ?? 'N/A'); ?>', '<?php echo htmlspecialchars($prestamo['usuario_nombre'] ?? 'N/A'); ?>')">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                                
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Eliminar"
                                                        onclick="confirmarEliminacion(<?php echo $prestamo['idPrestamo']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <!-- Acciones para lectores -->
                                                <?php if (empty($prestamo['fechaDevolucionReal'])): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-success btn-sm" 
                                                            title="Devolver Libro"
                                                            onclick="confirmarDevolucion(<?php echo $prestamo['idPrestamo']; ?>, '<?php echo htmlspecialchars($prestamo['libro_titulo']); ?>')">
                                                        <i class="fas fa-undo me-1"></i>Devolver
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-warning btn-sm ms-1" 
                                                            title="Solicitar Ampliación"
                                                            onclick="solicitarAmpliacion(<?php echo $prestamo['idPrestamo']; ?>, '<?php echo htmlspecialchars($prestamo['libro_titulo']); ?>')">
                                                        <i class="fas fa-clock me-1"></i>Ampliar
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Devuelto</span>
                                                <?php endif; ?>
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
                        <i class="fas fa-book-reader fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No hay préstamos registrados</h5>
                    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                        <p class="text-muted mb-3">Comienza agregando un nuevo préstamo</p>
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=agregar" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nuevo Préstamo
                        </a>
                    <?php else: ?>
                        <p class="text-muted mb-3">No tienes préstamos activos</p>
                        <div>
                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar" class="btn btn-success me-2">
                                <i class="fas fa-paper-plane me-2"></i>Solicitar Préstamo
                            </a>
                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros" class="btn btn-outline-primary">
                                <i class="fas fa-book me-2"></i>Ver Catálogo
                            </a>
                        </div>
                    <?php endif; ?>
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
                    <div class="mb-3">
                        <label for="observacionesDevolucion" class="form-label">Observaciones</label>
                        <textarea class="form-control" 
                                  id="observacionesDevolucion" 
                                  name="observaciones_devolucion" 
                                  rows="3" 
                                  placeholder="Observaciones sobre la devolución (opcional)"></textarea>
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

<!-- Modal para confirmar devolución del lector -->
<div class="modal fade" id="modalConfirmarDevolucion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-undo text-success me-2"></i>
                    Confirmar Devolución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    ¿Estás seguro de que deseas marcar como devuelto el libro <strong id="libroDevolver"></strong>?
                </div>
                <p class="text-muted">
                    Al confirmar, el libro será marcado como devuelto y estará disponible para otros usuarios.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=autodevolverLibro" style="display: inline;">
                    <input type="hidden" id="prestamoIdDevolver" name="prestamo_id">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo me-2"></i>Confirmar Devolución
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para solicitar ampliación -->
<div class="modal fade" id="modalAmpliacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Solicitar Ampliación de Préstamo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitarAmpliacion">
                <div class="modal-body">
                    <input type="hidden" id="prestamoIdAmpliacion" name="prestamo_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Solicitar ampliación para: <strong id="libroAmpliar"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="diasAdicionales" class="form-label">Días adicionales</label>
                        <select class="form-select" id="diasAdicionales" name="dias_adicionales" required>
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
                                  placeholder="Explica brevemente por qué necesitas más tiempo con el libro..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ampliar duración del préstamo (para bibliotecarios) -->
<div class="modal fade" id="modalAmpliarDuracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Ampliar Duración del Préstamo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=ampliarDuracionPrestamo">
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
// Filtro por estado
document.getElementById('filtroEstado').addEventListener('change', function() {
    const filtro = this.value;
    const filas = document.querySelectorAll('#tablaPrestamos tbody tr');
    
    filas.forEach(fila => {
        const estado = fila.getAttribute('data-estado');
        if (filtro === '' || estado === filtro) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
});

// Función para registrar devolución
function registrarDevolucion(prestamoId) {
    document.getElementById('devolucionPrestamoId').value = prestamoId;
    const modal = new bootstrap.Modal(document.getElementById('modalDevolucion'));
    modal.show();
}

// Función para confirmar eliminación
function confirmarEliminacion(prestamoId) {
    if (confirm('¿Estás seguro de que quieres eliminar este préstamo?')) {
        window.location.href = `/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=eliminar&id=${prestamoId}`;
    }
}

// Función para confirmar devolución del lector
function confirmarDevolucion(prestamoId, libroTitulo) {
    document.getElementById('prestamoIdDevolver').value = prestamoId;
    document.getElementById('libroDevolver').textContent = libroTitulo;
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarDevolucion'));
    modal.show();
}

// Función para solicitar ampliación
function solicitarAmpliacion(prestamoId, libroTitulo) {
    document.getElementById('prestamoIdAmpliacion').value = prestamoId;
    document.getElementById('libroAmpliar').textContent = libroTitulo;
    const modal = new bootstrap.Modal(document.getElementById('modalAmpliacion'));
    modal.show();
}

// Función para ampliar duración del préstamo (para bibliotecarios)
function ampliarDuracion(prestamoId, libroTitulo, usuarioNombre) {
    document.getElementById('ampliarPrestamoId').value = prestamoId;
    document.getElementById('ampliarInfo').innerHTML = `
        <div class="card border-0 bg-warning bg-opacity-10">
            <div class="card-body">
                <h6><i class="fas fa-user me-2"></i>Usuario: ${usuarioNombre}</h6>
                <h6><i class="fas fa-book me-2"></i>Libro: ${libroTitulo}</h6>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalAmpliarDuracion'));
    modal.show();
}

// Efectos hover para las filas
document.querySelectorAll('#tablaPrestamos tbody tr').forEach(fila => {
    fila.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#f8f9fa';
    });
    
    fila.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '';
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
    transition: background-color 0.2s ease;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}
</style>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Bienvenida -->
    <div class="row mb-4">
        <div class="col">
            <div class="welcome-card">
                <div class="d-flex align-items-center">
                    <div class="welcome-avatar me-3">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">¡Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</h3>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-crown me-1"></i>
                            <?= htmlspecialchars($usuario['rol_nombre']) ?> - Panel de Administración
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Último acceso: <?= date('d/m/Y H:i') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-books mb-3">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                    <div class="stats-title">Total Libros</div>
                    <div class="stats-number"><?= number_format($estadisticas['libros']['total']) ?></div>
                    <div class="stats-detail text-success">
                        <?= $estadisticas['libros']['disponibles'] ?> disponibles
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-users mb-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="stats-title">Total Usuarios</div>
                    <div class="stats-number"><?= number_format($estadisticas['usuarios']['total_usuarios']) ?></div>
                    <div class="stats-detail text-info">
                        <?= $estadisticas['usuarios']['total_lectores'] ?> lectores
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-categories mb-3">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <div class="stats-title">Categorías</div>
                    <div class="stats-number"><?= number_format($estadisticas['categorias']['total']) ?></div>
                    <div class="stats-detail text-primary">
                        Activas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-new mb-3">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                    <div class="stats-title">Nuevos Hoy</div>
                    <div class="stats-number"><?= $estadisticas['usuarios']['nuevos_hoy'] ?></div>
                    <div class="stats-detail text-warning">
                        Usuarios registrados
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="row g-4">
        <!-- Libros recientes -->
        <div class="col-lg-8">
            <div class="custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book-open me-2"></i>
                        Libros Agregados Recientemente
                    </h5>
                    <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($librosRecientes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Autor</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($librosRecientes as $libro): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="book-mini-icon me-2">
                                                        <i class="fas fa-book"></i>
                                                    </div>
                                                    <strong><?= htmlspecialchars($libro['titulo']) ?></strong>
                                                </div>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($libro['autor']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary rounded-pill">
                                                    <?= htmlspecialchars($libro['categoria_nombre']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold"><?= $libro['stock'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($libro['disponible'] > 0): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Disponible
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation me-1"></i>Agotado
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-book fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay libros registrados aún</h5>
                                <p class="text-muted mb-4">Comienza agregando el primer libro al catálogo</p>
                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=agregar" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Libro
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-4">
            <!-- Acciones rápidas -->
            <div class="custom-card mb-4">
                <div class="card-header text-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=agregar" 
                           class="btn btn-primary btn-lg rounded-3">
                            <i class="fas fa-book-medical me-2"></i>
                            Agregar Libro
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&action=agregar" 
                           class="btn btn-success btn-lg rounded-3">
                            <i class="fas fa-user-plus me-2"></i>
                            Registrar Usuario
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=categorias&action=agregar" 
                           class="btn btn-info btn-lg rounded-3">
                            <i class="fas fa-tag me-2"></i>
                            Nueva Categoría
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=agregar"
                           class="btn btn-warning btn-lg text-white rounded-3">
                            <i class="fas fa-handshake me-2"></i>
                            Nuevo Préstamo
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes"
                           class="btn btn-info btn-lg rounded-3">
                            <i class="fas fa-tasks me-2"></i>
                            Gestionar Solicitudes
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarAmpliaciones"
                           class="btn btn-warning btn-lg text-white rounded-3">
                            <i class="fas fa-clock me-2"></i>
                            Gestionar Ampliaciones
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=vencidos"
                           class="btn btn-danger btn-lg rounded-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Préstamos Vencidos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del sistema -->
            <div class="custom-card">
                <div class="card-header text-center">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="system-info">
                        <div class="info-item">
                            <span class="label">Versión:</span>
                            <span class="value">1.0.0</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Usuario:</span>
                            <span class="value"><?= htmlspecialchars($usuario['usuario']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Rol:</span>
                            <span class="badge bg-primary"><?= htmlspecialchars($usuario['rol_nombre']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Estado:</span>
                            <span class="badge bg-success">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                Conectado
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.welcome-avatar {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stats-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 15px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
    min-height: 160px;
}

.stats-card .card-body {
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.15rem 1.5rem 0 rgba(58, 59, 69, 0.18);
}

.stats-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
    color: white;
}

.stats-books {
    background: linear-gradient(45deg, #1cc88a, #13855c);
}

.stats-users {
    background: linear-gradient(45deg, #36b9cc, #258391);
}

.stats-categories {
    background: linear-gradient(45deg, #6f42c1, #59359a);
}

.stats-new {
    background: linear-gradient(45deg, #f6c23e, #dda20a);
}

.stats-title {
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #5a5c69;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: #3a3b45;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stats-detail {
    font-size: 0.875rem;
    font-weight: 500;
}

.custom-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 15px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    overflow: hidden;
    transition: all 0.3s ease;
}

.custom-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.15rem 1.5rem 0 rgba(58, 59, 69, 0.18);
}

.custom-card .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    color: #5a5c69;
}

.book-mini-icon {
    width: 35px;
    height: 35px;
    background: linear-gradient(45deg, #4e73df, #224abe);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.empty-state {
    padding: 3rem 2rem;
    color: #6c757d;
}

.btn-lg {
    padding: 0.875rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.2s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.75rem rgba(78, 115, 223, 0.3);
}

.btn-success {
    background: linear-gradient(45deg, #1cc88a, #13855c);
    border: none;
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.75rem rgba(28, 200, 138, 0.3);
}

.btn-info {
    background: linear-gradient(45deg, #36b9cc, #258391);
    border: none;
}

.btn-info:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.75rem rgba(54, 185, 204, 0.3);
}

.btn-warning {
    background: linear-gradient(45deg, #f6c23e, #dda20a);
    border: none;
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(246, 194, 62, 0.4);
    color: white;
}

.system-info {
    background: #f8f9fc;
    border-radius: 10px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item .label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
}

.info-item .value {
    font-weight: 700;
    color: #3a3b45;
}

.badge {
    border-radius: 10px;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 700;
    font-size: 0.875rem;
    color: #5a5c69;
    background: #f8f9fc;
}

.table-responsive {
    border-radius: 10px;
    border: 1px solid #e3e6f0;
}

.table {
    margin-bottom: 0;
}

.table td {
    border-color: #e3e6f0;
    vertical-align: middle;
}

/* Mejoras responsivas */
@media (max-width: 768px) {
    .stats-number {
        font-size: 1.75rem;
    }
    
    .welcome-card {
        padding: 1.5rem;
        text-align: center;
    }
    
    .stats-card .card-body {
        padding: 1.75rem 1rem;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>
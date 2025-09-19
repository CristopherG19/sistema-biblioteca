<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Bienvenida -->
    <div class="row mb-4">
        <div class="col">
            <div class="welcome-card-lector">
                <div class="d-flex align-items-center">
                    <div class="welcome-avatar me-3">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">¡Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</h3>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-book-reader me-1"></i>
                            <?= htmlspecialchars($usuario['rol_nombre']) ?> - Portal de Lectura
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

    <!-- Estadísticas para el lector -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-available mb-3">
                        <i class="fas fa-book-open fa-2x"></i>
                    </div>
                    <div class="stats-title">Libros Disponibles</div>
                    <div class="stats-number"><?= number_format($estadisticas['libros']['disponibles']) ?></div>
                    <div class="stats-detail text-success">
                        Listos para préstamo
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-loans mb-3">
                        <i class="fas fa-handshake fa-2x"></i>
                    </div>
                    <div class="stats-title">Mis Préstamos</div>
                    <div class="stats-number">0</div>
                    <div class="stats-detail text-info">
                        Préstamos activos
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon-wrapper stats-categories mb-3">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <div class="stats-title">Categorías</div>
                    <div class="stats-number"><?= number_format($estadisticas['categorias']['total']) ?></div>
                    <div class="stats-detail text-primary">
                        Para explorar
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="row">
        <!-- Libros disponibles -->
        <div class="col-lg-8 mb-4">
            <div class="custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Libros Disponibles para Préstamo
                    </h5>
                    <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search me-1"></i>Explorar Catálogo
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($librosDisponibles)): ?>
                        <div class="row">
                            <?php foreach ($librosDisponibles as $libro): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                                    <div class="book-card">
                                        <div class="book-cover">
                                            <i class="fas fa-book fa-lg"></i>
                                        </div>
                                        <div class="book-info">
                                            <h6 class="book-title"><?= htmlspecialchars($libro['titulo']) ?></h6>
                                            <p class="book-author"><?= htmlspecialchars($libro['autor']) ?></p>
                                            <span class="book-category">
                                                <?= htmlspecialchars($libro['categoria']) ?>
                                            </span>
                                            <div class="book-availability mt-2">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    Disponible (<?= $libro['disponible'] ?>)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay libros disponibles en este momento</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-4 mb-4">
            <!-- Acciones rápidas -->
            <div class="custom-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightning-bolt me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros" 
                           class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>
                            Buscar Libros
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar" 
                           class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>
                            Solicitar Préstamo
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes" 
                           class="btn btn-info btn-lg">
                            <i class="fas fa-inbox me-2"></i>
                            Mis Solicitudes
                        </a>
                        
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos"
                           class="btn btn-warning btn-lg text-white">
                            <i class="fas fa-handshake me-2"></i>
                            Mis Préstamos
                        </a>
                        
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="fas fa-heart me-2"></i>
                            Mis Favoritos
                            <small class="d-block">Próximamente</small>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información del usuario -->
            <div class="custom-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Mi Perfil
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Usuario:</span>
                        <span class="fw-bold"><?= htmlspecialchars($usuario['usuario']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Email:</span>
                        <span class="fw-bold"><?= htmlspecialchars($usuario['email']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Rol:</span>
                        <span class="badge bg-success"><i class="fas fa-book-reader me-1"></i>LECTOR</span>
                    </div>
                    <hr class="my-3">
                    <div class="text-center">
                        <small class="text-success fw-semibold">
                            <i class="fas fa-check-circle me-1"></i>
                            Cuenta verificada y activa
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consejos para el lector -->
    <div class="row">
        <div class="col-12">
            <div class="tips-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Consejos para una Mejor Experiencia
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="tip-item">
                                <i class="fas fa-search fa-2x text-primary mb-3"></i>
                                <h6>Explora el Catálogo</h6>
                                <p class="text-muted small">Usa la función de búsqueda para encontrar libros por título, autor o categoría.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="tip-item">
                                <i class="fas fa-calendar-alt fa-2x text-success mb-3"></i>
                                <h6>Respeta los Plazos</h6>
                                <p class="text-muted small">Devuelve los libros a tiempo para evitar multas y ayudar a otros lectores.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="tip-item">
                                <i class="fas fa-bookmark fa-2x text-warning mb-3"></i>
                                <h6>Cuida los Libros</h6>
                                <p class="text-muted small">Mantén los libros en buen estado para que otros también puedan disfrutarlos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-card-lector {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

.stats-available {
    background: linear-gradient(45deg, #1cc88a, #13855c);
}

.stats-loans {
    background: linear-gradient(45deg, #36b9cc, #258391);
}

.stats-categories {
    background: linear-gradient(45deg, #6f42c1, #59359a);
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

.book-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    margin-bottom: 1rem;
    box-shadow: 0 0.125rem 0.75rem rgba(58, 59, 69, 0.1);
    max-width: 200px;
    margin: 0 auto 1rem auto;
}

.book-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.15rem 1.5rem 0 rgba(58, 59, 69, 0.18);
    border-color: #1cc88a;
}

.book-cover {
    background: linear-gradient(45deg, #1cc88a, #13855c);
    border-radius: 8px;
    padding: 1.5rem 1rem;
    color: white;
    margin-bottom: 0.75rem;
    box-shadow: 0 0.25rem 0.75rem rgba(28, 200, 138, 0.3);
}

.book-title {
    font-weight: 700;
    color: #3a3b45;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
    line-height: 1.2;
}

.book-author {
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.book-category {
    background: #f8f9fc;
    color: #5a5c69;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.6875rem;
    font-weight: 600;
    display: inline-block;
    border: 1px solid #e3e6f0;
}

.tips-card {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 15px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    margin-top: 2rem;
}

.tips-card .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    color: #5a5c69;
}

.tip-item {
    text-align: center;
    padding: 2rem 1.5rem;
    transition: all 0.3s ease;
    background: white;
    border-radius: 12px;
    height: 100%;
    border: 1px solid #f1f3f4;
}

.tip-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.15rem 1rem rgba(0, 0, 0, 0.1);
    border-color: #e3e6f0;
}

.tip-item i {
    margin-bottom: 1rem;
    color: #5a5c69;
}

.tip-item h6 {
    font-weight: 700;
    color: #3a3b45;
    margin-bottom: 0.75rem;
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

.btn-info {
    background: linear-gradient(45deg, #36b9cc, #258391);
    border: none;
}

.btn-info:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.75rem rgba(54, 185, 204, 0.3);
}

.btn-success {
    background: linear-gradient(45deg, #1cc88a, #13855c);
    border: none;
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.75rem rgba(28, 200, 138, 0.3);
}

.btn-lg:disabled {
    opacity: 0.65;
    transform: none !important;
    box-shadow: none !important;
    background: #6c757d !important;
    border: none !important;
    color: white !important;
    cursor: not-allowed;
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
    letter-spacing: 0.5px;
}

/* Mejoras responsivas */
@media (max-width: 768px) {
    .stats-number {
        font-size: 1.75rem;
    }
    
    .welcome-card-lector {
        padding: 1.5rem;
        text-align: center;
    }
    
    .stats-card .card-body {
        padding: 1.75rem 1rem;
    }
    
    .book-card {
        margin-bottom: 0.75rem;
        max-width: 180px;
    }
}
</style>

<?php include __DIR__ . '/../partials/footer.php'; ?>
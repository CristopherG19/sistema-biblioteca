<?php
include 'partials/header.php';
?>
<!-- Hero Section -->
<div class="bg-gradient-primary text-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 text-white">Sistema de Gestión Bibliotecaria</h1>
                <p class="lead mb-4 text-white">Plataforma integral para la administración eficiente de recursos bibliográficos, usuarios y servicios de préstamo.</p>
                <div class="d-flex gap-3">
                    <a href="index.php?page=libros" class="btn btn-light btn-lg text-dark">
                        <i class="fas fa-book me-2 text-primary"></i>Explorar Catálogo
                    </a>
                    <a href="index.php?page=categorias" class="btn btn-outline-light btn-lg text-white border-white">
                        <i class="fas fa-tags me-2 text-white"></i>Ver Categorías
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-university fa-6x text-white opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-book text-primary fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">0</h3>
                    <p class="text-muted mb-0">Libros Registrados</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-tags text-success fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">0</h3>
                    <p class="text-muted mb-0">Categorías Activas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-users text-info fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">0</h3>
                    <p class="text-muted mb-0">Usuarios Activos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-handshake text-warning fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">0</h3>
                    <p class="text-muted mb-0">Préstamos Activos</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Modules -->
<div class="container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Módulos del Sistema</h2>
        <p class="text-muted">Accede a las diferentes funcionalidades de gestión</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-book text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Gestión de Libros</h5>
                            <p class="text-muted small mb-0">Catálogo completo</p>
                        </div>
                    </div>
                    <p class="card-text mb-4">Administra todo el catálogo de libros: agregar nuevos títulos, editar información, gestionar stock y disponibilidad.</p>
                    <div class="d-flex gap-2">
                        <a href="index.php?page=libros" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>Ver Libros
                        </a>
                        <a href="index.php?page=libros&action=agregar" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Agregar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-tags text-success fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Categorías</h5>
                            <p class="text-muted small mb-0">Clasificación temática</p>
                        </div>
                    </div>
                    <p class="card-text mb-4">Organiza la biblioteca con categorías temáticas. Facilita la búsqueda y clasificación de todos los recursos.</p>
                    <div class="d-flex gap-2">
                        <a href="index.php?page=categorias" class="btn btn-success">
                            <i class="fas fa-eye me-1"></i>Ver Categorías
                        </a>
                        <a href="index.php?page=categorias&action=agregar" class="btn btn-outline-success">
                            <i class="fas fa-plus me-1"></i>Agregar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Future Modules -->
    <div class="row g-4 mt-2">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-users text-info fa-2x"></i>
                    </div>
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <p class="card-text">Administra lectores y bibliotecarios del sistema.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="index.php?page=usuarios" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i>Ver Usuarios
                        </a>
                        <a href="index.php?page=usuarios&action=agregar" class="btn btn-outline-info">
                            <i class="fas fa-plus me-1"></i>Agregar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 opacity-75">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-handshake text-warning fa-2x"></i>
                    </div>
                    <h5 class="card-title">Préstamos</h5>
                    <p class="card-text">Gestiona préstamos, devoluciones y control de disponibilidad.</p>
                    <span class="badge bg-warning">Próximamente</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 opacity-75">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-bar text-danger fa-2x"></i>
                    </div>
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Consulta estadísticas y genera reportes detallados.</p>
                    <span class="badge bg-danger">Próximamente</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'partials/footer.php';
?>

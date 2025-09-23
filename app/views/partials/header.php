<?php
// Inicializar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir AuthController para verificar permisos
require_once __DIR__ . '/../../controllers/AuthController.php';

// Obtener datos del usuario actual
$usuarioActual = AuthController::getUsuarioActual();
$esBibliotecario = AuthController::esBibliotecario();
$esLector = AuthController::esLector();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Bibliotecaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/SISTEMA_BIBLIOTECA/public/css/style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="/SISTEMA_BIBLIOTECA/public/img/library-icon.ico" type="image/x-icon">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/SISTEMA_BIBLIOTECA/public/index.php?page=dashboard">
                <i class="fas fa-university me-2 fs-4"></i>
                <span>BiblioSys</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/SISTEMA_BIBLIOTECA/public/index.php?page=dashboard">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    
                    <!-- Catálogo - Disponible para todos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="catalogoDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-book me-1"></i>Catálogo
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros">
                                <i class="fas fa-book me-2"></i>Todos los Libros
                            </a></li>
                            <?php if ($esBibliotecario): ?>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=agregar">
                                    <i class="fas fa-plus me-2"></i>Agregar Libro
                                </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=categorias">
                                <i class="fas fa-tags me-2"></i>Categorías
                            </a></li>
                            <?php if ($esBibliotecario): ?>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=categorias&action=agregar">
                                    <i class="fas fa-tag me-2"></i>Nueva Categoría
                                </a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <!-- Préstamos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="prestamosDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-handshake me-1"></i>Préstamos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos">
                                <i class="fas fa-list me-2"></i>Todos los Préstamos
                            </a></li>
                            <?php if ($esBibliotecario): ?>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=agregar">
                                    <i class="fas fa-plus me-2"></i>Nuevo Préstamo
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=vencidos">
                                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Préstamos Vencidos
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Solicitudes</h6></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes">
                                    <i class="fas fa-tasks me-2 text-primary"></i>Gestionar Solicitudes
                                </a></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=gestionarSolicitudes&estado=Pendiente">
                                    <i class="fas fa-clock me-2 text-warning"></i>Solicitudes Pendientes
                                </a></li>
                            <?php else: ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos">
                                    <i class="fas fa-user-clock me-2"></i>Mis Préstamos
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Solicitudes</h6></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar">
                                    <i class="fas fa-paper-plane me-2 text-success"></i>Solicitar Préstamo
                                </a></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes">
                                    <i class="fas fa-inbox me-2 text-info"></i>Mis Solicitudes
                                </a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Usuario actual -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                <?php if ($esBibliotecario): ?>
                                    <i class="fas fa-user-shield"></i>
                                <?php else: ?>
                                    <i class="fas fa-user-graduate"></i>
                                <?php endif; ?>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="user-name"><?= htmlspecialchars($usuarioActual['nombre']) ?></div>
                                <small class="user-role"><?= htmlspecialchars($usuarioActual['rol_nombre']) ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-item-text">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-large me-3">
                                        <?php if ($esBibliotecario): ?>
                                            <i class="fas fa-user-shield"></i>
                                        <?php else: ?>
                                            <i class="fas fa-user-graduate"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($usuarioActual['nombre']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($usuarioActual['email']) ?></small>
                                        <div>
                                            <span class="badge <?= $esBibliotecario ? 'bg-primary' : 'bg-info' ?>">
                                                <?= htmlspecialchars($usuarioActual['rol_nombre']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Opciones según el rol -->
                            <?php if ($esBibliotecario): ?>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios">
                                    <i class="fas fa-users me-2"></i>Gestionar Usuarios
                                </a></li>
                                <li><a class="dropdown-item" href="/SISTEMA_BIBLIOTECA/public/index.php?page=reportes">
                                    <i class="fas fa-chart-bar me-2"></i>Reportes
                                </a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item disabled" href="#" tabindex="-1">
                                    <i class="fas fa-heart me-2"></i>Mis Favoritos
                                    <span class="badge bg-info ms-1">Próximamente</span>
                                </a></li>
                                <li><a class="dropdown-item disabled" href="#" tabindex="-1">
                                    <i class="fas fa-history me-2"></i>Mi Historial
                                    <span class="badge bg-info ms-1">Próximamente</span>
                                </a></li>
                            <?php endif; ?>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/SISTEMA_BIBLIOTECA/public/index.php?page=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Espaciado para navbar fixed -->
    <div style="height: 76px;"></div>

<style>
.user-avatar {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.user-avatar-large {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
}

.user-role {
    font-size: 11px;
    opacity: 0.8;
    line-height: 1;
}

.dropdown-item-text {
    padding: 1rem;
    white-space: normal;
}

.navbar-nav .dropdown-menu {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border-radius: 10px;
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.dropdown-item.text-danger:hover {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}
</style>

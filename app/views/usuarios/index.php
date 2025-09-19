<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-users text-primary me-2"></i>Gestión de Usuarios</h2>
                    <p class="text-muted mb-0">Administra lectores y bibliotecarios del sistema</p>
                </div>
                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&action=agregar" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Agregar Usuario
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-users text-primary fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1"><?php echo $estadisticas['total_usuarios']; ?></h3>
                    <p class="text-muted mb-0">Total Usuarios</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-book-reader text-success fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1"><?php echo $estadisticas['total_lectores']; ?></h3>
                    <p class="text-muted mb-0">Lectores</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-user-tie text-info fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1"><?php echo $estadisticas['total_bibliotecarios']; ?></h3>
                    <p class="text-muted mb-0">Bibliotecarios</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-plus text-warning fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1"><?php echo $estadisticas['nuevos_hoy']; ?></h3>
                    <p class="text-muted mb-0">Nuevos Hoy</p>
                </div>
            </div>
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

    <!-- Filtros y búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="GET" action="/SISTEMA_BIBLIOTECA/public/index.php">
                        <input type="hidden" name="page" value="usuarios">
                        <input type="hidden" name="action" value="buscar">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="q" placeholder="Buscar por nombre, apellido o email..." 
                                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                            <button class="btn btn-outline-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <form method="GET" action="/SISTEMA_BIBLIOTECA/public/index.php">
                        <input type="hidden" name="page" value="usuarios">
                        <input type="hidden" name="action" value="filtrarPorRol">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-filter"></i></span>
                            <select class="form-select" name="rol" onchange="this.form.submit()">
                                <option value="">Todos los roles</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol['idRol']; ?>" 
                                            <?php echo (isset($_GET['rol']) && $_GET['rol'] == $rol['idRol']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rol['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-2">
                    <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-refresh me-1"></i>Limpiar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Usuarios</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($usuarios)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron usuarios</h5>
                    <p class="text-muted">Comienza agregando el primer usuario al sistema</p>
                    <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&action=agregar" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Agregar Usuario
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th><i class="fas fa-user me-1"></i>Nombre Completo</th>
                                <th><i class="fas fa-envelope me-1"></i>Email</th>
                                <th><i class="fas fa-phone me-1"></i>Teléfono</th>
                                <th><i class="fas fa-user-tag me-1"></i>Rol</th>
                                <th><i class="fas fa-calendar me-1"></i>Fecha Registro</th>
                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark"><?php echo $usuario['idUsuario']; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                                                <small class="text-muted">@<?php echo htmlspecialchars($usuario['usuario']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                    <td>
                                        <?php 
                                        $rolClass = $usuario['rol'] == 1 ? 'bg-info' : 'bg-success';
                                        $rolIcon = $usuario['rol'] == 1 ? 'user-tie' : 'book-reader';
                                        ?>
                                        <span class="badge <?php echo $rolClass; ?>">
                                            <i class="fas fa-<?php echo $rolIcon; ?> me-1"></i>
                                            <?php echo isset($usuario['rol_nombre']) ? htmlspecialchars($usuario['rol_nombre']) : 'Sin rol'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&action=editar&id=<?php echo $usuario['idUsuario']; ?>" 
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&action=eliminar&id=<?php echo $usuario['idUsuario']; ?>" 
                                               class="btn btn-outline-danger" title="Eliminar"
                                               onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>
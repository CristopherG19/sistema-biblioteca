<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-books text-primary me-2"></i>Gestión de Libros</h2>
                    <p class="text-muted mb-0">Administra el catálogo completo de la biblioteca</p>
                </div>
                <div>
                    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                        <!-- Botones para Administradores -->
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=agregar" class="btn btn-primary">
                            <i class="fas fa-book-medical me-2"></i>Agregar Libro
                        </a>
                    <?php else: ?>
                        <!-- Botones para Lectores -->
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Solicitar Préstamo
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-book text-primary fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1"><?php echo count($libros); ?></h3>
                    <p class="text-muted mb-0">Total Libros</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1"><?php echo array_sum(array_column($libros, 'disponible')); ?></h3>
                    <p class="text-muted mb-0">Disponibles</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-hand-holding text-warning fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1"><?php echo array_sum(array_column($libros, 'stock')) - array_sum(array_column($libros, 'disponible')); ?></h3>
                    <p class="text-muted mb-0">En Préstamo</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-tags text-info fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1"><?php echo count($categorias); ?></h3>
                    <p class="text-muted mb-0">Categorías</p>
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
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar por título, autor o ISBN...">
                        <button class="btn btn-outline-primary" type="button">Buscar</button>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-filter"></i></span>
                        <select class="form-select" id="categoryFilter">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria['nombre']); ?>">
                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-refresh me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de libros -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Catálogo de Libros</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($libros)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay libros registrados</h5>
                    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                        <p class="text-muted">Comienza agregando el primer libro al catálogo</p>
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=agregar" class="btn btn-primary">
                            <i class="fas fa-book-medical me-2"></i>Agregar Libro
                        </a>
                    <?php else: ?>
                        <p class="text-muted">No hay libros disponibles en este momento</p>
                        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=misSolicitudes" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>Ver Mis Solicitudes
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="librosTable">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th><i class="fas fa-book me-1"></i>Título</th>
                                <th><i class="fas fa-user-edit me-1"></i>Autor</th>
                                <th><i class="fas fa-building me-1"></i>Editorial</th>
                                <th><i class="fas fa-calendar me-1"></i>Año</th>
                                <th><i class="fas fa-tag me-1"></i>Categoría</th>
                                <th><i class="fas fa-boxes me-1"></i>Stock</th>
                                <th><i class="fas fa-check-circle me-1"></i>Disponible</th>
                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libros as $libro): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark"><?php echo $libro['idLibro']; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($libro['titulo']); ?></div>
                                                <small class="text-muted">ISBN: <?php echo htmlspecialchars($libro['isbn']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                    <td><?php echo htmlspecialchars($libro['editorial']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($libro['anio']); ?></span></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo htmlspecialchars($libro['categoria']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($libro['stock']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($libro['disponible'] > 0): ?>
                                            <span class="badge bg-success"><?php echo htmlspecialchars($libro['disponible']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                                                <!-- Solo bibliotecarios pueden editar y eliminar -->
                                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=detalle&id=<?php echo $libro['idLibro']; ?>" 
                                                   class="btn btn-outline-info" title="Ver Detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=editar&id=<?php echo $libro['idLibro']; ?>" 
                                                   class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=agregar&libro_id=<?php echo $libro['idLibro']; ?>" 
                                                   class="btn btn-outline-success" title="Otorgar Préstamo">
                                                    <i class="fas fa-hand-holding"></i>
                                                </a>
                                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=eliminar&id=<?php echo $libro['idLibro']; ?>" 
                                                   class="btn btn-outline-danger" title="Eliminar"
                                                   onclick="return confirm('¿Estás seguro de que deseas eliminar este libro?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <!-- Los lectores solo ven detalle -->
                                                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=detalle&id=<?php echo $libro['idLibro']; ?>" 
                                                   class="btn btn-outline-info" title="Ver Detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($libro['disponible'] > 0): ?>
                                                    <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=solicitar&libro_id=<?php echo $libro['idLibro']; ?>" 
                                                       class="btn btn-outline-success" title="Solicitar Préstamo">
                                                        <i class="fas fa-hand-holding"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
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

<script>
// Funcionalidad de búsqueda
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('librosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const title = row.cells[1].textContent.toLowerCase();
        const author = row.cells[2].textContent.toLowerCase();
        const isbn = row.cells[1].querySelector('small').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || author.includes(searchTerm) || isbn.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Filtro por categoría
document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value.toLowerCase();
    const table = document.getElementById('librosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const category = row.cells[5].textContent.toLowerCase();
        
        if (selectedCategory === '' || category.includes(selectedCategory)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Limpiar filtros
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    
    const table = document.getElementById('librosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>

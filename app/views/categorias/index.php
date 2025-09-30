<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-tags text-primary me-2"></i>Gestión de Categorías</h2>
                    <p class="text-muted mb-0">Organiza y administra las categorías del catálogo</p>
                </div>
                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                    <a href="index.php?page=categorias&action=agregar" class="btn btn-primary">
                        <i class="fas fa-tag me-2"></i>Agregar Categoría
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-tags text-primary fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1"><?php echo count($categorias); ?></h3>
                    <p class="text-muted mb-0">Total Categorías</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1"><?php echo count(array_filter($categorias, function($cat) { return !empty(trim($cat['descripcion'])); })); ?></h3>
                    <p class="text-muted mb-0">Con Descripción</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-plus-circle text-info fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">
                        <?php 
                        $hoy = date('Y-m-d');
                        echo count(array_filter($categorias, function($cat) use ($hoy) { 
                            return isset($cat['fecha_creacion']) && date('Y-m-d', strtotime($cat['fecha_creacion'])) === $hoy; 
                        })); 
                        ?>
                    </h3>
                    <p class="text-muted mb-0">Nuevas Hoy</p>
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
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre o descripción...">
                        <button class="btn btn-outline-primary" type="button">Buscar</button>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">Todas</option>
                        <option value="con-descripcion">Con descripción</option>
                        <option value="sin-descripcion">Sin descripción</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-refresh me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de categorías -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Categorías</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($categorias)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay categorías registradas</h5>
                    <p class="text-muted">Comienza creando la primera categoría para organizar los libros</p>
                    <a href="index.php?page=categorias&action=agregar" class="btn btn-primary">
                        <i class="fas fa-tag me-2"></i>Agregar Categoría
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="categoriasTable">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th><i class="fas fa-tag me-1"></i>Nombre</th>
                                <th><i class="fas fa-align-left me-1"></i>Descripción</th>
                                <th><i class="fas fa-calendar me-1"></i>Fecha Creación</th>
                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark"><?php echo $categoria['idCategoria']; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-tag text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($categoria['nombre']); ?></div>
                                                <small class="text-muted">Categoría #<?php echo $categoria['idCategoria']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty(trim($categoria['descripcion']))): ?>
                                            <span class="text-muted"><?php echo htmlspecialchars($categoria['descripcion']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Sin descripción</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo isset($categoria['fecha_creacion']) ? date('d/m/Y', strtotime($categoria['fecha_creacion'])) : 'No disponible'; ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1): ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="index.php?page=categorias&action=editar&id=<?php echo $categoria['idCategoria']; ?>" 
                                                   class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?page=categorias&action=eliminar&id=<?php echo $categoria['idCategoria']; ?>" 
                                                   class="btn btn-outline-danger" title="Eliminar"
                                                   onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-eye"></i> Solo lectura
                                            </span>
                                        <?php endif; ?>
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
    const table = document.getElementById('categoriasTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const name = row.cells[1].textContent.toLowerCase();
        const description = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(searchTerm) || description.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Filtro por estado
document.getElementById('statusFilter').addEventListener('change', function() {
    const selectedStatus = this.value;
    const table = document.getElementById('categoriasTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const description = row.cells[2].textContent.trim();
        const hasDescription = !description.includes('Sin descripción');
        
        if (selectedStatus === '') {
            row.style.display = '';
        } else if (selectedStatus === 'con-descripcion' && hasDescription) {
            row.style.display = '';
        } else if (selectedStatus === 'sin-descripcion' && !hasDescription) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Limpiar filtros
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    
    const table = document.getElementById('categoriasTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>

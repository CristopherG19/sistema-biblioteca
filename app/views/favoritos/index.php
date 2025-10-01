<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header de la página -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-heart text-danger me-2"></i>
                        Mis Favoritos
                    </h2>
                    <p class="text-muted mb-0">Libros que has marcado como favoritos</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="actualizarFavoritos()">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                    </button>
                    <a href="index.php?page=libros" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Buscar Libros
                    </a>
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

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fas fa-heart fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['total_favoritos']; ?></h4>
                            <p class="text-muted mb-0">Total Favoritos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['favoritos_disponibles']; ?></h4>
                            <p class="text-muted mb-0">Disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $estadisticas['favoritos_no_disponibles']; ?></h4>
                            <p class="text-muted mb-0">No Disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="fas fa-percentage fa-2x"></i>
                            </div>
                            <h4 class="mb-1">
                                <?php 
                                $porcentaje = $estadisticas['total_favoritos'] > 0 
                                    ? round(($estadisticas['favoritos_disponibles'] / $estadisticas['total_favoritos']) * 100) 
                                    : 0; 
                                echo $porcentaje . '%';
                                ?>
                            </h4>
                            <p class="text-muted mb-0">Disponibilidad</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Favoritos -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="card-title mb-0 text-dark fw-bold">
                        <i class="fas fa-list me-2"></i>
                        Libros Favoritos
                        <span class="badge bg-primary ms-2"><?php echo count($favoritos); ?> libros</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($favoritos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3">Libro</th>
                                        <th class="py-3">Autor</th>
                                        <th class="py-3">Categoría</th>
                                        <th class="py-3">Stock</th>
                                        <th class="py-3">Estado</th>
                                        <th class="py-3">Agregado</th>
                                        <th class="py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($favoritos as $favorito): ?>
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php if ($favorito['portada']): ?>
                                                            <img src="<?php echo htmlspecialchars($favorito['portada']); ?>" 
                                                                 alt="Portada" class="rounded" 
                                                                 style="width: 40px; height: 50px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 50px;">
                                                                <i class="fas fa-book text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($favorito['titulo']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($favorito['isbn']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3"><?php echo htmlspecialchars($favorito['autor']); ?></td>
                                            <td class="py-3">
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($favorito['categoria_nombre']); ?></span>
                                            </td>
                                            <td class="py-3">
                                                <span class="fw-bold"><?php echo $favorito['stock']; ?></span>
                                            </td>
                                            <td class="py-3">
                                                <?php if ($favorito['disponible'] > 0): ?>
                                                    <span class="badge bg-success">✓ Disponible</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">⚠ No Disponible</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y', strtotime($favorito['fecha_agregado'])); ?>
                                                </small>
                                            </td>
                                            <td class="py-3">
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?page=libros&action=detalle&id=<?php echo $favorito['idLibro']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($favorito['archivo_pdf']): ?>
                                                        <a href="index.php?page=libros&action=visor&id=<?php echo $favorito['idLibro']; ?>" 
                                                           class="btn btn-sm btn-outline-info" title="Leer PDF" target="_blank">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarFavorito(<?php echo $favorito['idLibro']; ?>)" 
                                                            title="Quitar de Favoritos">
                                                        <i class="fas fa-heart-broken"></i>
                                                    </button>
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
                                <i class="fas fa-heart fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No tienes libros favoritos</h5>
                            <p class="text-muted">Explora nuestra biblioteca y marca los libros que te gusten como favoritos</p>
                            <a href="index.php?page=libros" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Buscar Libros
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function actualizarFavoritos() {
    location.reload();
}

function eliminarFavorito(libroId) {
    if (confirm('¿Estás seguro de que quieres quitar este libro de tus favoritos?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?page=favoritos&action=eliminar';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'libro_id';
        input.value = libroId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Función para toggle favorito desde otras páginas
function toggleFavorito(libroId) {
    fetch('index.php?page=favoritos&action=toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'libro_id=' + libroId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar el botón de favorito
            const boton = document.querySelector(`[data-libro-id="${libroId}"]`);
            if (boton) {
                if (data.accion === 'agregado') {
                    boton.innerHTML = '<i class="fas fa-heart"></i>';
                    boton.classList.remove('btn-outline-danger');
                    boton.classList.add('btn-danger');
                } else {
                    boton.innerHTML = '<i class="far fa-heart"></i>';
                    boton.classList.remove('btn-danger');
                    boton.classList.add('btn-outline-danger');
                }
            }
            
            // Mostrar mensaje
            const alerta = document.createElement('div');
            alerta.className = 'alert alert-success alert-dismissible fade show';
            alerta.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alerta, document.querySelector('.row'));
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

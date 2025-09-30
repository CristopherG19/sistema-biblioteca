<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-book-open text-primary me-2"></i>Editar Libro</h2>
                    <p class="text-muted mb-0">Modifica la información del libro: <strong><?php echo htmlspecialchars($libro['titulo']); ?></strong></p>
                </div>
                <a href="index.php?page=libros" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>

            <!-- Mostrar errores -->
            <?php if (isset($errores) && !empty($errores)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Se encontraron los siguientes errores:</h6>
                    <ul class="mb-0">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Información del Libro</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=libros&action=actualizar">
                        <input type="hidden" name="id" value="<?= $libro['idLibro'] ?>">
                        
                        <div class="row">
                            <!-- Información Principal -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Datos Principales</h6>
                                
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">
                                        <i class="fas fa-heading me-1"></i>Título *
                                    </label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" 
                                           value="<?= htmlspecialchars($libro['titulo']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="autor" class="form-label">
                                        <i class="fas fa-user-edit me-1"></i>Autor *
                                    </label>
                                    <input type="text" class="form-control" id="autor" name="autor" 
                                           value="<?= htmlspecialchars($libro['autor']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="editorial" class="form-label">
                                        <i class="fas fa-building me-1"></i>Editorial *
                                    </label>
                                    <input type="text" class="form-control" id="editorial" name="editorial" 
                                           value="<?= htmlspecialchars($libro['editorial']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="anio" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Año *
                                    </label>
                                    <input type="number" class="form-control" id="anio" name="anio" 
                                           value="<?= htmlspecialchars($libro['anio']) ?>" 
                                           min="1900" max="<?php echo date('Y') + 1; ?>" required>
                                </div>
                            </div>

                            <!-- Información Técnica -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-cog me-2"></i>Información Técnica</h6>
                                
                                <div class="mb-3">
                                    <label for="isbn" class="form-label">
                                        <i class="fas fa-barcode me-1"></i>ISBN *
                                    </label>
                                    <input type="text" class="form-control" id="isbn" name="isbn" 
                                           value="<?= htmlspecialchars($libro['isbn']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="stock" class="form-label">
                                        <i class="fas fa-boxes me-1"></i>Stock Total *
                                    </label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           value="<?= htmlspecialchars($libro['stock']) ?>" min="1" required>
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Total de ejemplares en la biblioteca</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="disponible" class="form-label">
                                        <i class="fas fa-check-circle me-1"></i>Disponibles *
                                    </label>
                                    <input type="number" class="form-control" id="disponible" name="disponible" 
                                           value="<?= htmlspecialchars($libro['disponible']) ?>" min="0" required>
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Ejemplares disponibles para préstamo</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="idCategoria" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Categoría *
                                    </label>
                                    <select class="form-select" id="idCategoria" name="idCategoria" required>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['idCategoria'] ?>" 
                                                    <?= $categoria['idCategoria'] == $libro['idCategoria'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Descripción
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                                      placeholder="Descripción breve del libro (opcional)"><?= htmlspecialchars($libro['descripcion']) ?></textarea>
                        </div>

                        <!-- Información del registro -->
                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-info-circle me-2"></i>Información del Registro:</h6>
                            <div class="row">
                                <div class="col-sm-6">
                                    <small><strong>ID del Libro:</strong> <?= $libro['idLibro'] ?></small>
                                </div>
                                <div class="col-sm-6">
                                    <small><strong>Ejemplares prestados:</strong> <?= $libro['stock'] - $libro['disponible'] ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <hr>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="index.php?page=libros" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Libro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockInput = document.getElementById('stock');
    const disponibleInput = document.getElementById('disponible');
    
    // Función para sincronizar disponible con stock
    function sincronizarDisponible() {
        const stockValue = parseInt(stockInput.value) || 0;
        const disponibleValue = parseInt(disponibleInput.value) || 0;
        
        // Si el stock es mayor que el disponible, ajustar disponible al stock
        if (stockValue > disponibleValue) {
            disponibleInput.value = stockValue;
        }
    }
    
    // Sincronizar cuando cambie el stock
    stockInput.addEventListener('input', sincronizarDisponible);
    
    // Validar que disponible no sea mayor que stock
    disponibleInput.addEventListener('input', function() {
        const stockValue = parseInt(stockInput.value) || 0;
        const disponibleValue = parseInt(this.value) || 0;
        
        if (disponibleValue > stockValue) {
            this.value = stockValue;
            alert('Los ejemplares disponibles no pueden ser mayores al stock total');
        }
    });
});
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>

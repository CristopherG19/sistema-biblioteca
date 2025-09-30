<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-tag text-primary me-2"></i>Agregar Categoría</h2>
                    <p class="text-muted mb-0">Crea una nueva categoría para organizar los libros</p>
                </div>
                <a href="index.php?page=categorias" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Información de la Categoría</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=categorias&action=guardar">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Datos de la Categoría</h6>
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-heading me-1"></i>Nombre de la Categoría *
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" 
                                   placeholder="Ej: Ciencia Ficción, Historia, Romance..." required>
                            <div class="form-text">
                                <small><i class="fas fa-info-circle me-1"></i>Nombre único para identificar la categoría</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Descripción
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                                      placeholder="Descripción detallada de la categoría (opcional)..."><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
                            <div class="form-text">
                                <small><i class="fas fa-info-circle me-1"></i>Información adicional sobre el tipo de libros que incluye esta categoría</small>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-lightbulb me-2"></i>Consejos:</h6>
                            <ul class="mb-0 small text-muted">
                                <li>Usa nombres descriptivos y específicos</li>
                                <li>Evita duplicar categorías existentes</li>
                                <li>La descripción ayuda a otros bibliotecarios a clasificar libros</li>
                            </ul>
                        </div>

                        <!-- Botones -->
                        <hr>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="index.php?page=categorias" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>

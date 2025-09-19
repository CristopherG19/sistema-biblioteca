<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-plus-circle text-primary me-2"></i>
                Nuevo Préstamo
            </h2>
            <p class="text-muted mb-0">Registra un nuevo préstamo de libro</p>
        </div>
        <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Mostrar errores -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-clipboard-list text-primary me-2"></i>
                        Información del Préstamo
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=guardar">
                        <div class="row">
                            <!-- Usuario -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usuario_id" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        Usuario <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="usuario_id" name="usuario_id" required>
                                        <option value="">Selecciona un usuario</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?php echo $usuario['idUsuario']; ?>"
                                                    <?php echo (isset($_POST['usuario_id']) && $_POST['usuario_id'] == $usuario['idUsuario']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($usuario['nombre'] . ' - ' . $usuario['email']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Selecciona el usuario que realizará el préstamo</div>
                                </div>
                            </div>

                            <!-- Libro -->
                            <div class="col-md-6">
                                <?php if (isset($_GET['libro_id'])): ?>
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Libro preseleccionado</strong><br>
                                        <small>Se ha seleccionado automáticamente el libro desde el catálogo</small>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="libro_id" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-book text-primary me-2"></i>
                                        Libro <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="libro_id" name="libro_id" required>
                                        <option value="">Selecciona un libro</option>
                                        <?php foreach ($libros as $libro): ?>
                                            <option value="<?php echo $libro['idLibro']; ?>" 
                                                    data-disponible="<?php echo $libro['disponible']; ?>"
                                                    <?php 
                                                    $selected = false;
                                                    if (isset($_POST['libro_id']) && $_POST['libro_id'] == $libro['idLibro']) {
                                                        $selected = true;
                                                    } elseif (isset($_GET['libro_id']) && $_GET['libro_id'] == $libro['idLibro']) {
                                                        $selected = true;
                                                    }
                                                    echo $selected ? 'selected' : ''; 
                                                    ?>>
                                                <?php echo htmlspecialchars($libro['titulo'] . ' - ' . $libro['autor']); ?>
                                                (Disponibles: <?php echo $libro['disponible']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Selecciona el libro a prestar</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha de Préstamo -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_prestamo" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        Fecha de Préstamo <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="fecha_prestamo" name="fecha_prestamo" 
                                           value="<?php echo isset($_POST['fecha_prestamo']) ? $_POST['fecha_prestamo'] : date('Y-m-d'); ?>" required>
                                    <div class="form-text">Fecha en que se realiza el préstamo</div>
                                </div>
                            </div>

                            <!-- Fecha de Devolución -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_devolucion" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-calendar-times text-primary me-2"></i>
                                        Fecha de Devolución <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" 
                                           value="<?php echo isset($_POST['fecha_devolucion']) ? $_POST['fecha_devolucion'] : date('Y-m-d', strtotime('+14 days')); ?>" required>
                                    <div class="form-text">Fecha límite para devolver el libro</div>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold text-dark">
                                <i class="fas fa-comment text-primary me-2"></i>
                                Observaciones
                            </label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                      placeholder="Observaciones adicionales sobre el préstamo (opcional)"><?php echo isset($_POST['observaciones']) ? htmlspecialchars($_POST['observaciones']) : ''; ?></textarea>
                            <div class="form-text">Información adicional sobre el préstamo</div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Registrar Préstamo
                            </button>
                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral de recordatorios -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Recordatorios
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Verificar disponibilidad del libro</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar text-info me-2"></i>
                            <small>Establecer fecha de devolución realista</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user-check text-primary me-2"></i>
                            <small>Confirmar datos del usuario</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-sticky-note text-warning me-2"></i>
                            <small>Agregar observaciones si es necesario</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaPrestamo = document.getElementById('fecha_prestamo');
    const fechaDevolucion = document.getElementById('fecha_devolucion');
    
    // Actualizar fecha de devolución cuando cambia la fecha de préstamo
    fechaPrestamo.addEventListener('change', function() {
        const fechaInicio = new Date(this.value);
        const fechaFin = new Date(fechaInicio);
        fechaFin.setDate(fechaFin.getDate() + 14);
        
        fechaDevolucion.value = fechaFin.toISOString().split('T')[0];
    });
    
    // Validar disponibilidad del libro
    const libroSelect = document.getElementById('libro_id');
    libroSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const disponible = selectedOption.getAttribute('data-disponible');
        
        if (disponible && parseInt(disponible) === 0) {
            alert('Atención: Este libro no tiene ejemplares disponibles.');
        }
    });
});
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <!-- Título y navegación -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark fw-bold mb-0">
                <i class="fas fa-edit text-primary me-2"></i>
                Editar Préstamo
            </h2>
            <p class="text-muted mb-0">Modifica la información del préstamo #<?php echo htmlspecialchars($prestamo['idPrestamo']); ?></p>
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

    <div class="row">
        <!-- Formulario Principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 text-dark fw-bold">
                        <i class="fas fa-form text-primary me-2"></i>
                        Información del Préstamo
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=actualizar" id="formPrestamo">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($prestamo['idPrestamo']); ?>">
                        
                        <div class="row">
                            <!-- Selección de Usuario -->
                            <div class="col-md-6 mb-4">
                                <label for="usuario_id" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Usuario <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="usuario_id" name="usuario_id" required>
                                    <option value="">Selecciona un usuario</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['idUsuario']; ?>" 
                                                <?php echo (isset($_POST['usuario_id']) ? 
                                                    ($_POST['usuario_id'] == $usuario['idUsuario'] ? 'selected' : '') : 
                                                    ($prestamo['usuario_id'] == $usuario['idUsuario'] ? 'selected' : '')); ?>>
                                            <?php echo htmlspecialchars($usuario['nombre'] . ' - ' . $usuario['email']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Usuario que realiza el préstamo</div>
                            </div>

                            <!-- Selección de Libro -->
                            <div class="col-md-6 mb-4">
                                <label for="libro_id" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-book text-primary me-2"></i>
                                    Libro <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="libro_id" name="libro_id" required>
                                    <option value="">Selecciona un libro</option>
                                    <?php foreach ($libros as $libro): ?>
                                        <option value="<?php echo $libro['idLibro']; ?>" 
                                                data-disponible="<?php echo $libro['disponible']; ?>"
                                                <?php echo (isset($_POST['libro_id']) ? 
                                                    ($_POST['libro_id'] == $libro['idLibro'] ? 'selected' : '') : 
                                                    ($prestamo['libro_id'] == $libro['idLibro'] ? 'selected' : '')); ?>>
                                            <?php echo htmlspecialchars($libro['titulo'] . ' - ' . $libro['autor']); ?>
                                            (Disponibles: <?php echo $libro['disponible']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Libro en préstamo</div>
                                <div id="libroInfo" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha de Préstamo -->
                            <div class="col-md-6 mb-4">
                                <label for="fecha_prestamo" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    Fecha de Préstamo <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_prestamo" 
                                       name="fecha_prestamo" 
                                       value="<?php echo isset($_POST['fecha_prestamo']) ? $_POST['fecha_prestamo'] : date('Y-m-d', strtotime($prestamo['fechaPrestamo'])); ?>" 
                                       required>
                                <div class="form-text">Fecha en que se realizó el préstamo</div>
                            </div>

                            <!-- Fecha de Devolución Esperada -->
                            <div class="col-md-6 mb-4">
                                <label for="fecha_devolucion_esperada" class="form-label fw-semibold text-dark">
                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                    Fecha de Devolución <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_devolucion_esperada" 
                                       name="fecha_devolucion_esperada" 
                                       value="<?php echo isset($_POST['fecha_devolucion_esperada']) ? $_POST['fecha_devolucion_esperada'] : date('Y-m-d', strtotime($prestamo['fechaDevolucionEsperada'])); ?>" 
                                       required>
                                <div class="form-text">Fecha límite para devolver el libro</div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold text-dark">
                                <i class="fas fa-comment text-primary me-2"></i>
                                Observaciones
                            </label>
                            <textarea class="form-control" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="4" 
                                      placeholder="Observaciones adicionales sobre el préstamo (opcional)"><?php echo htmlspecialchars(isset($_POST['observaciones']) ? $_POST['observaciones'] : ($prestamo['observaciones'] ?? '')); ?></textarea>
                            <div class="form-text">Información adicional sobre el préstamo</div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Préstamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <!-- Estado del Préstamo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info bg-opacity-10 border-0 py-3">
                    <h6 class="card-title mb-0 text-info fw-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Estado del Préstamo
                    </h6>
                </div>
                <div class="card-body">
                    <?php 
                    $estado = 'Activo';
                    $badgeClass = 'bg-success';
                    $iconClass = 'fas fa-clock';
                    
                    if (!empty($prestamo['fecha_devolucion_real'])) {
                        $estado = 'Devuelto';
                        $badgeClass = 'bg-info';
                        $iconClass = 'fas fa-check-circle';
                    } elseif (strtotime($prestamo['fechaDevolucionEsperada']) < time()) {
                        $estado = 'Vencido';
                        $badgeClass = 'bg-danger';
                        $iconClass = 'fas fa-exclamation-triangle';
                    }
                    ?>
                    
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="<?php echo $iconClass; ?> fa-3x text-muted"></i>
                        </div>
                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 fs-6">
                            <?php echo $estado; ?>
                        </span>
                        
                        <?php if (!empty($prestamo['fecha_devolucion_real'])): ?>
                            <div class="mt-3">
                                <small class="text-muted">Devuelto el:</small><br>
                                <strong><?php echo date('d/m/Y', strtotime($prestamo['fecha_devolucion_real'])); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información Actual -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                    <h6 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-file-alt me-2"></i>
                        Información Actual
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">ID del Préstamo:</small><br>
                        <strong>#<?php echo htmlspecialchars($prestamo['idPrestamo']); ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Fecha de Registro:</small><br>
                        <strong><?php echo date('d/m/Y H:i', strtotime($prestamo['fechaPrestamo'])); ?></strong>
                    </div>
                    
                    <?php if (!empty($prestamo['observaciones_devolucion'])): ?>
                        <div class="mb-0">
                            <small class="text-muted">Observaciones de Devolución:</small><br>
                            <em class="text-muted"><?php echo htmlspecialchars($prestamo['observaciones_devolucion']); ?></em>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recordatorios -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
                    <h6 class="card-title mb-0 text-warning fw-bold">
                        <i class="fas fa-lightbulb me-2"></i>
                        Recordatorios
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                            <small>Los cambios afectarán el historial del préstamo</small>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <i class="fas fa-calendar text-info me-2"></i>
                            <small>Verificar que las fechas sean coherentes</small>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <i class="fas fa-book text-success me-2"></i>
                            <small>Confirmar disponibilidad si cambias el libro</small>
                        </div>
                        <?php if ($estado === 'Devuelto'): ?>
                        <div class="list-group-item border-0 px-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <small>El libro ya fue devuelto</small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Configurar fecha mínima y máxima
document.addEventListener('DOMContentLoaded', function() {
    const fechaPrestamo = document.getElementById('fecha_prestamo');
    const fechaDevolucion = document.getElementById('fecha_devolucion_esperada');
    
    // Configurar fechas
    fechaPrestamo.addEventListener('change', function() {
        const fechaMin = new Date(this.value);
        fechaMin.setDate(fechaMin.getDate() + 1);
        fechaDevolucion.min = fechaMin.toISOString().split('T')[0];
    });
    
    // Verificar disponibilidad del libro
    document.getElementById('libro_id').addEventListener('change', function() {
        const libroId = this.value;
        const infoDiv = document.getElementById('libroInfo');
        const libroOriginal = '<?php echo $prestamo['libro_id']; ?>';
        
        if (libroId && libroId !== libroOriginal) {
            // Verificar disponibilidad solo si es un libro diferente
            fetch(`/SISTEMA_BIBLIOTECA/public/index.php?page=prestamos&action=getLibroInfo&libro_id=${libroId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.disponible) {
                            infoDiv.innerHTML = `
                                <div class="alert alert-success py-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <small>Libro disponible para cambio</small>
                                </div>
                            `;
                        } else {
                            infoDiv.innerHTML = `
                                <div class="alert alert-warning py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small>Libro no disponible actualmente</small>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            infoDiv.innerHTML = '';
        }
    });
});

// Validación del formulario
document.getElementById('formPrestamo').addEventListener('submit', function(e) {
    const libroSelect = document.getElementById('libro_id');
    const libroSeleccionado = libroSelect.options[libroSelect.selectedIndex];
    const libroOriginal = '<?php echo $prestamo['libro_id']; ?>';
    
    if (libroSeleccionado && libroSelect.value !== libroOriginal && libroSeleccionado.dataset.disponible == '0') {
        e.preventDefault();
        alert('El libro seleccionado no está disponible para préstamo.');
        return false;
    }
    
    const fechaPrestamo = new Date(document.getElementById('fecha_prestamo').value);
    const fechaDevolucion = new Date(document.getElementById('fecha_devolucion_esperada').value);
    
    if (fechaDevolucion <= fechaPrestamo) {
        e.preventDefault();
        alert('La fecha de devolución debe ser posterior a la fecha de préstamo.');
        return false;
    }
});
</script>

<style>
.form-label {
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #e0e6ed;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.card {
    transition: all 0.3s ease;
}

.alert {
    border: none;
    border-radius: 8px;
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    font-size: 1rem;
    font-weight: 500;
}
</style>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-paper-plane text-primary me-2"></i>Solicitar Préstamo</h2>
                    <p class="text-muted mb-0">Solicita un libro de la biblioteca para préstamo</p>
                </div>
                <div>
                    <a href="index.php?page=prestamos&action=misSolicitudes" class="btn btn-outline-info me-2">
                        <i class="fas fa-list me-2"></i>Mis Solicitudes
                    </a>
                    <a href="index.php?page=libros" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>

            <!-- Mostrar errores -->
            <?php if (!empty($errores)): ?>
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

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Solicitud</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=prestamos&action=procesarSolicitud">
                        
                        <!-- Datos Principales -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="libro_id" class="form-label">
                                        <i class="fas fa-book me-1"></i>Libro que deseas solicitar *
                                    </label>
                                    <select class="form-select" id="libro_id" name="libro_id" required>
                                        <option value="">Selecciona un libro</option>
                                        <?php foreach ($libros as $libro): ?>
                                            <?php if ($libro['disponible'] > 0): ?>
                                                <option value="<?php echo $libro['idLibro']; ?>" 
                                                        data-disponible="<?php echo $libro['disponible']; ?>"
                                                        data-titulo="<?php echo htmlspecialchars($libro['titulo']); ?>"
                                                        data-autor="<?php echo htmlspecialchars($libro['autor']); ?>"
                                                        data-editorial="<?php echo htmlspecialchars($libro['editorial']); ?>"
                                                        data-categoria="<?php echo htmlspecialchars($libro['categoria_nombre']); ?>"
                                                        <?php echo (isset($_POST['libro_id']) && $_POST['libro_id'] == $libro['idLibro']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($libro['titulo'] . ' - ' . $libro['autor']); ?>
                                                    (<?php echo $libro['disponible']; ?> disponible<?php echo $libro['disponible'] != 1 ? 's' : ''; ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        <small>Solo se muestran libros disponibles para préstamo</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="motivo" class="form-label">
                                        <i class="fas fa-comment me-1"></i>Motivo o comentarios (opcional)
                                    </label>
                                    <textarea class="form-control" id="motivo" name="motivo" rows="4" 
                                              placeholder="Puedes agregar información adicional sobre por qué necesitas este libro..."><?php echo isset($_POST['motivo']) ? htmlspecialchars($_POST['motivo']) : ''; ?></textarea>
                                    <div class="form-text">
                                        <small>Campo opcional pero puede ayudar al bibliotecario a priorizar tu solicitud</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Libro Seleccionado -->
                        <div id="libro-info" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-book me-2"></i>Información del Libro Seleccionado</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Título:</strong> <span id="info-titulo">-</span><br>
                                    <strong>Autor:</strong> <span id="info-autor">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Editorial:</strong> <span id="info-editorial">-</span><br>
                                    <strong>Categoría:</strong> <span id="info-categoria">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                            </button>
                            <a href="index.php?page=libros" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="button" class="btn btn-outline-warning" onclick="limpiarFormulario()">
                                <i class="fas fa-eraser me-2"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Panel lateral con información -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>¿Cómo funciona?</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px;">
                            1
                        </div>
                        <div>
                            <strong>Envías tu solicitud</strong><br>
                            <small class="text-muted">Selecciona el libro y envía tu solicitud</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px;">
                            2
                        </div>
                        <div>
                            <strong>Revisión</strong><br>
                            <small class="text-muted">El bibliotecario revisa tu solicitud</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px;">
                            3
                        </div>
                        <div>
                            <strong>Aprobación</strong><br>
                            <small class="text-muted">Recibes notificación del resultado</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-size: 14px;">
                            4
                        </div>
                        <div>
                            <strong>Préstamo activo</strong><br>
                            <small class="text-muted">Puedes retirar el libro de la biblioteca</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Recordatorios</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Solo puedes solicitar libros disponibles</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-info me-2"></i>
                            <small>Las solicitudes se procesan en 24-48h</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-book text-primary me-2"></i>
                            <small>Una solicitud por libro a la vez</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-bell text-warning me-2"></i>
                            <small>Revisa tus solicitudes regularmente</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const libroSelect = document.getElementById('libro_id');
    const libroInfo = document.getElementById('libro-info');
    
    libroSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value && selectedOption) {
            // Mostrar información del libro
            document.getElementById('info-titulo').textContent = selectedOption.getAttribute('data-titulo') || '-';
            document.getElementById('info-autor').textContent = selectedOption.getAttribute('data-autor') || '-';
            document.getElementById('info-editorial').textContent = selectedOption.getAttribute('data-editorial') || '-';
            document.getElementById('info-categoria').textContent = selectedOption.getAttribute('data-categoria') || '-';
            
            libroInfo.style.display = 'block';
        } else {
            libroInfo.style.display = 'none';
        }
    });
});

function limpiarFormulario() {
    if (confirm('¿Estás seguro de que deseas limpiar el formulario?')) {
        document.getElementById('libro_id').value = '';
        document.getElementById('motivo').value = '';
        document.getElementById('libro-info').style.display = 'none';
    }
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

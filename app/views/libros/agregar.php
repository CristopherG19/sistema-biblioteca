<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-book-medical text-primary me-2"></i>Agregar Libro</h2>
                    <p class="text-muted mb-0">Añade un nuevo libro al catálogo de la biblioteca</p>
                </div>
                <a href="index.php?page=libros" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>

            <!-- Mostrar errores -->
                            <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
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
                    <form method="POST" action="index.php?page=libros&action=guardar" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Información Principal -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Datos Principales</h6>
                                
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">
                                        <i class="fas fa-heading me-1"></i>Título *
                                    </label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" 
                                           value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="autor" class="form-label">
                                        <i class="fas fa-user-edit me-1"></i>Autor *
                                    </label>
                                    <input type="text" class="form-control" id="autor" name="autor" 
                                           value="<?php echo isset($_POST['autor']) ? htmlspecialchars($_POST['autor']) : ''; ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="editorial" class="form-label">
                                        <i class="fas fa-building me-1"></i>Editorial *
                                    </label>
                                    <input type="text" class="form-control" id="editorial" name="editorial" 
                                           value="<?php echo isset($_POST['editorial']) ? htmlspecialchars($_POST['editorial']) : ''; ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="anio" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Año *
                                    </label>
                                    <input type="number" class="form-control" id="anio" name="anio" 
                                           value="<?php echo isset($_POST['anio']) ? htmlspecialchars($_POST['anio']) : ''; ?>" 
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
                                           value="<?php echo isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : ''; ?>" 
                                           required>
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Código de identificación internacional del libro (debe ser único)</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="stock" class="form-label">
                                        <i class="fas fa-boxes me-1"></i>Stock *
                                    </label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>" 
                                           min="1" required>
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Cantidad de ejemplares disponibles</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="idCategoria" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Categoría *
                                    </label>
                                    <select class="form-select" id="idCategoria" name="idCategoria" required>
                                        <option value="">Seleccione una categoría</option>
                                        <?php 
                                        $categoriaSeleccionada = isset($_POST['idCategoria']) ? $_POST['idCategoria'] : '';
                                        foreach ($categorias as $categoria): 
                                        ?>
                                            <option value="<?php echo $categoria['idCategoria']; ?>" 
                                                    <?php echo ($categoriaSeleccionada == $categoria['idCategoria']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>Descripción
                                    </label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                                              placeholder="Descripción breve del libro (opcional)"><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Campo opcional para agregar información adicional</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Archivo Digital -->
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-file-pdf me-2"></i>Archivo Digital (Opcional)
                                </h6>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Información:</strong> Puedes subir la versión digital del libro en formato PDF. 
                                    El sistema automáticamente contará las páginas y calculará el tamaño del archivo.
                                    <br><small>Tamaño máximo: 50MB | Formato permitido: PDF</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="archivo_pdf" class="form-label">
                                        <i class="fas fa-upload me-1"></i>Seleccionar archivo PDF
                                    </label>
                                    <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" 
                                           accept=".pdf" onchange="mostrarInfoArchivo(this)">
                                    <div class="form-text">
                                        <small>Selecciona un archivo PDF para la versión digital del libro</small>
                                    </div>
                                </div>
                                
                                <!-- Información del archivo seleccionado -->
                                <div id="infoArchivo" class="alert alert-light d-none">
                                    <h6><i class="fas fa-file-pdf text-danger me-2"></i>Archivo seleccionado:</h6>
                                    <div id="detallesArchivo"></div>
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
                                <i class="fas fa-save me-2"></i>Guardar Libro
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
    const isbnInput = document.getElementById('isbn');
    const form = document.querySelector('form');
    
    // Validación en tiempo real del ISBN
    isbnInput.addEventListener('input', function() {
        let isbn = this.value.replace(/[-\s]/g, ''); // Remover guiones y espacios
        
        if (isbn.length === 10 || isbn.length === 13) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (isbn.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Formatear automáticamente el ISBN
    isbnInput.addEventListener('blur', function() {
        let isbn = this.value.replace(/[-\s]/g, '');
        if (isbn.length === 10) {
            // Formato ISBN-10: XXX-X-XXX-XXXXX-X
            this.value = isbn.substring(0,3) + '-' + isbn.substring(3,4) + '-' + 
                        isbn.substring(4,7) + '-' + isbn.substring(7,12) + '-' + isbn.substring(12);
        } else if (isbn.length === 13) {
            // Formato ISBN-13: XXX-X-XX-XXXXXX-X
            this.value = isbn.substring(0,3) + '-' + isbn.substring(3,4) + '-' + 
                        isbn.substring(4,6) + '-' + isbn.substring(6,12) + '-' + isbn.substring(12);
        }
    });
});
</script>

<script>
function mostrarInfoArchivo(input) {
    const infoDiv = document.getElementById('infoArchivo');
    const detallesDiv = document.getElementById('detallesArchivo');
    
    if (input.files && input.files[0]) {
        const archivo = input.files[0];
        
        // Validar que sea PDF
        if (archivo.type !== 'application/pdf') {
            alert('Por favor selecciona un archivo PDF válido.');
            input.value = '';
            infoDiv.classList.add('d-none');
            return;
        }
        
        // Validar tamaño (50MB máximo)
        const tamañoMaximo = 50 * 1024 * 1024; // 50MB en bytes
        if (archivo.size > tamañoMaximo) {
            alert('El archivo es demasiado grande. El tamaño máximo es 50MB.');
            input.value = '';
            infoDiv.classList.add('d-none');
            return;
        }
        
        // Mostrar información del archivo
        const tamañoMB = (archivo.size / (1024 * 1024)).toFixed(2);
        detallesDiv.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Nombre:</strong> ${archivo.name}<br>
                    <strong>Tamaño:</strong> ${tamañoMB} MB
                </div>
                <div class="col-md-6">
                    <strong>Tipo:</strong> ${archivo.type}<br>
                    <strong>Última modificación:</strong> ${new Date(archivo.lastModified).toLocaleDateString()}
                </div>
            </div>
        `;
        
        infoDiv.classList.remove('d-none');
    } else {
        infoDiv.classList.add('d-none');
    }
}

// JavaScript para manejo de archivos PDF
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>

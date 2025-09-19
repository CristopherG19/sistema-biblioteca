<?php
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-user-edit text-primary me-2"></i>Editar Usuario</h2>
                    <p class="text-muted mb-0">Modifica la información del usuario: <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong> (@<?php echo htmlspecialchars($usuario['usuario']); ?>)</p>
                </div>
                <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios&action=actualizar">
                        <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">
                        
                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Datos Personales</h6>
                                
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nombre Completo *
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : htmlspecialchars($usuario['nombre']); ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="usuario" class="form-label">
                                        <i class="fas fa-at me-1"></i>Nombre de Usuario *
                                    </label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" 
                                           value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : htmlspecialchars($usuario['usuario']); ?>" 
                                           required minlength="3">
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Mínimo 3 caracteres, usado para iniciar sesión</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($usuario['email']); ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Teléfono *
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : htmlspecialchars($usuario['telefono']); ?>" 
                                           required>
                                </div>
                            </div>

                            <!-- Información de Sistema -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-cog me-2"></i>Configuración del Sistema</h6>
                                
                                <div class="mb-3">
                                    <label for="rol" class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>Rol del Sistema *
                                    </label>
                                    <select class="form-select" id="rol" name="rol" required>
                                        <option value="">Seleccione un rol</option>
                                        <?php 
                                        $rolActual = isset($_POST['rol']) ? $_POST['rol'] : $usuario['rol'];
                                        foreach ($roles as $rol): 
                                        ?>
                                            <option value="<?php echo $rol['idRol']; ?>" 
                                                    <?php echo ($rolActual == $rol['idRol']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($rol['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        <small>
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Bibliotecario:</strong> Acceso completo al sistema<br>
                                            <strong>Lector:</strong> Acceso limitado para préstamos
                                        </small>
                                    </div>
                                </div>

                                <!-- Información del registro -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-info-circle me-1"></i>Información del Registro
                                    </label>
                                    <div class="bg-light p-3 rounded">
                                        <small>
                                            <strong>ID:</strong> <?php echo $usuario['idUsuario']; ?><br>
                                            <strong>Usuario:</strong> @<?php echo $usuario['usuario']; ?><br>
                                            <strong>Registrado:</strong> <?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?>
                                        </small>
                                    </div>
                                </div>

                                <hr>

                                <!-- Cambiar contraseña (opcional) -->
                                <h6 class="text-warning mb-3"><i class="fas fa-key me-2"></i>Cambiar Contraseña (Opcional)</h6>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Nueva Contraseña
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               minlength="6" placeholder="Dejar vacío para mantener la actual">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <small><i class="fas fa-shield-alt me-1"></i>Mínimo 6 caracteres (opcional)</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Confirmar Nueva Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6" placeholder="Confirmar nueva contraseña">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <hr>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=usuarios" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-undo me-2"></i>Restaurar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const password = document.getElementById('password');
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
});

// Validar que las contraseñas coincidan (solo si se ingresa una nueva contraseña)
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    // Solo validar si se ingresó una contraseña
    if (password.length > 0 || confirmPassword.length > 0) {
        if (password !== confirmPassword) {
            this.setCustomValidity('Las contraseñas no coinciden');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// También validar cuando se cambia la contraseña principal
document.getElementById('password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    confirmPassword.dispatchEvent(new Event('input'));
});
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>
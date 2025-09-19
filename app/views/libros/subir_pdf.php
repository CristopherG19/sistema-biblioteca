<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Subir/Actualizar PDF - <?php echo htmlspecialchars($libro['titulo']); ?></h5>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $libro['idLibro']; ?>">
                <div class="mb-3">
                    <label for="archivo_pdf" class="form-label">Archivo PDF</label>
                    <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf" required>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" type="submit">Subir PDF</button>
                    <a href="/SISTEMA_BIBLIOTECA/public/index.php?page=libros&action=detalle&id=<?php echo $libro['idLibro']; ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
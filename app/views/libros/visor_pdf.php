<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($libro['titulo']); ?> - Visor PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .pdf-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .pdf-viewer-container {
            height: calc(100vh - 80px);
            position: relative;
            overflow: hidden;
        }
        
        .pdf-embed {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
        }
        
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .toolbar {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 20px;
            display: flex;
            justify-content: between;
            align-items: center;
            gap: 15px;
        }
        
        .book-info {
            flex: 1;
        }
        
        .viewer-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                gap: 10px;
            }
            
            .viewer-controls {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="pdf-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>
                        <?php echo htmlspecialchars($libro['titulo']); ?>
                    </h5>
                    <small class="opacity-75">
                        por <?php echo htmlspecialchars($libro['autor']); ?>
                    </small>
                </div>
                <div>
                    <a href="index.php?page=libros&action=detalle&id=<?php echo $libro['idLibro']; ?>" 
                       class="btn btn-light btn-sm me-2">
                        <i class="fas fa-info-circle me-1"></i>Detalles
                    </a>
                    <a href="index.php?page=libros" 
                       class="btn btn-outline-light btn-sm">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
        <div class="book-info">
            <strong><?php echo htmlspecialchars($libro['titulo']); ?></strong>
            <span class="text-muted">- <?php echo htmlspecialchars($libro['autor']); ?></span>
            <?php if (!empty($libro['numero_paginas'])): ?>
                <span class="badge bg-secondary ms-2">
                    <?php echo $libro['numero_paginas']; ?> páginas
                </span>
            <?php endif; ?>
        </div>
        
        <div class="viewer-controls">
            <button class="btn btn-outline-primary btn-sm" onclick="zoomOut()">
                <i class="fas fa-search-minus"></i>
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="zoomIn()">
                <i class="fas fa-search-plus"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm" onclick="fullscreen()">
                <i class="fas fa-expand"></i>
            </button>
            <a href="<?php echo $urlPDF; ?>" 
               class="btn btn-outline-success btn-sm" 
               download="<?php echo htmlspecialchars($libro['titulo'] . '.pdf'); ?>">
                <i class="fas fa-download"></i>
            </a>
        </div>
    </div>

    <!-- Visor PDF -->
    <div class="pdf-viewer-container" id="pdfContainer">
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando PDF...</p>
        </div>
        
        <!-- PDF.js Canvas Viewer -->
        <canvas id="pdfCanvas" style="border: 1px solid #ccc; width: 100%; display: none;"></canvas>
        
        <!-- Controles de navegación -->
        <div id="pdfControls" class="text-center p-3" style="display: none; background: #f8f9fa; border-top: 1px solid #dee2e6;">
            <button id="prevPage" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-chevron-left"></i> Anterior
            </button>
            <span>Página <span id="pageNum">1</span> de <span id="pageCount">--</span></span>
            <button id="nextPage" class="btn btn-outline-secondary btn-sm ms-2">
                Siguiente <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <!-- Fallback para navegadores sin soporte -->
        <div id="pdfFallback" class="text-center p-5" style="display: none;">
            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
            <h4>No se puede mostrar el PDF</h4>
            <p class="text-muted">El visor PDF no está disponible en tu navegador.</p>
            <div class="d-grid gap-2 col-6 mx-auto">
                <a href="<?php echo $urlPDF; ?>" 
                   class="btn btn-primary" 
                   target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>Abrir en nueva pestaña
                </a>
                <a href="<?php echo $urlPDF; ?>" 
                   class="btn btn-outline-primary" 
                   download="<?php echo htmlspecialchars($libro['titulo'] . '.pdf'); ?>">
                    <i class="fas fa-download me-2"></i>Descargar PDF
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configurar PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.2;
        let canvas = document.getElementById('pdfCanvas');
        let ctx = canvas.getContext('2d');
        
        // Cargar el PDF
        const pdfUrl = '<?php echo $urlPDF; ?>';
        
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('pageCount').textContent = pdfDoc.numPages;
            
            // Ocultar spinner y mostrar controles
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('pdfCanvas').style.display = 'block';
            document.getElementById('pdfControls').style.display = 'block';
            
            // Renderizar primera página
            renderPage(pageNum);
        }).catch(function(error) {
            console.error('Error cargando PDF:', error);
            showError();
        });
        
        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
            
            document.getElementById('pageNum').textContent = num;
        }
        
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }
        
        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }
        
        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }
        
        // Event listeners
        document.getElementById('prevPage').addEventListener('click', onPrevPage);
        document.getElementById('nextPage').addEventListener('click', onNextPage);
        
        let currentZoom = 100;
        
        function hideLoing() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }
        
        function showError() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('pdfCanvas').style.display = 'none';
            document.getElementById('pdfControls').style.display = 'none';
            document.getElementById('pdfFallback').style.display = 'block';
        }
        
        function zoomIn() {
            scale += 0.25;
            renderPage(pageNum);
        }
        
        function zoomOut() {
            if (scale > 0.5) {
                scale -= 0.25;
                renderPage(pageNum);
            }
        }
        
        function updateZoom() {
            renderPage(pageNum);
        }
        
        function fullscreen() {
            const container = document.getElementById('pdfContainer');
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        }
        
        // Registrar tiempo de lectura cada minuto
        setInterval(() => {
            fetch('/SISTEMA_BIBLIOTECA/app/utils/registrar_lectura.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    idLibro: <?php echo $libro['idLibro']; ?>,
                    tiempo: 1
                })
            }).catch(e => console.log('Error registrando lectura:', e));
        }, 60000); // 60 segundos
    </script>
</body>
</html>

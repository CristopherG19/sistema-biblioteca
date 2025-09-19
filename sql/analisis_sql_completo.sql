-- =====================================================
-- ANÁLISIS COMPLETO DE SENTENCIAS SQL EN EL PROYECTO
-- Sistema de Biblioteca - Migración a Procedimientos Almacenados
-- =====================================================

/*
RESUMEN DEL ANÁLISIS:

1. MODELOS CON SENTENCIAS SQL DIRECTAS:
   - Usuario.php: ✅ Parcialmente migrado (algunos métodos usan SP, otros no)
   - Libro.php: ✅ Parcialmente migrado (algunos métodos usan SP, otros no)  
   - Prestamo.php: ❌ NO migrado - Todas las consultas son directas
   - Categoria.php: ✅ Completamente migrado a SP
   - SolicitudPrestamo.php: ✅ Completamente migrado a SP

2. CONTROLADORES CON CONSULTAS SQL DIRECTAS:
   - LibrosController.php: ❌ Contiene 1 consulta SQL directa (registro de lecturas)

3. PROCEDIMIENTOS ALMACENADOS NECESARIOS:

PARA USUARIO (faltantes):
- sp_usuario_obtener_por_id
- sp_usuario_buscar
- sp_usuario_verificar_existe
- sp_usuario_verificar_email
- sp_usuario_estadisticas
- sp_usuario_obtener_por_username
- sp_usuario_actualizar_ultimo_acceso

PARA LIBRO (faltantes):
- sp_libro_obtener_por_id
- sp_libro_actualizar_pdf
- sp_libro_obtener_disponibles
- sp_libro_buscar_por_titulo_autor
- sp_libro_verificar_isbn_existe
- sp_libro_obtener_con_prestamos
- sp_libro_registrar_lectura

PARA PRESTAMO (todos faltantes):
- sp_prestamo_obtener_todos
- sp_prestamo_obtener_por_usuario
- sp_prestamo_obtener_activos
- sp_prestamo_obtener_vencidos
- sp_prestamo_insertar_completo
- sp_prestamo_devolver_completo
- sp_prestamo_obtener_estadisticas
- sp_prestamo_obtener_por_libro
- sp_prestamo_obtener_usuario_libro
- sp_prestamo_validar_disponibilidad
- sp_prestamo_actualizar_observaciones
- sp_prestamo_eliminar

PARA AMPLIACIONES (todos nuevos):
- sp_ampliacion_solicitar
- sp_ampliacion_obtener_solicitudes  
- sp_ampliacion_aprobar
- sp_ampliacion_rechazar

TOTAL: 28 procedimientos almacenados necesarios
*/
# 📊 REPORTE: SENTENCIAS SQL vs PROCEDIMIENTOS ALMACENADOS

**Fecha del Análisis:** 18 de septiembre de 2025  
**Base de Datos:** biblioteca_db  
**Sistema:** Sistema de Biblioteca

---

## 🔍 **ANÁLISIS DE MODELOS - SENTENCIAS SQL DIRECTAS**

### **📄 CATEGORIA.PHP**
**Sentencias SQL directas encontradas:**
1. **getById()** - Línea 14:
   ```sql
   SELECT * FROM Categorias WHERE idCategoria = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_categoria_obtener_por_id`

### **📄 LIBRO.PHP**
**Sentencias SQL directas encontradas:**
1. **getById() (fallback)** - Línea 27:
   ```sql
   SELECT l.*, c.nombre as categoria FROM Libros l JOIN Categorias c ON l.idCategoria = c.idCategoria WHERE l.idLibro = ?
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_libro_obtener_por_id` (fallback implementado)

2. **insertarConPDF() - lastInsertId()** - Línea 77:
   ```php
   $conexion->lastInsertId()
   ```
   **Estado:** ❌ **FUNCIONALIDAD NO IMPLEMENTADA EN PROCEDIMIENTO**

3. **actualizarCampos() - UPDATE dinámico** - Línea 141:
   ```sql
   UPDATE Libros SET [campos_dinamicos] WHERE idLibro = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_libro_actualizar_campos`

4. **actualizarStockPrestamo()** - Línea 208:
   ```sql
   UPDATE Libros SET disponible = disponible - 1 WHERE idLibro = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_libro_actualizar_stock_prestamo`

5. **actualizarStockDevolucion()** - Línea 220:
   ```sql
   UPDATE Libros SET disponible = disponible + 1 WHERE idLibro = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_libro_actualizar_stock_devolucion`

6. **getConPrestamos() (fallback)** - Línea 241:
   ```sql
   SELECT l.*, c.nombre as categoria,
          COALESCE(COUNT(p.idPrestamo), 0) as total_prestamos,
          COALESCE(SUM(CASE WHEN p.fechaDevolucionReal IS NULL THEN 1 ELSE 0 END), 0) as prestamos_activos
   FROM Libros l 
   INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
   LEFT JOIN Prestamos p ON l.idLibro = p.idLibro
   GROUP BY l.idLibro
   ORDER BY l.titulo
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_libro_obtener_con_prestamos` (fallback implementado)

7. **getRecientes()** - Línea 260:
   ```sql
   SELECT l.*, c.nombre as categoria 
   FROM Libros l 
   INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
   ORDER BY l.idLibro DESC 
   LIMIT ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_libro_obtener_recientes`

8. **getDisponibles() (fallback)** - Línea 177:
   ```sql
   SELECT l.*, c.nombre as categoria 
   FROM Libros l 
   INNER JOIN Categorias c ON l.idCategoria = c.idCategoria 
   WHERE l.disponible > 0
   ORDER BY l.titulo
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_libro_obtener_disponibles` (fallback implementado)

### **📄 PRESTAMO.PHP**
**Sentencias SQL directas encontradas:**
1. **getAll() (fallback)** - Línea 24:
   ```sql
   SELECT p.*, 
          u.nombre as usuario_nombre, u.email as usuario_email,
          l.titulo as libro_titulo, l.autor as libro_autor,
          c.nombre as categoria_nombre
   FROM Prestamos p
   INNER JOIN Usuarios u ON p.idUsuario = u.idUsuario
   INNER JOIN Libros l ON p.idLibro = l.idLibro  
   INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
   ORDER BY p.fechaPrestamo DESC
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_prestamo_obtener_todos` (fallback implementado)

2. **getById()** - Línea 309:
   ```sql
   SELECT * FROM Prestamos WHERE idPrestamo = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_prestamo_obtener_por_id`

### **📄 SOLICITUDPRESTAMO.PHP**
**Sentencias SQL directas encontradas:**
1. **responder()** - Línea 61:
   ```sql
   UPDATE solicitudes_prestamo 
   SET estado = ?, bibliotecario_id = ?, observaciones_bibliotecario = ?, fecha_respuesta = NOW()
   WHERE idSolicitud = ? AND estado = 'Pendiente'
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_solicitud_responder`

2. **getEstadisticasUsuario()** - Línea 118:
   ```sql
   SELECT
       COUNT(*) as total_solicitudes,
       SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
       SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
       SUM(CASE WHEN estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
       SUM(CASE WHEN estado = 'Convertida' THEN 1 ELSE 0 END) as convertidas
   FROM solicitudes_prestamo
   WHERE usuario_id = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_solicitudes_estadisticas_usuario`

3. **getById()** - Línea 147:
   ```sql
   SELECT
       s.*,
       u.nombre as usuario_nombre,
       u.email as usuario_email,
       l.titulo as libro_titulo,
       l.autor as libro_autor
   FROM solicitudes_prestamo s
   INNER JOIN Usuarios u ON s.usuario_id = u.idUsuario
   INNER JOIN Libros l ON s.libro_id = l.idLibro
   WHERE s.idSolicitud = ?
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_solicitud_obtener_por_id`

4. **cancelar()** - Línea 172:
   ```sql
   UPDATE solicitudes_prestamo 
   SET estado = 'Rechazada', 
       observaciones_bibliotecario = 'Cancelada por el usuario',
       fecha_respuesta = NOW()
   WHERE idSolicitud = ? 
   AND usuario_id = ? 
   AND estado = 'Pendiente'
   ```
   **Estado:** ❌ **NO TIENE PROCEDIMIENTO EQUIVALENTE**
   **Procedimiento requerido:** `sp_solicitud_cancelar`

5. **getLibrosDisponiblesDirect() (fallback)** - Línea 208:
   ```sql
   SELECT 
       l.idLibro,
       l.titulo,
       l.autor,
       l.editorial,
       l.anio,
       l.isbn,
       l.disponible,
       l.descripcion,
       c.nombre as categoria
   FROM Libros l
   INNER JOIN Categorias c ON l.idCategoria = c.idCategoria
   WHERE l.disponible > 0
   ORDER BY l.titulo
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_libros_disponibles_solicitud` (fallback implementado)

### **📄 USUARIO.PHP**
**Sentencias SQL directas encontradas:**
1. **getAll() (fallback)** - Línea 25:
   ```sql
   SELECT u.*, r.nombre as rol_nombre 
   FROM Usuarios u 
   INNER JOIN Roles r ON u.rol = r.idRol 
   ORDER BY u.nombre
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_listar_usuarios` (fallback implementado)

2. **getById() (fallback)** - Línea 48:
   ```sql
   SELECT u.*, r.nombre as rol_nombre 
   FROM Usuarios u 
   INNER JOIN Roles r ON u.rol = r.idRol 
   WHERE u.idUsuario = ?
   ```
   **Estado:** ✅ **TIENE PROCEDIMIENTO:** `sp_usuario_obtener_por_id` (fallback implementado)

---

## 📋 **PROCEDIMIENTOS ALMACENADOS EXISTENTES EN LA BD**

### **✅ PROCEDIMIENTOS INSTALADOS (44 total):**

#### **🏷️ CATEGORÍAS (4)**
- `sp_actualizar_categoria`
- `sp_eliminar_categoria`
- `sp_insertar_categoria`
- `sp_listar_categorias`

#### **📚 LIBROS (4)**
- `sp_actualizar_libro`
- `sp_eliminar_libro`
- `sp_insertar_libro`
- `sp_listar_libros`

#### **📚 LIBROS ESPECÍFICOS (4)**
- `sp_libro_obtener_con_prestamos`
- `sp_libro_obtener_disponibles`
- `sp_libro_obtener_por_id`
- `sp_libro_registrar_lectura`

#### **👥 USUARIOS (4)**
- `sp_actualizar_usuario`
- `sp_eliminar_usuario`
- `sp_insertar_usuario`
- `sp_listar_usuarios`

#### **👥 USUARIOS ESPECÍFICOS (2)**
- `sp_usuario_estadisticas`
- `sp_usuario_obtener_por_id`

#### **🔄 PRÉSTAMOS (4)**
- `sp_actualizar_prestamo`
- `sp_eliminar_prestamo`
- `sp_insertar_prestamo`
- `sp_listar_prestamos`

#### **🔄 PRÉSTAMOS ESPECÍFICOS (2)**
- `sp_prestamo_obtener_estadisticas`
- `sp_prestamo_obtener_todos`

#### **📋 SOLICITUDES (5)**
- `sp_libros_disponibles_solicitud`
- `sp_solicitudes_estadisticas`
- `sp_solicitudes_listar`
- `sp_solicitudes_usuario`
- `sp_solicitud_aprobar_y_crear_prestamo`
- `sp_solicitud_insertar`

#### **🔧 ROLES (2)**
- `sp_actualizar_rol`
- `sp_eliminar_rol`
- `sp_insertar_rol`
- `sp_listar_roles`

#### **📖 FUNCIONALIDADES AVANZADAS (11)**
- `sp_actualizar_multa`
- `sp_eliminar_historial_lectura`
- `sp_eliminar_interes_usuario`
- `sp_eliminar_libro_favorito`
- `sp_eliminar_multa`
- `sp_insertar_historial_lectura`
- `sp_insertar_interes_usuario`
- `sp_insertar_libro_favorito`
- `sp_insertar_multa`
- `sp_listar_historial_lectura`
- `sp_listar_intereses_usuario`
- `sp_listar_libros_favoritos`

#### **🧪 TESTING (1)**
- `sp_test_simple`

---

## ⚠️ **PROCEDIMIENTOS ALMACENADOS FALTANTES**

### **❌ PROCEDIMIENTOS QUE DEBERÍAN EXISTIR (16 faltantes):**

#### **🏷️ CATEGORÍAS:**
1. `sp_categoria_obtener_por_id`

#### **📚 LIBROS:**
2. `sp_libro_actualizar_campos`
3. `sp_libro_actualizar_stock_prestamo`
4. `sp_libro_actualizar_stock_devolucion`
5. `sp_libro_obtener_recientes`
6. `sp_libro_buscar_por_titulo_autor`
7. `sp_libro_verificar_isbn_existe`
8. `sp_libro_actualizar_pdf`

#### **🔄 PRÉSTAMOS:**
9. `sp_prestamo_obtener_por_id`
10. `sp_prestamo_obtener_por_usuario`
11. `sp_prestamo_obtener_activos`
12. `sp_prestamo_obtener_vencidos`
13. `sp_prestamo_insertar_completo`
14. `sp_prestamo_devolver_completo`
15. `sp_prestamo_obtener_por_libro`
16. `sp_prestamo_obtener_usuario_libro`
17. `sp_prestamo_validar_disponibilidad`
18. `sp_prestamo_actualizar_observaciones`

#### **🔧 AMPLIACIONES:**
19. `sp_ampliacion_solicitar`
20. `sp_ampliacion_obtener_solicitudes`
21. `sp_ampliacion_aprobar`
22. `sp_ampliacion_rechazar`

#### **📋 SOLICITUDES:**
23. `sp_solicitud_responder`
24. `sp_solicitudes_estadisticas_usuario`
25. `sp_solicitud_obtener_por_id`
26. `sp_solicitud_cancelar`

#### **👥 USUARIOS:**
27. `sp_usuario_obtener_por_rol`
28. `sp_usuario_buscar`
29. `sp_usuario_verificar_existe`
30. `sp_usuario_verificar_email`
31. `sp_usuario_obtener_por_username`
32. `sp_usuario_actualizar_ultimo_acceso`

---

## 📊 **RESUMEN ESTADÍSTICO**

### **📈 ESTADO ACTUAL:**
- **Total de procedimientos existentes:** 44
- **Total de procedimientos requeridos:** 76
- **Procedimientos faltantes:** 32
- **Cobertura actual:** 58% (44/76)

### **🔍 SENTENCIAS SQL DIRECTAS:**
- **Total de sentencias SQL directas encontradas:** 18
- **Sentencias con fallback implementado:** 6
- **Sentencias sin procedimiento equivalente:** 12

### **⚠️ IMPACTO:**
- **Modelos con fallbacks:** 3 (Libro.php, Prestamo.php, Usuario.php)
- **Modelos sin fallbacks:** 2 (Categoria.php, SolicitudPrestamo.php)
- **Funcionalidades en riesgo:** 12 consultas críticas

### **🎯 PRIORIDADES DE IMPLEMENTACIÓN:**

#### **🚨 CRÍTICAS (Implementar primero):**
1. `sp_categoria_obtener_por_id`
2. `sp_solicitud_responder`
3. `sp_solicitud_obtener_por_id`
4. `sp_prestamo_obtener_por_id`

#### **⚡ IMPORTANTES (Implementar segundo):**
5. `sp_libro_obtener_recientes`
6. `sp_libro_actualizar_stock_prestamo`
7. `sp_libro_actualizar_stock_devolucion`
8. `sp_prestamo_insertar_completo`
9. `sp_prestamo_devolver_completo`

#### **🔧 OPCIONALES (Implementar después):**
10. Resto de procedimientos para funcionalidades avanzadas

---

## 📝 **RECOMENDACIONES**

### **✅ ACCIONES INMEDIATAS:**
1. **Crear los 4 procedimientos críticos** para evitar errores en funcionalidades básicas
2. **Implementar fallbacks** en Categoria.php y SolicitudPrestamo.php
3. **Validar** que todos los procedimientos existentes funcionen correctamente

### **🔄 ACCIONES A MEDIANO PLAZO:**
1. **Completar migración** de todas las sentencias SQL a procedimientos almacenados
2. **Eliminar fallbacks** una vez que todos los procedimientos estén implementados
3. **Optimizar rendimiento** de consultas complejas mediante procedimientos

### **🛡️ MEDIDAS DE SEGURIDAD:**
1. **Mantener fallbacks** hasta completar la migración total
2. **Implementar logging** detallado para monitorear el uso de fallbacks
3. **Crear tests** para validar equivalencia entre SQL directo y procedimientos

---

**🎯 CONCLUSIÓN:** El sistema tiene una base sólida de procedimientos almacenados (58% de cobertura), pero requiere completar la migración de 32 procedimientos faltantes para alcanzar una arquitectura completamente basada en stored procedures.

*Reporte generado automáticamente - Sistema de Biblioteca*  
*Fecha: 18 de septiembre de 2025*
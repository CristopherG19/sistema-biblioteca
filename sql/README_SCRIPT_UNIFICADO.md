# 📚 Script SQL Unificado - Sistema de Gestión Bibliotecaria

## 🎯 Descripción

Este documento describe el script SQL unificado `sistema_biblioteca_completo.sql` que contiene toda la estructura de base de datos, procedimientos almacenados y configuraciones necesarias para el sistema bibliotecario.

## 📁 Archivos Consolidados

El script unificado reemplaza y consolida los siguientes archivos:

### Archivos Originales Consolidados:
- ✅ `schema_phpmyadmin.sql` - Esquema base de tablas
- ✅ `procedimientos_almacenados_completos.sql` - Procedimientos almacenados
- ✅ `solicitudes_prestamo.sql` - Tabla y procedimientos de solicitudes
- ✅ `solicitudes_ampliacion.sql` - Tabla de ampliaciones
- ✅ `procedimientos_criticos_fase1.sql` - Procedimientos críticos
- ✅ `procedimientos_importantes_fase2.sql` - Procedimientos importantes
- ✅ `procedimientos_restantes_fase3.sql` - Procedimientos restantes
- ✅ `sp_libro_obtener_por_isbn.sql` - Procedimiento específico de libros

## 🏗️ Estructura del Script Unificado

### 1. **Creación de Base de Datos** (Sección 1)
```sql
DROP DATABASE IF EXISTS biblioteca_db;
CREATE DATABASE biblioteca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. **Tablas Principales** (Sección 2)
- `Roles` - Roles del sistema (Bibliotecario, Lector)
- `Usuarios` - Información de usuarios
- `Categorias` - Categorías de libros
- `Libros` - Catálogo de libros
- `Prestamos` - Registro de préstamos

### 3. **Tablas de Funcionalidades Avanzadas** (Sección 3)
- `solicitudes_prestamo` - Solicitudes de préstamo por lectores
- `SolicitudesAmpliacion` - Solicitudes de ampliación de préstamos
- `Multas` - Sistema de multas
- `HistorialLectura` - Historial de lectura de usuarios
- `LibrosFavoritos` - Libros favoritos por usuario
- `InteresesUsuario` - Categorías de interés por usuario

### 4. **Datos Iniciales** (Sección 4)
- Roles del sistema
- Categorías de ejemplo
- Usuario administrador por defecto

### 5. **Procedimientos Almacenados** (Secciones 5-9)

#### **Usuarios** (Sección 5)
- `sp_usuario_obtener_por_id`
- `sp_usuario_buscar`
- `sp_usuario_verificar_existe`
- `sp_usuario_verificar_email`
- `sp_usuario_estadisticas`
- `sp_usuario_obtener_por_username`
- `sp_usuario_actualizar_ultimo_acceso`
- `sp_usuario_obtener_por_rol`

#### **Libros** (Sección 6)
- `sp_libro_obtener_por_id`
- `sp_libro_actualizar_pdf`
- `sp_libro_obtener_disponibles`
- `sp_libro_buscar_por_titulo_autor`
- `sp_libro_verificar_isbn_existe`
- `sp_libro_obtener_con_prestamos`
- `sp_libro_registrar_lectura`

#### **Préstamos** (Sección 7)
- `sp_prestamo_obtener_todos`
- `sp_prestamo_obtener_por_usuario`
- `sp_prestamo_obtener_activos`
- `sp_prestamo_obtener_vencidos`
- `sp_prestamo_insertar_completo`
- `sp_prestamo_devolver_completo`
- `sp_prestamo_obtener_estadisticas`
- `sp_prestamo_obtener_por_id`

#### **Solicitudes de Préstamo** (Sección 8)
- `sp_solicitudes_listar`
- `sp_solicitudes_usuario`
- `sp_solicitud_insertar`
- `sp_solicitud_responder`
- `sp_solicitud_aprobar_y_crear_prestamo`
- `sp_solicitudes_estadisticas`

#### **Ampliaciones** (Sección 9)
- `sp_ampliacion_solicitar`
- `sp_ampliacion_obtener_solicitudes`
- `sp_ampliacion_aprobar`
- `sp_ampliacion_rechazar`

### 6. **Optimizaciones** (Secciones 10-13)
- **Índices adicionales** para consultas frecuentes
- **Vistas** para consultas complejas
- **Triggers** para mantener consistencia
- **Configuración** de sesión y timezone

## 🚀 Instalación

### Requisitos Previos
- MySQL 5.7+ o MariaDB 10.2+
- phpMyAdmin o cliente MySQL
- Permisos de administrador de base de datos

### Pasos de Instalación

1. **Abrir phpMyAdmin** o cliente MySQL
2. **Ejecutar el script completo** `sistema_biblioteca_completo.sql`
3. **Verificar la instalación:**
   ```sql
   SHOW TABLES;
   SHOW PROCEDURE STATUS WHERE Db = 'biblioteca_db';
   ```
4. **Configurar la conexión** en `config/database.php`

### Verificación de Instalación

```sql
-- Verificar tablas creadas
SELECT COUNT(*) as total_tablas FROM information_schema.tables 
WHERE table_schema = 'biblioteca_db';

-- Verificar procedimientos almacenados
SELECT COUNT(*) as total_procedimientos FROM information_schema.routines 
WHERE routine_schema = 'biblioteca_db' AND routine_type = 'PROCEDURE';

-- Verificar datos iniciales
SELECT * FROM Roles;
SELECT * FROM Categorias;
SELECT COUNT(*) as total_usuarios FROM Usuarios;
```

## 📊 Características del Script

### ✅ **Ventajas del Script Unificado**

1. **Instalación en un solo paso** - No requiere ejecutar múltiples archivos
2. **Consistencia garantizada** - Todas las dependencias están en orden
3. **Optimizado** - Índices y configuraciones optimizadas
4. **Documentado** - Comentarios detallados en cada sección
5. **Mantenible** - Estructura clara y organizada
6. **Escalable** - Preparado para futuras expansiones

### 🔧 **Mejoras Implementadas**

1. **Índices optimizados** para consultas frecuentes
2. **Vistas** para simplificar consultas complejas
3. **Triggers** para mantener consistencia de datos
4. **Campos de auditoría** (fecha_creacion, fecha_actualizacion)
5. **Validaciones** en procedimientos almacenados
6. **Manejo de errores** mejorado
7. **Codificación UTF-8** completa

### 📈 **Rendimiento**

- **Índices compuestos** para consultas multi-campo
- **Vistas materializadas** para estadísticas frecuentes
- **Procedimientos optimizados** con consultas eficientes
- **Configuración de sesión** optimizada

## 🛠️ Mantenimiento

### Actualizaciones Futuras

Para actualizar el sistema:

1. **Hacer backup** de la base de datos actual
2. **Ejecutar solo las secciones** que necesiten actualización
3. **Verificar** que no hay conflictos
4. **Probar** funcionalidades afectadas

### Backup Recomendado

```sql
-- Crear backup antes de cambios importantes
mysqldump -u usuario -p biblioteca_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 📝 Notas Importantes

1. **El script elimina** la base de datos existente si existe
2. **Incluye datos iniciales** para comenzar a usar el sistema
3. **Configuración UTF-8** para caracteres especiales
4. **Triggers automáticos** para fechas de actualización
5. **Validaciones** en procedimientos almacenados

## 🎯 Próximos Pasos

Después de ejecutar el script:

1. **Configurar** `config/database.php` con credenciales correctas
2. **Probar** la conexión desde la aplicación
3. **Iniciar sesión** con usuario: `admin`, contraseña: `password`
4. **Crear** categorías y libros de prueba
5. **Registrar** usuarios de prueba
6. **Probar** funcionalidades del sistema

---

**¡El sistema está listo para usar!** 🎉

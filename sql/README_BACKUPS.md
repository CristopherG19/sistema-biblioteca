# 📚 Backups de la Base de Datos - Sistema de Biblioteca

## 📁 Archivos de Backup Disponibles

### 🗄️ Backup Completo (Con Datos)
- **Archivo:** `backup_completo_2025-09-30_17-47-31.sql`
- **Contenido:** Estructura + Datos + Procedimientos + Vistas + Triggers
- **Uso:** Restaurar completamente la base de datos
- **Tamaño:** ~0.08 MB

### 🏗️ Backup de Estructura (Sin Datos)
- **Archivo:** `backup_estructura_2025-09-30_17-47-31.sql`
- **Contenido:** Solo estructura + Procedimientos + Vistas + Triggers
- **Uso:** Crear nueva base de datos vacía
- **Tamaño:** ~0.05 MB

### 📚 Archivos de Scripts Originales
- **`sistema_biblioteca_completo.sql`** - Script original de instalación
- **`favoritos_historial.sql`** - Script para funcionalidades de favoritos e historial

## 📊 Contenido del Backup Completo

### 📋 Tablas (15)
1. `categorias` - Categorías de libros
2. `favoritos` - Libros favoritos de usuarios
3. `historial_actividad` - Registro de actividades
4. `historiallectura` - Historial de lectura
5. `interesesusuario` - Intereses de usuarios
6. `libros` - Catálogo de libros
7. `librosfavoritos` - Tabla de favoritos (legacy)
8. `multas` - Sistema de multas
9. `prestamos` - Préstamos de libros
10. `roles` - Roles de usuario
11. `solicitudes_prestamo` - Solicitudes de préstamo
12. `solicitudesampliacion` - Solicitudes de ampliación
13. `usuarios` - Usuarios del sistema
14. `vista_estadisticas_generales` - Vista de estadísticas
15. `vista_prestamos_activos` - Vista de préstamos activos

### 🔧 Procedimientos Almacenados (73)
- Gestión de usuarios
- Gestión de libros
- Gestión de préstamos
- Gestión de categorías
- Sistema de favoritos
- Sistema de historial
- Reportes y estadísticas
- Validaciones y verificaciones

### 👁️ Vistas (2)
- `vista_estadisticas_generales` - Estadísticas generales del sistema
- `vista_prestamos_activos` - Préstamos actualmente activos

### ⚡ Triggers (0)
- No hay triggers configurados actualmente

## 🚀 Cómo Restaurar el Backup

### Opción 1: Usando MySQL Command Line
```bash
# Backup completo
mysql -u root -p < backup_completo_2025-09-30_17-47-31.sql

# Solo estructura
mysql -u root -p < backup_estructura_2025-09-30_17-47-31.sql
```

### Opción 2: Usando phpMyAdmin
1. Abrir phpMyAdmin
2. Crear nueva base de datos `biblioteca_db`
3. Seleccionar la base de datos
4. Ir a la pestaña "Importar"
5. Seleccionar el archivo `.sql`
6. Hacer clic en "Continuar"

### Opción 3: Usando MySQL Workbench
1. Abrir MySQL Workbench
2. Conectar al servidor MySQL
3. File → Open SQL Script
4. Seleccionar el archivo `.sql`
5. Ejecutar el script

## 📅 Frecuencia de Backups Recomendada

- **Desarrollo:** Diario
- **Producción:** Cada 6 horas
- **Antes de cambios importantes:** Inmediato

## 🔒 Seguridad

- Los backups contienen datos sensibles
- Mantener en ubicación segura
- Considerar encriptación para producción
- No subir a repositorios públicos

## 📝 Notas Importantes

- Los backups incluyen todas las configuraciones de charset (utf8mb4)
- Las claves foráneas se restauran correctamente
- Los índices se recrean automáticamente
- Los procedimientos almacenados mantienen sus permisos

## 🛠️ Generación de Nuevos Backups

Para generar un nuevo backup, ejecutar:
```bash
php crear_backup.php
```

Esto creará automáticamente:
- Backup completo con timestamp
- Backup de estructura con timestamp
- Estadísticas del proceso

---
**Última actualización:** 30 de Septiembre de 2025
**Sistema:** BiblioSys v1.0

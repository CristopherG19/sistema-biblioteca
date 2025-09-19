# 📚 Sistema de Gestión Bibliotecaria - BiblioSys

Un sistema completo de gestión bibliotecaria desarrollado en PHP con arquitectura MVC, que permite administrar libros, usuarios, préstamos y generar reportes detallados.

## 🚀 Características Principales

### 👥 Gestión de Usuarios
- **Registro de usuarios** (Bibliotecarios y Lectores)
- **Autenticación segura** con roles diferenciados
- **Perfiles de usuario** con información completa
- **Gestión de permisos** por rol

### 📖 Gestión de Libros
- **Catálogo completo** de libros con información detallada
- **Sistema de categorías** para organización
- **Búsqueda avanzada** por título, autor, ISBN
- **Control de inventario** con stock disponible
- **Gestión de archivos PDF** para libros digitales

### 📋 Sistema de Préstamos
- **Solicitudes de préstamo** por parte de lectores
- **Aprobación/rechazo** por bibliotecarios
- **Gestión de préstamos directos** por bibliotecarios
- **Sistema de ampliaciones** de duración
- **Devolución de libros** con observaciones
- **Control de vencimientos** y préstamos activos

### 📊 Reportes y Estadísticas
- **Dashboard interactivo** con métricas clave
- **Reportes de préstamos** con filtros por fecha
- **Estadísticas de usuarios** y actividad
- **Análisis del catálogo** de libros
- **Exportación** a Excel, PDF (próximamente)

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 7.4+
- **Base de Datos:** MySQL/MariaDB
- **Arquitectura:** MVC (Model-View-Controller)
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Iconos:** Font Awesome
- **Procedimientos:** Stored Procedures MySQL

## 📁 Estructura del Proyecto

```
SISTEMA_BIBLIOTECA/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/         # Modelos de datos
│   └── views/          # Vistas HTML
├── config/             # Configuración de BD
├── public/             # Punto de entrada público
├── sql/                # Scripts de base de datos
└── assets/             # Recursos estáticos
```

## ⚙️ Instalación

### Requisitos Previos
- PHP 7.4 o superior
- MySQL/MariaDB 5.7+
- Servidor web (Apache/Nginx)
- XAMPP/WAMP (recomendado para desarrollo)

### Pasos de Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/CristopherG19/sistema-biblioteca.git
   ```

2. **Configurar la base de datos:**
   - Crear una base de datos MySQL
   - Ejecutar el script unificado `sql/sistema_biblioteca_completo.sql`
   - Verificar la instalación con `sql/verificar_instalacion.sql`

3. **Configurar la conexión:**
   - Copiar `config/database.example.php` a `config/database.php`
   - Configurar los datos de conexión a la base de datos

4. **Configurar el servidor web:**
   - Apuntar el DocumentRoot a la carpeta `public/`
   - Asegurar que mod_rewrite esté habilitado

## 🎯 Funcionalidades por Rol

### 👨‍💼 Bibliotecario
- Gestión completa de usuarios
- Administración del catálogo de libros
- Aprobación de solicitudes de préstamo
- Gestión de préstamos directos
- Control de ampliaciones de duración
- Generación de reportes
- Dashboard administrativo

### 👤 Lector
- Búsqueda de libros
- Solicitud de préstamos
- Visualización de préstamos activos
- Solicitud de ampliaciones
- Historial de solicitudes
- Dashboard personal

## 📊 Características Técnicas

### Seguridad
- **Autenticación** basada en sesiones
- **Validación** de datos de entrada
- **Prevención** de inyección SQL con PDO
- **Control de acceso** por roles

### Rendimiento
- **Procedimientos almacenados** para consultas complejas
- **Índices** optimizados en la base de datos
- **Caché** de consultas frecuentes
- **Paginación** en listados extensos

### Escalabilidad
- **Arquitectura MVC** modular
- **Separación** de responsabilidades
- **Código reutilizable** y mantenible
- **API REST** preparada para futuras integraciones

## 🔧 Configuración Avanzada

### Variables de Entorno
```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_biblioteca');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### Configuración de Sesiones
```php
// Configuración de sesiones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // Solo en HTTPS
```

## 📈 Roadmap

### Próximas Características
- [ ] **Exportación de reportes** a Excel/PDF
- [ ] **Gráficos interactivos** con Chart.js
- [ ] **Notificaciones** por email
- [ ] **API REST** para integraciones
- [ ] **App móvil** (React Native)
- [ ] **Sistema de multas** automático
- [ ] **Reservas** de libros
- [ ] **Sistema de favoritos**

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Para contribuir:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**CristopherG19**
- GitHub: [@CristopherG19](https://github.com/CristopherG19)

## 📞 Soporte

Si tienes preguntas o necesitas ayuda, puedes:
- Abrir un issue en GitHub
- Contactar al desarrollador

---

⭐ **¡No olvides darle una estrella al proyecto si te ha sido útil!** ⭐

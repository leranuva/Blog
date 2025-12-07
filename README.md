# Modern Blog System 2025

Blog dinámico moderno construido con PHP, JavaScript, CSS y MySQL. Refactorización completa de un blog estático a un sistema dinámico con tecnologías actuales.

## Características

- ✅ Sistema de gestión de contenido dinámico
- ✅ Base de datos MySQL con estructura normalizada
- ✅ Sistema de autenticación y sesiones
- ✅ Panel de administración
- ✅ API REST para comentarios y newsletter
- ✅ JavaScript moderno (ES6+, Fetch API)
- ✅ Diseño responsive
- ✅ Sistema de categorías y tags
- ✅ Comentarios con moderación
- ✅ Búsqueda de posts
- ✅ Paginación
- ✅ Newsletter

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Librerías**: jQuery, Owl Carousel, AOS (Animate On Scroll), Font Awesome

## Instalación

### Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior (o MariaDB)
- Apache con mod_rewrite habilitado
- XAMPP, WAMP, o servidor similar

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   git clone https://github.com/leranuva/Blog.git
   cd blog
   ```

2. **Configurar la base de datos**
   - Abre phpMyAdmin o tu cliente MySQL
   - Importa el archivo `database/schema.sql`
   - O ejecuta el script SQL manualmente

3. **Configurar la conexión**
   - Edita `config/database.php` con tus credenciales:
   ```php
   private $host = 'localhost';
   private $db_name = 'blog_db';
   private $username = 'root';
   private $password = '';
   ```

4. **Configurar la URL base**
   - Edita `config/config.php`:
   ```php
   define('BASE_URL', 'http://localhost:8000');
   ```

5. **Crear directorio de uploads**
   ```bash
   mkdir uploads
   chmod 755 uploads
   ```

6. **Iniciar el servidor**
   ```bash
   php -S localhost:8000
   ```

7. **Acceder al sitio**
   - Frontend: http://localhost:8000
   - Admin: http://localhost:8000/admin
   - Login: http://localhost:8000/login.php
   - Credenciales por defecto: `admin` / `admin123`

## Estructura del Proyecto

```
blog/
├── admin/              # Panel de administración
├── api/               # Endpoints API REST
├── assets/            # Recursos estáticos (imágenes, etc.)
├── config/            # Archivos de configuración
├── css/               # Estilos CSS
├── database/          # Scripts SQL
├── includes/          # Clases PHP y funciones
├── js/                # JavaScript
├── uploads/           # Archivos subidos
├── index.php          # Página principal
├── post.php           # Página de post individual
├── category.php       # Página de categorías
├── login.php          # Página de login
└── logout.php         # Cerrar sesión
```

## Funcionalidades Principales

### Frontend

- **Página Principal**: Lista de posts con paginación
- **Post Individual**: Vista completa del post con comentarios
- **Categorías**: Filtrado por categorías
- **Búsqueda**: Búsqueda de posts (próximamente)
- **Newsletter**: Suscripción al newsletter

### Backend

- **Gestión de Posts**: CRUD completo de posts
- **Categorías**: Gestión de categorías
- **Comentarios**: Sistema de moderación
- **Usuarios**: Sistema de autenticación
- **Estadísticas**: Dashboard con métricas

## API Endpoints

### Comentarios
- `POST /api/comments.php` - Crear comentario
- `GET /api/comments.php?post_id=X` - Obtener comentarios

### Newsletter
- `POST /api/newsletter.php` - Suscribirse al newsletter

## Seguridad

- Protección CSRF con tokens
- Sanitización de inputs
- Prepared statements (PDO)
- Validación de datos
- Protección de archivos sensibles

## Desarrollo

### Agregar un nuevo post

1. Inicia sesión en el panel de administración
2. Ve a "Posts" > "Create New Post"
3. Completa el formulario y guarda

### Personalizar el diseño

- Edita `css/style.css` para estilos principales
- Edita `css/mobile-style.css` para estilos móviles
- Las variables CSS están en `:root` en `style.css`

## Próximas Mejoras

- [ ] Sistema de búsqueda avanzada
- [ ] Editor WYSIWYG para posts
- [ ] Sistema de tags completo
- [ ] Subida de imágenes
- [ ] SEO mejorado
- [ ] Cache de consultas
- [ ] API REST completa
- [ ] PWA (Progressive Web App)

## Licencia

Este proyecto es una plantilla creada por Daily Tuition y refactorizada para uso moderno.

## Créditos

- Plantilla original: Daily Tuition
- Refactorización: 2025

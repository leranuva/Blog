# Guía de Instalación Rápida

## Paso 1: Configurar Base de Datos

1. Abre phpMyAdmin (http://localhost/phpmyadmin)
2. Crea una nueva base de datos llamada `blog_db`
3. Importa el archivo `database/schema.sql`
   - O copia y pega el contenido del archivo en la pestaña SQL

## Paso 2: Configurar Credenciales

Edita `config/database.php` si tus credenciales de MySQL son diferentes:

```php
private $host = 'localhost';
private $db_name = 'blog_db';
private $username = 'root';  // Cambia si es necesario
private $password = '';       // Cambia si es necesario
```

## Paso 3: Configurar URL Base

Edita `config/config.php`:

```php
define('BASE_URL', 'http://localhost:8000');  // O la URL de tu servidor
```

## Paso 4: Verificar Permisos

Asegúrate de que el directorio `uploads/` tenga permisos de escritura:

```bash
chmod 755 uploads
```

## Paso 5: Iniciar Servidor

```bash
php -S localhost:8000
```

## Paso 6: Acceder al Sistema

- **Frontend**: http://localhost:8000
- **Admin**: http://localhost:8000/admin
- **Login**: http://localhost:8000/login.php

### Credenciales por Defecto

- Usuario: `admin`
- Contraseña: `admin123`

**⚠️ IMPORTANTE**: Cambia la contraseña después de la primera sesión.

## Solución de Problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo
- Verifica las credenciales en `config/database.php`
- Asegúrate de que la base de datos `blog_db` exista

### Error 404 en las páginas
- Verifica que mod_rewrite esté habilitado en Apache
- O usa el servidor PHP incorporado: `php -S localhost:8000`

### Imágenes no se muestran
- Verifica que las rutas en `config/config.php` sean correctas
- Verifica que el directorio `uploads/` exista y tenga permisos

## Próximos Pasos

1. Cambia la contraseña del administrador
2. Crea tus primeras categorías
3. Publica tu primer post
4. Personaliza el diseño editando `css/style.css`


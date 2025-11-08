# UniStock - Sistema de Gestión de Inventario

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

## 📋 Descripción

**UniStock** es una aplicación web moderna desarrollada con Laravel para la gestión integral de inventarios. Diseñada para pequeñas y medianas empresas, ofrece una solución completa para controlar productos, categorías, proveedores y movimientos de stock.

## ✨ Características Principales

### 🔧 Módulos Principales
- **Gestión de Productos**: CRUD completo de productos con imágenes
- **Control de Categorías**: Organización jerárquica de productos
- **Administración de Proveedores**: Registro y seguimiento de proveedores
- **Movimientos de Inventario**: Entradas, salidas y ajustes de stock
- **Sistema de Usuarios**: Roles y permisos de acceso

### 📊 Funcionalidades Avanzadas
- **Alertas de Stock**: Notificaciones de productos con bajo inventario
- **Reportes y Estadísticas**: Dashboard con métricas clave
- **Búsqueda Avanzada**: Filtros múltiples y búsqueda en tiempo real
- **Backup de Datos**: Exportación e importación de información
- **Interfaz Responsive**: Compatible con dispositivos móviles

## 🚀 Tecnologías Utilizadas

### Backend
- **Laravel 10+** - Framework PHP
- **MySQL** - Base de datos
- **Eloquent ORM** - Mapeo objeto-relacional
- **Authentication** - Sistema de autenticación integrado

### Frontend
- **Bootstrap 5** - Framework CSS
- **JavaScript** - Interactividad
- **jQuery** - Manipulación DOM
- **Chart.js** - Gráficos y estadísticas

### Herramientas de Desarrollo
- **Composer** - Gestión de dependencias
- **Artisan** - CLI de Laravel
- **Blade** - Motor de plantillas

## 📦 Instalación

### Requisitos Previos
- PHP 8.1 o superior
- Composer
- MySQL 5.7+
- Node.js y NPM (opcional para assets)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tuusuario/unistock.git
   cd unistock
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar base de datos**
   ```bash
   # Editar .env con tus credenciales de BD
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=unistock
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_password
   ```

5. **Ejecutar migraciones**
   ```bash
   php artisan migrate --seed
   ```

6. **Configurar almacenamiento**
   ```bash
   php artisan storage:link
   ```

7. **Instalar assets (opcional)**
   ```bash
   npm install
   npm run build
   ```

8. **Servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## 👤 Acceso por Defecto

Después de ejecutar los seeders, puedes acceder con:
- **Email**: admin@unistock.com
- **Contraseña**: password

## 🗂️ Estructura del Proyecto

```
unistock/
├── app/
│   ├── Models/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   └── assets/
├── public/
│   └── storage/
└── config/
```

## 🔐 Roles y Permisos

- **Administrador**: Acceso completo al sistema
- **Gestor**: Gestión de productos y categorías
- **Operador**: Solo movimientos de inventario
- **Consulta**: Solo visualización de reportes

## 📈 Características del Dashboard

- **Resumen general** de inventario
- **Productos más vendidos**
- **Alertas de stock bajo**
- **Gráficos de movimientos**
- **Métricas clave** del negocio

## 🛠️ Comandos Artisan Útiles

```bash
# Crear backup de la base de datos
php artisan backup:run

# Generar reportes
php artisan reports:generate

# Limpiar cache
php artisan optimize:clear

# Ejecutar tests
php artisan test
```

## 🌐 API Endpoints

UniStock incluye una API RESTful para integraciones:

```
GET    /api/products
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
DELETE /api/products/{id}

GET    /api/categories
GET    /api/inventory-movements
```

## 🔔 Configuración de Alertas

Puedes configurar los umbrales de alerta en el archivo de configuración:

```php
// config/inventory.php
'low_stock_threshold' => 10,
'critical_stock_threshold' => 5,
```

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Si encuentras algún problema o tienes preguntas:

- 📧 Email: soporte@unistock.com
- 🐛 Issues: [GitHub Issues](https://github.com/tuusuario/unistock/issues)
- 📚 Documentación: [Wiki del Proyecto](https://github.com/tuusuario/unistock/wiki)

## 🔄 Changelog

### v1.0.0
- ✅ Gestión completa de productos
- ✅ Sistema de categorías
- ✅ Control de proveedores
- ✅ Movimientos de inventario
- ✅ Dashboard con reportes

---

**Desarrollado con ❤️ usando Laravel**

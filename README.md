# UniStock - Sistema de Gestión de Inventario

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)



**UniStock** 

## Tecnologías Utilizadas

### Backend
- **Laravel 10+** - Framework PHP
- **MySQL** - Base de datos
- **Eloquent ORM** - Mapeo objeto-relacional
- **Authentication** - Sistema de autenticación integrado

### Herramientas de Desarrollo
- **Composer** - Gestión de dependencias
- **Artisan** - CLI de Laravel
- **Blade** - Motor de plantillas

## Instalación

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






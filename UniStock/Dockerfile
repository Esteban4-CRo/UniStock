FROM php:8.2-apache

# Actualizar e instalar dependencias del sistema y Node.js (necesario para Vite)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Limpiar caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias para Laravel y PostgreSQL (Supabase)
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar el contenido del proyecto
COPY . /var/www/html

# Instalar dependencias de PHP y Node.js, y compilar assets
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install
RUN npm run build
RUN php artisan storage:link

# Configurar Apache para que apunte a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilitar mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# Ajustar permisos para que Apache pueda escribir en storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ajustar los puertos de Apache para que escuchen en el $PORT asignado por Render
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

# Iniciar Apache
CMD ["apache2-foreground"]

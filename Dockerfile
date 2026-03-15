FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip nodejs npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory to Laravel project
WORKDIR /var/www/html

# Copy Laravel project
COPY UniStock/ .

# Copy Apache config
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Storage link
RUN php artisan storage:link || true

EXPOSE 80

# Startup: cache config then launch Apache
CMD bash -c "php artisan config:cache && php artisan route:cache && apache2-foreground"

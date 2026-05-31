FROM php:8.2-apache

# Install system dependencies + Node.js for Vite
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip nodejs npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite and headers
RUN a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the actual Laravel project (inside UniStock/ subfolder)
COPY UniStock/ .

# Configure Apache VirtualHost properly for Laravel
RUN echo '<VirtualHost *>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Create .env from example for build-time steps
RUN cp .env.example .env

# Install PHP deps WITHOUT running post-install scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Build frontend assets
RUN npm install && npm run build

# Generate placeholder key
RUN php artisan key:generate

# Storage symlink
RUN php artisan storage:link || true

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Startup script: dynamically set Apache port, clear caches (avoids 404), run migrations, start apache
CMD bash -c "sed -i \"s/Listen 80/Listen \${PORT:-80}/\" /etc/apache2/ports.conf && php artisan config:clear && php artisan route:clear && php artisan migrate --force && apache2-foreground"

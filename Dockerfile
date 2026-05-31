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

# Apache virtual host: AllowOverride All so .htaccess works, dynamic $PORT for Render
RUN printf '<VirtualHost *:${PORT}>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Make Apache listen on the dynamic $PORT that Render assigns
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf

# Create .env from example for build-time steps
RUN cp .env.example .env

# Install PHP deps WITHOUT running post-install scripts (avoids artisan at build time)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Build frontend assets
RUN npm install && npm run build

# Generate a placeholder app key (Render overrides with APP_KEY env var at runtime)
RUN php artisan key:generate

# Storage symlink
RUN php artisan storage:link || true

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# At startup: cache config+routes with REAL env vars from Render, run migrations, then launch Apache
CMD bash -c "php artisan config:cache && php artisan route:cache && php artisan migrate --force && apache2-foreground"

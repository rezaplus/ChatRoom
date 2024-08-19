# Use an official PHP image with the necessary extensions for Laravel
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    libsqlite3-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo_sqlite

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents to the working directory
COPY . .

# Install application dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the start.sh script
COPY dockerfiles/start.sh /usr/local/bin/start.sh

# Set file permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod +x /usr/local/bin/start.sh

# Expose port 9000 and set the entry point
EXPOSE 9000
ENTRYPOINT ["start.sh"]
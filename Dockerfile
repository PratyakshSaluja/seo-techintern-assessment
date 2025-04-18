# Stage 1: Build dependencies with Composer
FROM composer:2 as builder

WORKDIR /app

# Copy only necessary files to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-plugins --no-scripts --prefer-dist

# Copy the rest of the application code
COPY . .

# Ensure autoload is optimized
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Stage 2: Setup the final image with Apache and PHP
FROM php:8.1-apache

# Install system dependencies required by PHP extensions
# Use Debian Frontent noninteractive to avoid prompts
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd intl mysqli zip opcache pdo pdo_mysql

# Get recommended PHP ini settings for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application code and vendor directory from builder stage
COPY --from=builder /app .

# Ensure the 'writable' directory exists and is writable by Apache
# Also set ownership for the entire application directory
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chmod -R 775 writable \
    && chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Apache starts automatically by the base image, no CMD needed unless overriding

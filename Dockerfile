FROM php:8.3-apache

# Update base OS packages and install system dependencies required for GD, ZIP, and MySQL extensions
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip

# Enable Apache rewrite and headers modules
RUN a2enmod rewrite headers

# Copy application files to Apache root
COPY . /var/www/html/

# Set correct ownership and permissions for Apache and uploads
RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# Configure Apache DocumentRoot and AllowOverride All
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]

FROM php:8.3-apache-bookworm

# Update base OS security patches and install required dependencies
RUN apt-get update && apt-get dist-upgrade -y && apt-get install -y --no-install-recommends \
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
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Copy and configure dynamic port entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]

# Stage 1: Build & Security
FROM php:8.2-apache

# Security: Disable Apache signature and set server tokens
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Install system dependencies for PostgreSQL and GD
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Enable Apache modules
RUN a2enmod rewrite headers

# Update DocumentRoot to point to 'public' folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/expose_php = On/expose_php = Off/' "$PHP_INI_DIR/php.ini"

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Security: Set proper ownership and permissions
# Apache runs as www-data by default
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]

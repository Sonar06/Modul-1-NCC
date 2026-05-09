# ==============================
# STAGE 1 — Builder
# ==============================
FROM php:8.2-cli AS builder

WORKDIR /app

# Copy semua source code
COPY . .

# ==============================
# STAGE 2 — Runtime
# ==============================
FROM php:8.2-apache

# Install extension MySQL + curl
RUN apt-get update && apt-get install -y \
    curl \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set document root ke folder public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update Apache config
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Allow .htaccess override
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

# Copy hasil build dari stage builder
COPY --from=builder /app /var/www/html/

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
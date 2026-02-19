FROM php:8.1-apache

# Abilita mod_rewrite per Apache
RUN a2enmod rewrite headers

# Installa dipendenze di sistema e estensioni PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mysqli \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# DocumentRoot punta alla root del progetto (dove si trova index.php)
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Configura Apache VirtualHost per AllowOverride (necessario per .htaccess)
RUN sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' \
        /etc/apache2/sites-available/000-default.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
        /etc/apache2/apache2.conf

# PHP config
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Copia il progetto
COPY . /var/www/html/

# Installa le dipendenze Composer
RUN composer install --no-interaction --optimize-autoloader

# Permessi corretti
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

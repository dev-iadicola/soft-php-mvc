# ==========================
#  PHP 8.4 + Composer (NO SQLite)
# ==========================
FROM php:8.4-cli

# Installa le dipendenze necessarie
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install pdo \
    && rm -rf /var/lib/apt/lists/*

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia tutto il progetto
COPY . .

# Installa le dipendenze PHP
RUN composer install --no-interaction --prefer-dist

# Mostra i moduli caricati (debug)
RUN php -m | grep -E "pdo" || true

# Comando di default
CMD ["vendor/bin/phpunit", "--testdox", "--colors=always"]
